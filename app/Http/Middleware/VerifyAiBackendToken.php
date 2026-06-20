<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAiBackendToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.ai_backend.token');

        if (! $expected) {
            return response()->json(['error' => 'AI backend token not configured.'], 500);
        }

        $incoming = $request->bearerToken() ?? $request->header('X-Api-Token');

        if (! $incoming || ! hash_equals($expected, $incoming)) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
