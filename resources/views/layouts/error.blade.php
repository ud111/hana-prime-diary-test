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
<body class="flex min-h-full flex-col bg-background font-sans text-[15px] leading-relaxed text-on-surface antialiased">
    <header class="border-b border-outline-variant bg-surface-lowest">
        <div class="mx-auto flex h-14 max-w-3xl items-center px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-on-surface">
                <span class="num flex h-7 w-7 items-center justify-center rounded-md bg-primary text-sm font-bold text-on-primary">1</span>
                <span class="text-[15px] font-bold tracking-tight">{{ config('app.name') }}</span>
            </a>
        </div>
    </header>
    <main class="mx-auto flex w-full max-w-3xl flex-1 flex-col items-center px-4 py-16 sm:px-6">
        <section class="card flex w-full max-w-sm flex-col items-center gap-3 p-8 text-center">
            <p class="num text-4xl font-bold tracking-tight text-outline-variant">@yield('code')</p>
            <h1 class="text-lg font-bold tracking-tight">@yield('title')</h1>
            <p class="text-sm text-on-surface-variant">@yield('message')</p>
            <a href="{{ url('/') }}" class="btn-secondary mt-3">一覧へ戻る</a>
        </section>
    </main>
</body>
</html>
