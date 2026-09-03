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
    </article>
@endsection
