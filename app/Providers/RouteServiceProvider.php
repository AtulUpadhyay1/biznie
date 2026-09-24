<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // The chat route is public (auth is optional), so the user has to be
        // resolved off the bearer token here rather than via `$request->user()`.
        RateLimiter::for('chat', function (Request $request) {
            $user   = auth('sanctum')->user();
            $limits = config('biznie.chat.rate_limits');

            $limit = $user
                ? Limit::perMinute((int) $limits['user_per_minute'])->by('chat:u:' . $user->getAuthIdentifier())
                : Limit::perMinute((int) $limits['guest_per_minute'])->by('chat:ip:' . $request->ip());

            return $limit->response(function (Request $request, array $headers) {
                $language = in_array($request->input('language'), config('biznie.chat.languages'), true)
                    ? $request->input('language')
                    : 'en';

                return response()->json([
                    'success'     => false,
                    'message'     => __('chat.rate_limited', [], $language),
                    'retry_after' => (int) ($headers['Retry-After'] ?? 60),
                ], 429, $headers);
            });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Biznie v2 API (consumed by biznie-next-app). Additive — does not affect legacy /api routes.
            Route::middleware('api')
                ->prefix('api/v2')
                ->group(base_path('routes/api_v2.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('admin')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));

            Route::prefix('seller')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/seller.php'));
        });
    }
}
