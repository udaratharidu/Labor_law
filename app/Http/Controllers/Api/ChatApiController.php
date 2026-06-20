<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatSession;
use App\Models\ChatSummary;
use App\Services\BackendApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatApiController extends Controller
{
    /**
     * Create a new chat session.
     * POST /api/chat/sessions
     * Body: { user_id }
     */
    public function createSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $sessionId = $validated['user_id'].'_'.now()->format('YmdHisu');

        $session = ChatSession::create([
            'session_id' => $sessionId,
            'user_id'    => $validated['user_id'],
            'chat_title' => null,
        ]);

        return response()->json([
            'id'         => $session->id,
            'session_id' => $session->session_id,
            'chat_title' => $session->chat_title,
            'user_id'    => $session->user_id,
            'created_at' => $session->created_at,
        ], 201);
    }

    /**
     * Update chat session title.
     * PATCH /api/chat/sessions/{session_id}/title
     * Body: { title }
     */
    public function updateTitle(Request $request, string $sessionId): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $session = ChatSession::where('session_id', $sessionId)->firstOrFail();
        $session->update(['chat_title' => $validated['title']]);

        return response()->json([
            'id'         => $session->id,
            'session_id' => $session->session_id,
            'chat_title' => $session->chat_title,
        ]);
    }

    /**
     * Send message to AI and return response.
     * POST /api/chat/sessions/{session_id}/ask
     * Body: { user_message, chat_id? }
     */
    public function ask(Request $request, string $sessionId, BackendApiClient $apiClient): JsonResponse
    {
        $validated = $request->validate([
            'user_message' => ['required', 'string', 'max:3000'],
            'chat_id'      => ['nullable', 'integer', 'exists:chats,id'],
        ]);

        $session = ChatSession::where('session_id', $sessionId)->firstOrFail();

        $aiResponse = $apiClient->sendChatMessage(
            $validated['user_message'],
            $session->user_id,
            $sessionId
        );

        return response()->json([
            'session_id'   => $sessionId,
            'chat_id'      => $validated['chat_id'] ?? null,
            'ai_response'  => $aiResponse,
        ]);
    }

    /**
     * Save a chat entry (user message + AI response) to the chats table.
     * POST /api/chat/sessions/{session_id}/chats
     * Body: { user_message, ai_response }
     */
    public function createChat(Request $request, string $sessionId): JsonResponse
    {
        $validated = $request->validate([
            'user_message' => ['required', 'string'],
            'ai_response'  => ['required', 'string'],
        ]);

        // verify session exists
        ChatSession::where('session_id', $sessionId)->firstOrFail();

        $chat = Chat::create([
            'chat_session_id' => $sessionId,
            'user_message'    => $validated['user_message'],
            'ai_response'     => $validated['ai_response'],
        ]);

        return response()->json([
            'id'              => $chat->id,
            'chat_session_id' => $chat->chat_session_id,
            'user_message'    => $chat->user_message,
            'ai_response'     => $chat->ai_response,
            'created_at'      => $chat->created_at,
        ], 201);
    }

    /**
     * Get chat history (all sessions) for a user.
     * GET /api/chat/history/{user_id}
     */
    public function history(int $userId): JsonResponse
    {
        $sessions = ChatSession::where('user_id', $userId)
            ->latest('id')
            ->get(['id', 'session_id', 'chat_title', 'created_at']);

        return response()->json($sessions);
    }

    /**
     * Get all chats for a session (user_id verified via session ownership).
     * GET /api/chat/sessions/{session_id}/chats?user_id=
     */
    public function getChats(Request $request, string $sessionId): JsonResponse
    {
        //$validated = $request->validate([
        //    'user_id' => ['required', 'integer', 'exists:users,id'],
        //]);

        $session = ChatSession::where('session_id', $sessionId)
            //->where('user_id', $validated['user_id'])
            ->firstOrFail();

        $chats = $session->chats()
            ->where('is_summarized', false)
            ->orderBy('created_at')
            ->get(['id', 'user_message', 'ai_response', 'is_summarized', 'created_at']);

        return response()->json([
            'session_id' => $sessionId,
            'chat_title' => $session->chat_title,
            'chats'      => $chats,
        ]);
    }

    /**
     * Update (or create) the chat summary for a session.
     * PATCH /api/chat/sessions/{session_id}/summary
     * Body: { summary }
     */
    public function updateSummary(Request $request, string $sessionId): JsonResponse
    {
        $validated = $request->validate([
            'summary' => ['required', 'string'],
        ]);

        $session = ChatSession::where('session_id', $sessionId)->firstOrFail();

        $summary = ChatSummary::updateOrCreate(
            ['chat_session_id' => $session->id],
            ['chat_summary'    => $validated['summary']]
        );

        return response()->json([
            'session_id' => $sessionId,
            'summary'    => $summary->chat_summary,
            'updated_at' => $summary->updated_at,
        ]);
    }

    /**
     * Clear all chats for a session.
     * DELETE /api/chat/sessions/{session_id}/chats
     */
    public function clearChats(string $sessionId): JsonResponse
    {
        $session = ChatSession::where('session_id', $sessionId)->firstOrFail();
        $deleted = $session->chats()->delete();

        return response()->json([
            'session_id'   => $sessionId,
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * Count chats for a session.
     * GET /api/chat/sessions/{session_id}/count
     */
    public function countChats(string $sessionId): JsonResponse
    {
        $session = ChatSession::where('session_id', $sessionId)->firstOrFail();
        $count = $session->chats()->where('is_summarized', false)->count();

        return response()->json([
            'session_id' => $sessionId,
            'count'      => $count,
        ]);
    }

    /**
     * Get the summary for a session.
     * GET /api/chat/sessions/{session_id}/summary
     */
    public function getSummary(string $sessionId): JsonResponse
    {
        $session = ChatSession::where('session_id', $sessionId)->firstOrFail();

        $summary = ChatSummary::where('chat_session_id', $session->id)->first();

        return response()->json([
            'session_id' => $sessionId,
            'summary'    => $summary?->chat_summary ?? null,
        ]);
    }

    /**
     * Mark given chat IDs as summarized.
     * PATCH /api/chat/mark-summarized
     * Body: { chat_ids: [1, 2, 3] }
     */
    public function markAsSummarized(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_ids'   => ['required', 'array', 'min:1'],
            'chat_ids.*' => ['integer', 'exists:chats,id'],
        ]);

        $updated = Chat::whereIn('id', $validated['chat_ids'])
            ->update(['is_summarized' => true]);

        return response()->json([
            'marked_count' => $updated,
            'chat_ids'     => $validated['chat_ids'],
        ]);
    }
}
