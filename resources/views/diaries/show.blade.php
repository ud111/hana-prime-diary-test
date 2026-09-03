@extends('layouts.app')

@section('title', $diary->diary_date->isoFormat('YYYY年M月D日').' の日記 | '.config('app.name'))

@section('content')
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('diaries.index') }}" class="btn-tonal">
            <x-icon name="arrow-left" class="h-4 w-4"/>
            <span>一覧へ戻る</span>
        </a>
        {{-- 編集・削除は持ち主だけ --}}
        @auth
            <div class="flex items-center gap-1">
                <a href="{{ route('diaries.edit', $diary) }}" class="btn-tonal">
                    <x-icon name="pencil" class="h-4 w-4"/>
                    <span>編集する</span>
                </a>
                <form method="POST" action="{{ route('diaries.destroy', $diary) }}"
                      onsubmit="return confirm('この日記を削除します。よろしいですか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full text-error transition hover:bg-error-container/60" title="削除" aria-label="削除">
                        <x-icon name="trash" class="h-4 w-4"/>
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <article class="card flex flex-col gap-6 sm:p-8">
        <header class="flex flex-col gap-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-primary">{{ $diary->diary_date->isoFormat('MMMM D, YYYY') }}</p>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                <time datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日（dddd）') }}</time>
            </h1>
        </header>
        <blockquote class="relative rounded-xl bg-surface-low px-6 py-5 sm:px-8">
            <span class="absolute left-3 top-1 text-4xl leading-none text-primary-fixed" aria-hidden="true">“</span>
            <p class="text-lg font-medium leading-relaxed break-all sm:text-xl">{{ $diary->content }}</p>
            <p class="mt-3 text-right text-xs text-on-surface-variant">{{ mb_strlen($diary->content) }} 文字</p>
        </blockquote>
        @if ($diary->hasImage())
            <figure class="overflow-hidden rounded-xl bg-surface-low">
                <img class="mx-auto block max-h-[32rem] w-auto max-w-full" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真">
            </figure>
        @endif
    </article>
@endsection
