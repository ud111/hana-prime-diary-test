<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    {{-- 本文は Noto Sans JP、数字と英字は Inter (Google Fonts)。読み込めない環境では system-ui にフォールバック --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex min-h-full flex-col bg-background font-sans text-[15px] leading-relaxed text-on-surface antialiased">
    <header class="sticky top-0 z-50 border-b border-outline-variant bg-surface-lowest/95 backdrop-blur">
        <div class="mx-auto flex h-14 max-w-3xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ route('diaries.index') }}" class="flex items-center gap-2.5 rounded-full text-on-surface focus:outline-none focus-visible:ring-4 focus-visible:ring-primary/20">
                <span class="text-[16px] font-bold tracking-wide text-gray-900">{{ config('app.name') }}</span>
            </a>
            <nav class="flex items-center gap-1.5" aria-label="メインメニュー">
                {{-- 持ち主だけが投稿・ログアウトできる。未ログインならログインへ --}}
                @auth
                    <a href="{{ route('diaries.create') }}" class="btn-primary h-9 px-4">
                        <x-icon name="plus" class="h-4 w-4"/>
                        <span>新規投稿</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-icon" title="ログアウト" aria-label="ログアウト">
                            <x-icon name="logout" class="h-4 w-4"/>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary h-9">
                        <span>ログイン</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-8 px-4 py-3 sm:px-6 sm:py-3">
        {{-- 投稿・更新・削除・ログインのメッセージ (session の status に入れる) --}}
        @if (session('status'))
            <div class="flex items-center gap-3 rounded-xl border border-success/20 bg-success-container/60 px-4 py-3 text-sm font-medium text-success" role="status">
                <x-icon name="check-circle" class="h-5 w-5 shrink-0"/>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-8 text-center text-xs text-on-surface-variant">
        <span class="num">© {{ now()->year }}</span> {{ config('app.name') }}
    </footer>
</body>
</html>
