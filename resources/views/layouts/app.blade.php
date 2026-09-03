<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <h1 class="site-title"><a href="{{ route('diaries.index') }}">{{ config('app.name') }}</a></h1>
        <nav class="site-nav">
            {{-- 持ち主だけがログアウトできる。未ログインならログインへ --}}
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="link-button link-button-plain">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}">ログイン</a>
            @endauth
        </nav>
    </header>

    <main class="container">
        {{-- 投稿・更新・削除後のメッセージ (session の status に入れる) --}}
        @if (session('status'))
            <p class="flash" role="status">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
