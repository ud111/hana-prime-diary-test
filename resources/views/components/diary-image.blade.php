{{--
    日記の画像。軽量版 (AVIF / WebP) があれば <picture> で出し、無ければ元の JPEG だけを出す。
    $width は使う軽量版の幅 (480 か 1200)。width / height 属性は元画像の縦横比から計算する
--}}
@props(['diary', 'width' => 480, 'alt' => '', 'loading' => 'lazy', 'fetchpriority' => null])
@php($w = $diary->image_width ? min($width, $diary->image_width) : null)
@php($h = $diary->imageHeightFor($width))
@if ($diary->hasImageVariants())
    <picture>
        {{-- 生成時に作れた形式だけを出す (無い形式の source を出すと対応ブラウザで画像が壊れる) --}}
        @foreach (array_intersect(['avif', 'webp'], $diary->image_formats ?? []) as $format)
            <source type="image/{{ $format }}" srcset="{{ $diary->imageVariantUrl($width, $format) }}">
        @endforeach
        <img {{ $attributes }} src="{{ $diary->image_url }}" alt="{{ $alt }}" width="{{ $w }}" height="{{ $h }}" loading="{{ $loading }}" @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif decoding="async">
    </picture>
@else
    {{-- 軽量版が無い (導入前の) 画像。寸法が分からないので width / height は出さない --}}
    <img {{ $attributes }} src="{{ $diary->image_url }}" alt="{{ $alt }}" loading="{{ $loading }}" @if ($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif decoding="async">
@endif
