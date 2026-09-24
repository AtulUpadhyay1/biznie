<?php

namespace App\Services\Chat\Handlers;

use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;

/** Contact details, from the same website setup the legacy `/api/config` serves. */
class SupportHandler
{
    public function __construct(private readonly MenuHandler $menu)
    {
    }

    public function contact(ChatContext $ctx): array
    {
        $options = [];
        if (! $ctx->authenticated()) {
            $options[] = Blocks::option($ctx->t('menu.callback'), Blocks::action('lead.start'));
        }

        return [
            Blocks::text($ctx->t('contact_intro')),
            Blocks::contact(
                self::setting('phone1') ?? self::setting('phone2'),
                self::setting('whatsapp'),
                self::setting('email'),
            ),
            $this->menu->backToMenu($ctx, $options),
        ];
    }

    private static function setting(string $key): ?string
    {
        $value = trim((string) websiteSetupValue($key));

        return $value === '' ? null : $value;
    }
}
