@extends('layouts.app')

@php($pageTitle = $diary->diary_date->isoFormat('YYYY年M月D日').' の日記 | '.config('app.name'))
@section('title', $pageTitle)

@php($ogImage = $diary->hasImage() ? $diary->image_url : asset('images/ogp.png'))
@push('head')
    <x-seo
        :title="$pageTitle"
        :description="$diary->content"
        :url="route('diaries.show', $diary)"
        :image="$ogImage"
        type="article"
        :json-ld="[
            [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $diary->content,
                'datePublished' => $diary->diary_date->toDateString(),
                'dateModified' => $diary->updated_at->toAtomString(),
                'image' => [$ogImage],
                'inLanguage' => 'ja',
                'mainEntityOfPage' => route('diaries.show', $diary),
                'author' => ['@type' => 'Person', 'name' => config('app.name').'の持ち主'],
                'publisher' => ['@type' => 'Organization', 'name' => config('app.name')],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => '日誌一覧', 'item' => route('diaries.index')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $diary->diary_date->isoFormat('YYYY年M月D日').'の日記', 'item' => route('diaries.show', $diary)],
                ],
            ],
        ]"
    />
    <meta property="article:published_time" content="{{ $diary->diary_date->toDateString() }}">
    <meta property="article:modified_time" content="{{ $diary->updated_at->toAtomString() }}">
@endpush

@section('content')
  <div class="flex flex-col gap-3">
    <div class="flex items-center justify-between gap-3">
        <x-breadcrumbs :items="[['label' => '日誌一覧', 'url' => route('diaries.index')], ['label' => $diary->diary_date->isoFormat('YYYY年M月D日').'の日記']]"/>
        {{-- 編集・削除は持ち主だけ --}}
        @auth
            <div class="flex items-center gap-1">
                <a href="{{ route('diaries.edit', $diary) }}" class="btn-secondary h-9">
                    <x-icon name="pencil" class="h-4 w-4"/>
                    <span>編集する</span>
                </a>
                @include('diaries._delete_form', ['diary' => $diary, 'iconOnly' => true])
            </div>
        @endauth
    </div>

    <article class="card flex flex-col gap-6 p-5 sm:py-8 sm:px-12">
        {{-- h1 はページの主題である本文。日付は控えめな副題として time で示す --}}
        <header class="flex flex-col gap-2">
            <p class="text-sm text-on-surface-variant">
                <time datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日（dddd）') }}</time>
            </p>
            <h1 class="text-[24px] font-black leading-[1.5] sm:leading-normal tracking-wide break-all sm:text-[34px] text-on-surface">{{ $diary->content }}</h1>
        </header>
        @if ($diary->hasImage())
            {{-- 画像が横幅に満たないときは、同じ画像を引き伸ばしてぼかした背景で余白を埋める --}}
            <figure class="relative overflow-hidden rounded-xl sm:rounded-2xl border border-outline-variant bg-surface-low">
                <x-diary-image :diary="$diary" :width="480" class="absolute inset-0 h-full w-full scale-110 object-cover opacity-70 blur-2xl" alt="" aria-hidden="true"/>
                <x-diary-image :diary="$diary" :width="1200" class="relative mx-auto block max-h-[32rem] w-auto max-w-full" :alt="$diary->diary_date->toDateString().' の写真'" loading="eager" fetchpriority="high"/>
            </figure>
        @endif

        {{-- シェア (Stitch のデザイン)。外部スクリプトは読み込まず、各サービスの共有 URL とクリップボードだけで実装する --}}
        @php($shareUrl = route('diaries.show', $diary))
        @php($shareText = $diary->diary_date->isoFormat('YYYY年M月D日').'の日記 | '.config('app.name'))
        <div class="flex flex-col items-center gap-3 border-t border-outline-variant/50 pt-6">
            <span class="text-xs tracking-wider text-on-surface-variant">この日記をシェアする</span>
            <div class="inline-flex items-center gap-2 rounded-full border border-outline-variant bg-surface-lowest px-3 py-1 sm:py-2 shadow-sm">
                <a class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" href="https://twitter.com/intent/tweet?{{ http_build_query(['text' => $shareText, 'url' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer" title="X でシェア" aria-label="X でシェア">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" href="https://www.facebook.com/sharer/sharer.php?{{ http_build_query(['u' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer" title="Facebook でシェア" aria-label="Facebook でシェア">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" href="https://social-plugins.line.me/lineit/share?{{ http_build_query(['url' => $shareUrl, 'text' => $shareText]) }}" target="_blank" rel="noopener noreferrer" title="LINE でシェア" aria-label="LINE でシェア">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0c4.411 0 8 2.912 8 6.492 0 1.433-.555 2.723-1.715 3.994-1.678 1.932-5.431 4.285-6.285 4.645-.83.35-.734-.197-.696-.413l.003-.018.114-.685c.027-.204.055-.521-.026-.723-.09-.223-.444-.339-.704-.395C2.846 12.39 0 9.701 0 6.492 0 2.912 3.59 0 8 0M5.022 7.686H3.497V4.918a.156.156 0 0 0-.155-.156H2.78a.156.156 0 0 0-.156.156v3.486c0 .041.017.08.044.107v.001l.002.002.002.002a.15.15 0 0 0 .108.043h2.242c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157m.791-2.924a.156.156 0 0 0-.156.156v3.486c0 .086.07.155.156.155h.562c.086 0 .155-.07.155-.155V4.918a.156.156 0 0 0-.155-.156zm3.863 0a.156.156 0 0 0-.156.156v2.07L7.923 4.832l-.013-.015v-.001l-.01-.01-.003-.003-.011-.009h-.001L7.88 4.79l-.003-.002-.005-.003-.008-.005h-.002l-.003-.002-.01-.004-.004-.002-.01-.003h-.002l-.003-.001-.009-.002h-.006l-.003-.001h-.004l-.002-.001h-.574a.156.156 0 0 0-.156.155v3.486c0 .086.07.155.156.155h.56c.087 0 .157-.07.157-.155v-2.07l1.6 2.16a.2.2 0 0 0 .039.038l.001.001.01.006.004.002.008.004.007.003.005.002.01.003h.003a.2.2 0 0 0 .04.006h.56c.087 0 .157-.07.157-.155V4.918a.156.156 0 0 0-.156-.156zm3.815.717v-.56a.156.156 0 0 0-.155-.157h-2.242a.16.16 0 0 0-.108.044h-.001l-.001.002-.002.003a.16.16 0 0 0-.044.107v3.486c0 .041.017.08.044.107l.002.003.002.002a.16.16 0 0 0 .108.043h2.242c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157H11.81v-.589h1.525c.086 0 .155-.07.155-.156v-.56a.156.156 0 0 0-.155-.157H11.81v-.589h1.525c.086 0 .155-.07.155-.156Z"/></svg>
                </a>
                <span class="mx-0.5 h-5 w-px bg-surface-container" aria-hidden="true"></span>
                <button type="button" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[13px] font-medium text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" data-copy-link="{{ $shareUrl }}">
                    <x-icon name="link" class="h-4 w-4"/>
                    <span data-copy-label>リンクをコピー</span>
                </button>
            </div>
        </div>
    </article>
  </div>

    {{-- 前後の日記への導線 (一覧と同じ並び。左が古い日記、右が新しい日記) --}}
    <nav class="grid gap-2 sm:gap-3 sm:grid-cols-2" aria-label="前後の日記">
        @include('diaries._nav_card', ['target' => $older, 'direction' => 'older'])
        @include('diaries._nav_card', ['target' => $newer, 'direction' => 'newer'])
    </nav>

    {{-- リンクのコピー (無くても共有リンクは使える、補助的な JS) --}}
    <script>
        (function () {
            var button = document.querySelector('[data-copy-link]');
            if (!button) return;
            // クリップボード API が無い環境 (http の LAN アドレスなど) では、押しても何も起きないボタンを出さない
            if (!navigator.clipboard) { button.hidden = true; return; }
            var label = button.querySelector('[data-copy-label]');
            button.addEventListener('click', function () {
                navigator.clipboard.writeText(button.getAttribute('data-copy-link')).then(function () {
                    label.textContent = 'コピーしました';
                    setTimeout(function () { label.textContent = 'リンクをコピー'; }, 2000);
                });
            });
        })();
    </script>
@endsection
