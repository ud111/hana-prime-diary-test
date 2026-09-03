@extends('layouts.app')

@section('title', '日記一覧 | '.config('app.name'))

@section('content')
    {{-- ページの導入。統計や検索など仕様に無いものは置かない --}}
    <section class="flex flex-col gap-1.5">
        <h1 class="text-[22px] font-bold leading-snug tracking-tight sm:text-[26px]">日々の開発の小さな前進を、たった1行に。</h1>
        <p class="text-sm text-on-surface-variant">コミットに残らない気づきや学びを、1日1行だけ書き留めます。</p>
    </section>

    <section class="flex flex-col gap-3" aria-label="日記一覧">
        <h2 class="sr-only">日記一覧</h2>
        {{-- 範囲外のページでも「日記がない」と誤解させないよう、総件数で判定する --}}
        @if ($diaries->total() === 0)
            <div class="card flex flex-col items-center gap-3 px-6 py-14 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-low text-outline">
                    <x-icon name="pencil" class="h-5 w-5"/>
                </span>
                <p class="font-semibold">まだ日記がありません。</p>
                @auth
                    <p class="text-sm text-on-surface-variant">今日の 1 行から始めましょう。</p>
                    <a href="{{ route('diaries.create') }}" class="btn-primary mt-1">日記を書く</a>
                @else
                    <p class="text-sm text-on-surface-variant">持ち主がログインすると書き始められます。</p>
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
