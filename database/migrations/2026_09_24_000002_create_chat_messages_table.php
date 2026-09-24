<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The chat audit trail. A user turn carries the client's
     * `client_message_id`; the unique index on (session, client_message_id) is
     * what makes a retried send replay the stored reply instead of running
     * twice. Bot turns point back at the user turn through `reply_to_id`.
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chat_session_id');
            $table->string('role', 10);
            $table->uuid('client_message_id')->nullable();
            $table->uuid('reply_to_id')->nullable()->index();
            $table->string('intent', 40)->nullable();
            $table->string('engine', 10)->nullable();
            $table->text('text')->nullable();
            $table->json('action')->nullable();
            $table->json('blocks')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->unique(['chat_session_id', 'client_message_id']);
            $table->foreign('chat_session_id')->references('id')->on('chat_sessions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
