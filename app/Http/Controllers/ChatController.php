<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\BackendApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = Auth::id();

        $chats = Chat::query()
            ->when($userId, fn ($query) => $query->where('user_id', $userId))
            ->when(! $userId, fn ($query) => $query->whereNull('user_id')->where('session_id', $sessionId))
            ->latest('id')
            ->take(20)
            ->get()
            ->reverse()
            ->values();

        return view('chat.index', [
            'chats' => $chats,
            'history' => $this->getHistory($userId),
            'isNewChat' => $chats->isEmpty(),
            'sessionId' => $sessionId,
        ]);
    }

    public function create(Request $request)
    {
        if (! Auth::check()) {
            $request->session()->regenerate();
        }

        return view('chat.index', [
            'chats' => collect(),
            'history' => $this->getHistory(Auth::id()),
            'isNewChat' => true,
            'sessionId' => $request->session()->getId(),
        ]);
    }

    public function store(Request $request, BackendApiClient $apiClient)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $sessionId = $request->session()->getId();
        $response = $apiClient->sendChatMessage($validated['message'], Auth::id(), $sessionId);

        $chat = Chat::query()->create([
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'message' => $validated['message'],
            'response' => $response,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $chat->id,
                'message' => $chat->message,
                'response' => $chat->response,
            ]);
        }

        return redirect()->route('chat.index');
    }

    private function getHistory(?int $userId)
    {
        if (! $userId) {
            return collect();
        }

        return Chat::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->take(10)
            ->get(['id', 'message']);
    }
}
