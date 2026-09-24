<?php

namespace App\Services\Chat\Handlers;

use App\Models\CommodityProductOrder;
use App\Models\ProductEnquiry;
use App\Services\Chat\Blocks;
use App\Services\Chat\ChatContext;
use Illuminate\Support\Str;

/** Greeting, main menu and the "log in" entry point. */
class MenuHandler
{
    /** Greeting (personalised when signed in) followed by the main menu. */
    public function bootstrap(ChatContext $ctx): array
    {
        if (! $ctx->authenticated()) {
            return [
                Blocks::text($ctx->t('greeting_guest')),
                $this->mainMenu($ctx),
            ];
        }

        $ownerId = $ctx->ownerId();

        $activeOrders = OrderHandler::whereActive(CommodityProductOrder::where('customer_user_id', $ownerId))->count();

        $liveEnquiries = ProductEnquiry::where('user_id', $ownerId)->liveBidding()->count();

        $name  = $this->firstName((string) $ctx->user->name);
        $lines = [$name !== '' ? $ctx->t('greeting_user', ['name' => $name]) : $ctx->t('greeting_user_anon')];
        if ($activeOrders > 0) {
            $lines[] = $ctx->choice('summary_orders', $activeOrders, ['count' => $activeOrders]);
        }
        if ($liveEnquiries > 0) {
            $lines[] = $ctx->choice('summary_enquiries', $liveEnquiries, ['count' => $liveEnquiries]);
        }
        $lines[] = $ctx->t('menu_prompt');

        return [
            Blocks::text(implode("\n", $lines)),
            $this->mainMenu($ctx),
        ];
    }

    public function menu(ChatContext $ctx): array
    {
        return [
            Blocks::text($ctx->t('menu_prompt')),
            $this->mainMenu($ctx),
        ];
    }

    /**
     * An explicit "Log in" choice for guests. The client shows its inline
     * login card and, on success, replays `bootstrap` for the personal greeting.
     */
    public function login(ChatContext $ctx): array
    {
        if ($ctx->authenticated()) {
            return $this->bootstrap($ctx);
        }

        return [Blocks::loginRequired($ctx->t('login_prompt'), Blocks::action('bootstrap'))];
    }

    public function mainMenu(ChatContext $ctx): array
    {
        $options = [
            Blocks::option($ctx->t('menu.track_order'), Blocks::action('order.list', ['scope' => 'active'])),
            Blocks::option($ctx->t('menu.my_enquiries'), Blocks::action('enquiry.list', ['scope' => 'open'])),
            Blocks::option($ctx->t('menu.create_enquiry'), Blocks::action('enquiry.start')),
            Blocks::option($ctx->t('menu.check_prices'), Blocks::action('product.search')),
            Blocks::option($ctx->t('menu.support'), Blocks::action('support.contact')),
        ];

        if (! $ctx->authenticated()) {
            $options[] = Blocks::option($ctx->t('menu.callback'), Blocks::action('lead.start'));
            $options[] = Blocks::option($ctx->t('menu.login'), Blocks::action('login'));
        }

        return Blocks::quickReplies($options);
    }

    /** Just "Back to menu", for the end of a flow. */
    public function backToMenu(ChatContext $ctx, array $extra = []): array
    {
        return Blocks::quickReplies([
            ...$extra,
            Blocks::option($ctx->t('menu.main_menu'), Blocks::action('menu')),
        ]);
    }

    private function firstName(string $name): string
    {
        return Str::limit(trim(Str::before(trim($name), ' ')), 40, '');
    }
}
