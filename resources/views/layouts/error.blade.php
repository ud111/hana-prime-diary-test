{{--
    エラーページ専用の軽いレイアウト。
    通常のレイアウトは @auth などでセッションや DB に触るため、DB 障害時の 500 でも確実に描画できるよう依存を持たせない
--}}
<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex min-h-full flex-col bg-background font-sans text-on-surface antialiased">
    <header class="border-b border-outline-variant/30 bg-surface-lowest/90">
        <div class="mx-auto flex h-16 max-w-5xl items-center px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-on-surface">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-base font-bold text-on-primary shadow-sm">1</span>
                <span class="text-base font-bold tracking-tight">{{ config('app.name') }}</span>
            </a>
        </div>
    </header>
    <main class="mx-auto flex w-full max-w-5xl flex-1 flex-col items-center px-4 py-16 sm:px-6">
        <section class="card flex w-full max-w-md flex-col items-center gap-4 py-10 text-center sm:p-10">
            <p class="text-5xl font-bold tracking-tight text-outline-variant">@yield('code')</p>
            <h1 class="text-xl font-bold tracking-tight">@yield('title')</h1>
            <p class="text-sm text-on-surface-variant">@yield('message')</p>
            <a href="{{ url('/') }}" class="btn-tonal mt-2">一覧へ戻る</a>
        </section>
    </main>
</body>
</html>
