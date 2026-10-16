<?php

use App\Models\Notification;
use App\Support\NotificationTarget;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B33: notifications say who they are for (buyer / seller / transporter) and
 * where they open. Existing rows are backfilled by inferring the role from the
 * RFQ they name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('role', 20)->nullable()->after('type')->index();
            $table->string('link', 190)->nullable()->after('role');
        });

        Notification::query()->whereNotNull('user_id')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $data = is_array($row->data) ? $row->data : [];
                $target = NotificationTarget::infer(
                    (int) $row->user_id,
                    is_string($data['unique_id'] ?? null) ? $data['unique_id'] : null,
                    $row->title . ' ' . $row->body
                );
                if (! $target['role']) {
                    $target = NotificationTarget::inferFromTitle(
                        (string) $row->title,
                        \App\Models\User::whereKey($row->user_id)->value('type')
                    );
                }
                if ($target['role']) {
                    $row->forceFill($target)->saveQuietly();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['role', 'link']);
        });
    }
};
