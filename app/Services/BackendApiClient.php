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

        $token = config('services.ai_backend.token');

        $request = Http::timeout(20);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->post(rtrim($endpoint, '/').'/ask', [
            'question' => $message,
        ]);

        $token = config('services.ai_backend.token');

        $request = Http::timeout(60);

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->post(
            rtrim($endpoint, '/').'/sessions/'.rawurlencode($sessionId).'/ask',
            ['question' => $message]
        );

        if ($response->failed()) {
            return 'The AI service is temporarily unavailable.';
        }

        $data = $response->json();

        return (string) ($data['answer'] ?? $data['response'] ?? 'No response payload returned.');
    }
}
