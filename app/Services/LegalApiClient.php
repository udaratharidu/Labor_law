<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LegalApiClient
{
    private function baseUrl(): string
    {
        return rtrim((string) config('services.legal_api.url'), '/');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>|null
     */
    private function get(string $path, array $query = []): ?array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get($this->baseUrl().'/'.ltrim($path, '/'), $query);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (RequestException|\Throwable $e) {
            Log::warning('Legal API request failed', ['path' => $path, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function listActs(?string $status = null, int $page = 1): ?array
    {
        return $this->get('acts', array_filter([
            'status' => $status,
            'page' => $page,
        ]));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function showAct(int $actId): ?array
    {
        return $this->get("acts/{$actId}");
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getActTree(int $actId): ?array
    {
        return $this->get("acts/{$actId}/tree");
    }
}
