@extends('layouts.app')

@section('title', '日記一覧 | '.config('app.name'))

@section('content')
    {{-- ヒーロー: サイトの趣旨と、持ち主向けの投稿導線 --}}
    <section class="card flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between sm:p-8">
        <div class="flex flex-col gap-2">
            <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-primary">
                <x-icon name="code" class="h-4 w-4"/>
                Daily Dev Reflection
            </p>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">日々の開発の小さな前進を、たった1行に。</h1>
            <p class="text-sm text-on-surface-variant">コミットに残らない小さな閃きや技術的知見を、余白の中に静かに残しましょう。</p>
        </div>
        @auth
            <a href="{{ route('diaries.create') }}" class="btn-primary shrink-0 self-start px-5 py-2.5 sm:self-center">
                <x-icon name="pencil" class="h-4 w-4"/>
                <span>新規日誌を書く</span>
            </a>
        @endauth
    </section>

    <section class="flex flex-col gap-4" aria-label="日記一覧">
        <h2 class="sr-only">日記一覧</h2>
        {{-- 範囲外のページでも「日記がない」と誤解させないよう、総件数で判定する --}}
        @if ($diaries->total() === 0)
            <div class="card flex flex-col items-center gap-2 py-12 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-low text-outline">
                    <x-icon name="image" class="h-6 w-6"/>
                </span>
                <p class="text-sm font-semibold">まだ日記がありません。</p>
                @auth
                    <p class="text-xs text-on-surface-variant">最初の 1 行を書いてみましょう。</p>
                @endauth
            </div>
        @else
            @foreach ($diaries as $diary)
                @include('diaries._item', ['diary' => $diary])
            @endforeach

            {{ $diaries->links() }}
        @endif
    </section>
@endsection
