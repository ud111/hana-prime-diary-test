<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    {{-- 本文フォントは Inter + Noto Sans JP (Google Fonts)。読み込めない環境では system-ui にフォールバック --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex min-h-full flex-col bg-background font-sans text-on-surface antialiased">
    <header class="sticky top-0 z-50 border-b border-outline-variant/30 bg-surface-lowest/90 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-5xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ route('diaries.index') }}" class="flex items-center gap-2.5 text-on-surface">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-base font-bold text-on-primary shadow-sm">1</span>
                <span class="text-base font-bold tracking-tight">{{ config('app.name') }}</span>
            </a>
            <nav class="flex items-center gap-2" aria-label="メインメニュー">
                <a href="{{ route('diaries.index') }}" class="hidden rounded-full px-3 py-1.5 text-sm font-semibold text-on-surface hover:bg-surface-low sm:inline-flex">日誌一覧</a>
                {{-- 持ち主だけが投稿・ログアウトできる。未ログインならログインへ --}}
                @auth
                    <a href="{{ route('diaries.create') }}" class="btn-primary">
                        <x-icon name="plus" class="h-4 w-4"/>
                        <span>新規投稿</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full bg-surface-low text-on-surface-variant transition hover:bg-surface-container hover:text-on-surface" title="ログアウト" aria-label="ログアウト">
                            <x-icon name="logout" class="h-4 w-4"/>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-tonal">
                        <x-icon name="login" class="h-4 w-4"/>
                        <span>ログイン</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 px-4 py-8 sm:px-6">
        {{-- 投稿・更新・削除・ログインのメッセージ (session の status に入れる) --}}
        @if (session('status'))
            <div class="card flex items-center gap-3 py-4" role="status">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-success-container text-success">
                    <x-icon name="check-circle" class="h-5 w-5"/>
                </span>
                <p class="text-sm font-semibold">{{ session('status') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="py-8 text-center text-xs text-on-surface-variant">
        © {{ now()->year }} {{ config('app.name') }}
    </footer>
</body>
</html>
