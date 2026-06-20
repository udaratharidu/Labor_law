<?php

use App\Http\Controllers\Api\ChatApiController;
use App\Http\Middleware\VerifyAiBackendToken;
use Illuminate\Support\Facades\Route;

Route::middleware(VerifyAiBackendToken::class)->prefix('chat')->group(function (): void {
    // Session management
    Route::post('/sessions', [ChatApiController::class, 'createSession']);
    Route::patch('/sessions/{session_id}/title', [ChatApiController::class, 'updateTitle']);

    // AI ask (forward to AI backend)
    Route::post('/sessions/{session_id}/ask', [ChatApiController::class, 'ask']);

    // Chat CRUD
    Route::post('/sessions/{session_id}/chats', [ChatApiController::class, 'createChat']);
    Route::get('/sessions/{session_id}/chats', [ChatApiController::class, 'getChats']);
    Route::delete('/sessions/{session_id}/chats', [ChatApiController::class, 'clearChats']);
    Route::get('/sessions/{session_id}/count', [ChatApiController::class, 'countChats']);

    // Summary
    Route::get('/sessions/{session_id}/summary', [ChatApiController::class, 'getSummary']);
    Route::patch('/sessions/{session_id}/summary', [ChatApiController::class, 'updateSummary']);

    // Mark chats as summarized
    Route::patch('/mark-summarized', [ChatApiController::class, 'markAsSummarized']);

    // History
    Route::get('/history/{user_id}', [ChatApiController::class, 'history']);
});
