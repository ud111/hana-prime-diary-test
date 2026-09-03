@extends('layouts.app')

@section('title', $diary->diary_date->isoFormat('YYYY年M月D日').' の日記 | '.config('app.name'))

@section('content')
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

    <article class="card flex flex-col gap-6 p-5 sm:p-8">
        <header class="flex flex-col gap-1">
            <h1 class="text-[22px] font-bold tracking-tight sm:text-[26px]">
                <time datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日（dddd）') }}</time>
            </h1>
        </header>
        <div class="border-l-4 border-primary/60 pl-5">
            <p class="text-[19px] font-medium leading-relaxed break-all sm:text-[21px]">{{ $diary->content }}</p>
            <p class="num mt-2 text-[13px] text-on-surface-variant">{{ mb_strlen($diary->content) }} 文字</p>
        </div>
        @if ($diary->hasImage())
            <figure class="overflow-hidden rounded-xl border border-outline-variant bg-surface-low">
                <img class="mx-auto block max-h-[32rem] w-auto max-w-full" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真">
            </figure>
        @endif

        {{-- シェア。外部スクリプトは読み込まず、各サービスの共有 URL とクリップボードだけで実装する --}}
        @php($shareUrl = route('diaries.show', $diary))
        @php($shareText = $diary->diary_date->isoFormat('YYYY年M月D日').'の日記 | '.config('app.name'))
        <div class="flex flex-wrap items-center gap-2 border-t border-outline-variant pt-5" data-share>
            <span class="inline-flex items-center gap-1.5 text-[13px] font-medium text-on-surface-variant"><x-icon name="share" class="h-4 w-4"/>この日記をシェア</span>
            <a class="btn-secondary h-9 px-3.5 text-[13px]" href="https://twitter.com/intent/tweet?{{ http_build_query(['text' => $shareText, 'url' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer">X</a>
            <a class="btn-secondary h-9 px-3.5 text-[13px]" href="https://www.facebook.com/sharer/sharer.php?{{ http_build_query(['u' => $shareUrl]) }}" target="_blank" rel="noopener noreferrer">Facebook</a>
            <a class="btn-secondary h-9 px-3.5 text-[13px]" href="https://social-plugins.line.me/lineit/share?{{ http_build_query(['url' => $shareUrl, 'text' => $shareText]) }}" target="_blank" rel="noopener noreferrer">LINE</a>
            <button type="button" class="btn-secondary h-9 px-3.5 text-[13px]" data-copy-link="{{ $shareUrl }}">
                <x-icon name="link" class="h-4 w-4"/>
                <span data-copy-label>リンクをコピー</span>
            </button>
        </div>
    </article>

    {{-- 前後の日記への導線 (一覧と同じ並び。左が古い日記、右が新しい日記) --}}
    <nav class="grid gap-3 sm:grid-cols-2" aria-label="前後の日記">
        @if ($older)
            <a href="{{ route('diaries.show', $older) }}" class="card flex items-center gap-3 p-4 transition-colors hover:border-outline">
                <x-icon name="chevron-left" class="h-4 w-4 shrink-0 text-on-surface-variant"/>
                <span class="flex min-w-0 flex-col gap-0.5">
                    <span class="text-xs text-on-surface-variant">前の日記</span>
                    <span class="num text-[13px] font-semibold">{{ $older->diary_date->isoFormat('YYYY.MM.DD (ddd)') }}</span>
                    <span class="truncate text-sm">{{ $older->content }}</span>
                </span>
            </a>
        @else
            <span class="card flex items-center gap-3 p-4 text-on-surface-variant" aria-disabled="true">
                <x-icon name="chevron-left" class="h-4 w-4 shrink-0"/>
                <span class="text-sm">これより前の日記はありません</span>
            </span>
        @endif
        @if ($newer)
            <a href="{{ route('diaries.show', $newer) }}" class="card flex items-center justify-end gap-3 p-4 text-right transition-colors hover:border-outline">
                <span class="flex min-w-0 flex-col gap-0.5">
                    <span class="text-xs text-on-surface-variant">次の日記</span>
                    <span class="num text-[13px] font-semibold">{{ $newer->diary_date->isoFormat('YYYY.MM.DD (ddd)') }}</span>
                    <span class="truncate text-sm">{{ $newer->content }}</span>
                </span>
                <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-on-surface-variant"/>
            </a>
        @else
            <span class="card flex items-center justify-end gap-3 p-4 text-right text-on-surface-variant" aria-disabled="true">
                <span class="text-sm">これより新しい日記はありません</span>
                <x-icon name="chevron-right" class="h-4 w-4 shrink-0"/>
            </span>
        @endif
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
