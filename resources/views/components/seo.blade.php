{{--
    ページごとのメタ情報 (description / canonical / robots / OGP / Twitter カード / JSON-LD)。
    絶対 URL は url() から作るので、本番 URL は .env の APP_URL を変えるだけでよい。
    $noindex が true のページ (ログイン・投稿・編集) は検索対象外にし、canonical も出さない
--}}
@props([
    'title' => config('app.name'),
    'description',
    'url' => null,
    'image' => null,
    'type' => 'website',
    'noindex' => false,
    'jsonLd' => [],
])
@php($url ??= url()->current())
@php($image ??= asset('images/ogp.png'))
<meta name="description" content="{{ $description }}">
@if ($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <link rel="canonical" href="{{ $url }}">
@endif
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:locale" content="ja_JP">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
@foreach ($jsonLd as $data)
    {{-- JSON_HEX_TAG で </script> による抜け出しを防ぐ --}}
    <script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endforeach
