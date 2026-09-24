<?php

namespace App\Services\Rfq;

use App\Models\User;

/**
 * The one seam between RFQ / chat code and the global push helpers.
 *
 * `sendNotification()` / `sendAdminNotification()` fire real FCM pushes, so
 * anything that raises an RFQ or a lead goes through this class instead of
 * calling them directly. Tests swap it for a recording fake; the dev database
 * holds real seller and admin device tokens.
 */
class PushNotifier
{
    /** A push (and, when `$save`, a bell row) for one user. */
    public function toUser(User $user, string $title, string $body, string $type = 'notification', array $payload = [], bool $save = false): void
    {
        sendNotification($user, $title, $body, $type, $payload, $save);
    }

    /** The admin bell row plus a push to every admin device. */
    public function toAdmins(string $title, string $body, string $type = 'notification', array $payload = []): void
    {
        sendAdminNotification($title, $body, $type, $payload);
    }
}
