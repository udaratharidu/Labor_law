<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chats');

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->text('user_message');
            $table->longText('ai_response');
            // stores the same session_id string as chat_sessions.session_id
            $table->string('chat_session_id', 191)->index();
            $table->foreign('chat_session_id')
                ->references('session_id')
                ->on('chat_sessions')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 191)->index();
            $table->text('message');
            $table->longText('response');
            $table->timestamps();
        });
    }
};
