{{--
    エラーページ専用の軽いレイアウト。
    通常のレイアウトは @auth などでセッションや DB に触るため、DB 障害時の 500 でも確実に描画できるよう依存を持たせない
--}}
<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>@yield('title') | {{ config('app.name') }}</title>
    {{-- 長期キャッシュ (nginx) と両立させるため、CSS の更新時刻をクエリに付けて差し替え時に確実に取り直させる --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : '' }}">
</head>
<body class="flex min-h-full flex-col bg-background font-sans text-[15px] leading-relaxed text-on-surface antialiased">
    <header class="border-b border-outline-variant bg-surface-lowest">
        <div class="mx-auto flex h-14 max-w-3xl items-center px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-on-surface">
                <span class="text-[16px] font-bold tracking-wide text-on-surface">{{ config('app.name') }}</span>
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
