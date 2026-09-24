<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/** Enforces the chat log retention window (default 90 days). */
class PruneChatMessages extends Command
{
    protected $signature = 'chat:prune {--days= : Keep this many days (defaults to BIZNIE_CHAT_RETENTION_DAYS)}';

    protected $description = 'Delete Biznie AI chat messages and idle sessions older than the retention window';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('biznie.chat.retention_days', 90));
        if ($days < 1) {
            $this->error('--days must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = Carbon::now()->subDays($days);

        $messages = 0;
        do {
            $deleted = ChatMessage::where('created_at', '<', $cutoff)->limit(1000)->delete();
            $messages += $deleted;
        } while ($deleted > 0);

        // Sessions go once they have been idle past the window; their
        // remaining messages cascade with them.
        $sessions = 0;
        do {
            $deleted = ChatSession::where(function ($q) use ($cutoff) {
                $q->where('last_activity_at', '<', $cutoff)
                    ->orWhere(fn ($q) => $q->whereNull('last_activity_at')->where('created_at', '<', $cutoff));
            })->limit(500)->delete();
            $sessions += $deleted;
        } while ($deleted > 0);

        $this->info("Pruned {$messages} chat messages and {$sessions} chat sessions older than {$days} days.");

        return self::SUCCESS;
    }
}
