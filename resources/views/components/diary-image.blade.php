{{--
    日記の画像。軽量版 (AVIF / WebP) があれば <picture> で出し、無ければ元の JPEG だけを出す。
    $width は使う軽量版の幅 (480 か 1200)。width / height 属性は元画像の縦横比から計算する
--}}
@props(['diary', 'width' => 480, 'alt' => '', 'loading' => 'lazy', 'fetchpriority' => null])
@php($w = $diary->image_width ? min($width, $diary->image_width) : null)
@php($h = $diary->imageHeightFor($width))
@if ($diary->hasImageVariants())
    <picture>
        @foreach (['avif', 'webp'] as $format)
            <source type="image/{{ $format }}" srcset="{{ $diary->imageVariantUrl($width, $format) }}">
        @endforeach
        <img {{ $attributes }} src="{{ $diary->image_url }}" alt="{{ $alt }}" width="{{ $w }}" height="{{ $h }}" loading="{{ $loading }}" @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif decoding="async">
    </picture>
@else
    <img {{ $attributes }} src="{{ $diary->image_url }}" alt="{{ $alt }}" loading="{{ $loading }}" @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif decoding="async">
@endif
