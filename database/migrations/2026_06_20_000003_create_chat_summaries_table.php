<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->unique()->constrained('chat_sessions')->cascadeOnDelete();
            $table->text('chat_summary');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_summaries');
    }
};
