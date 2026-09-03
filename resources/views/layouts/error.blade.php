{{--
    エラーページ専用の軽いレイアウト。
    通常のレイアウトは @auth などでセッションや DB に触るため、DB 障害時の 500 でも確実に描画できるよう依存を持たせない
--}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <h1 class="site-title"><a href="{{ url('/') }}">{{ config('app.name') }}</a></h1>
    </header>
    <main class="container error-page">
        <p class="error-code">@yield('code')</p>
        <h2>@yield('title')</h2>
        <p>@yield('message')</p>
        <p><a href="{{ url('/') }}">一覧へ戻る</a></p>
    </main>
</body>
</html>
