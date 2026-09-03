@extends('layouts.app')

@section('title', $diary->diary_date->isoFormat('YYYY年M月D日').' の日記 | '.config('app.name'))

@section('content')
  <div class="flex flex-col gap-3">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('diaries.index') }}" class="inline-flex items-center gap-1 text-[13px] font-medium text-on-surface-variant hover:text-primary">
            <x-icon name="arrow-left" class="h-3.5 w-3.5"/>
            一覧へ戻る
        </a>
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

    <article class="card flex flex-col gap-6 py-5 sm:py-8 sm:px-12">
        {{-- 日付は控えめに、本文を主役にする --}}
        <header class="flex flex-col gap-2">
            <h1 class="text-sm font-normal text-on-surface-variant">
                <time class="text-gray-500" datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日（dddd）') }}</time>
            </h1>
            <div>
                <p class="text-[24px] font-bold leading-relaxed sm:leading-normal tracking-wide break-all sm:text-[34px] text-gray-900">{{ $diary->content }}</p>
            </div>
        </header>
        @if ($diary->hasImage())
            {{-- 画像が横幅に満たないときは、同じ画像を引き伸ばしてぼかした背景で余白を埋める --}}
            <figure class="relative overflow-hidden rounded-xl border border-outline-variant bg-surface-low">
                <img class="absolute inset-0 h-full w-full scale-110 object-cover opacity-70 blur-2xl" src="{{ $diary->image_url }}" alt="" aria-hidden="true">
                <img class="relative mx-auto block max-h-[32rem] w-auto max-w-full" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真">
            </figure>
        @endif

        {{-- シェア (Stitch のデザイン)。外部スクリプトは読み込まず、各サービスの共有 URL とクリップボードだけで実装する --}}
        @php($shareUrl = route('diaries.show', $diary))
        @php($shareText = $diary->diary_date->isoFormat('YYYY年M月D日').'の日記 | '.config('app.name'))
        <div class="flex flex-col items-center gap-3 border-t border-outline-variant/50 pt-6">
            <span class="text-xs tracking-wider text-outline">この日記をシェアする</span>
            <div class="inline-flex items-center gap-2 rounded-full border border-outline-variant bg-surface-lowest px-3 py-2 shadow-sm">
                <a class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" href="https://twitter.com/intent/tweet?{{ http_build_query(['text' => $shareText, 'url' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer" title="X でシェア" aria-label="X でシェア">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" href="https://www.facebook.com/sharer/sharer.php?{{ http_build_query(['u' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer" title="Facebook でシェア" aria-label="Facebook でシェア">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a class="flex h-9 w-9 items-center justify-center rounded-full text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface" href="https://social-plugins.line.me/lineit/share?{{ http_build_query(['url' => $shareUrl, 'text' => $shareText]) }}" target="_blank" rel="noopener noreferrer" title="LINE でシェア" aria-label="LINE でシェア">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 5.64 2 10.13c0 4.02 3.56 7.39 8.37 8.02.33.07.77.22.88.5.1.25.07.65.03.9l-.14.86c-.04.25-.2 1 .87.54 1.07-.45 5.78-3.4 7.89-5.83C21.36 13.5 22 11.9 22 10.13 22 5.64 17.52 2 12 2zm-3.1 10.6H6.4a.5.5 0 0 1-.5-.5V8.3a.5.5 0 1 1 1 0v3.3h2a.5.5 0 1 1 0 1zm1.9-.5a.5.5 0 1 1-1 0V8.3a.5.5 0 1 1 1 0v3.8zm4.6 0a.5.5 0 0 1-.9.3l-2.1-2.8v2.5a.5.5 0 1 1-1 0V8.3a.5.5 0 0 1 .9-.3l2.1 2.8V8.3a.5.5 0 1 1 1 0v3.8zm3.6-2.4a.5.5 0 1 1 0 1h-2v.9h2a.5.5 0 1 1 0 1h-2.5a.5.5 0 0 1-.5-.5V8.3a.5.5 0 0 1 .5-.5H19a.5.5 0 1 1 0 1h-2v.9h2z"/></svg>
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
    <nav class="grid gap-3 sm:grid-cols-2" aria-label="前後の日記">
        @include('diaries._nav_card', ['target' => $older, 'direction' => 'older'])
        @include('diaries._nav_card', ['target' => $newer, 'direction' => 'newer'])
    </nav>

    {{-- リンクのコピー (無くても共有リンクは使える、補助的な JS) --}}
    <script>
        (function () {
            var button = document.querySelector('[data-copy-link]');
            if (!button || !navigator.clipboard) return;
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
