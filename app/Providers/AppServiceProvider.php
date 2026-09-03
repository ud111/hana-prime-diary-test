<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ページネーションのリンクは Tailwind に依存しない自前のビューで描画する
        Paginator::defaultView('pagination.default');

        // canonical / OGP / sitemap の絶対 URL は、リクエストの Host ヘッダではなく APP_URL から作る。
        // Host ヘッダは誰でも偽装でき、sitemap のキャッシュに偽のホスト名が残る恐れがあるため
        if ($appUrl = config('app.url')) {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // ログインの総当たり対策: メールアドレスと IP の組み合わせごとに 1 分 5 回まで
        RateLimiter::for('login', function (Request $request) {
            // email[]=... のような配列が来ても落ちないよう、文字列のときだけキーに使う
            $email = $request->input('email');

            return Limit::perMinute(5)->by((is_string($email) ? strtolower($email) : '').'|'.$request->ip());
        });
    }
}
