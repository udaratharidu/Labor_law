<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BackendApiClient
{
    public function sendChatMessage(string $message, ?int $userId, string $sessionId): string
    {
        $endpoint = config('services.ai_backend.url');

        if (! $endpoint) {
            return 'AI backend is not configured yet. This is a placeholder response.';
        }

        $response = Http::timeout(20)->post(rtrim($endpoint, '/').'/chat', [
            'message' => $message,
            'user_id' => $userId,
            'session_id' => $sessionId,
        ]);

        if ($response->failed()) {
            return 'The AI service is temporarily unavailable.';
        }

        return (string) $response->json('response', 'No response payload returned.');
    }
}
