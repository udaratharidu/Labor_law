<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Services\BackendApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->query('session');

        $session = $sessionId
            ? ChatSession::where('session_id', $sessionId)->where('user_id', $userId)->first()
            : ChatSession::where('user_id', $userId)->latest('id')->first();

        $chats = $session
            ? $session->chats()->orderBy('created_at')->take(100)->get()
            : collect();

        return view('chat.index', [
            'chats'   => $chats,
            'history' => $this->getHistory($userId),
            'session' => $session,
        ]);
    }

    public function create(): \Illuminate\View\View
    {
        return view('chat.index', [
            'chats'   => collect(),
            'history' => $this->getHistory(Auth::id()),
            'session' => null,
        ]);
    }

    public function store(Request $request, BackendApiClient $apiClient)
    {
        $validated = $request->validate([
            'message'    => ['required', 'string', 'max:3000'],
            'session_id' => ['nullable', 'string'],
        ]);

        $userId = Auth::id();

        $session = null;
        if (! empty($validated['session_id'])) {
            $session = ChatSession::where('session_id', $validated['session_id'])
                ->where('user_id', $userId)
                ->first();
        }

        if (! $session) {
            $newSessionId = $userId.'_'.now()->format('YmdHisu');
            $session = ChatSession::create([
                'session_id' => $newSessionId,
                'user_id'    => $userId,
                'chat_title' => mb_substr($validated['message'], 0, 60),
            ]);
        }

        $aiResponse = $apiClient->sendChatMessage($validated['message'], $userId, $session->session_id);

        if ($request->expectsJson()) {
            return response()->json([
                'session_id'   => $session->session_id,
                'user_message' => $validated['message'],
                'ai_response'  => $aiResponse,
            ]);
        }

        return redirect()->route('chat.index', ['session' => $session->session_id]);
    }

    private function getHistory(?int $userId)
    {
        if (! $userId) {
            return collect();
        }

        return ChatSession::where('user_id', $userId)
            ->latest('id')
            ->take(50)
            ->get(['id', 'session_id', 'chat_title', 'created_at']);
    }
}
