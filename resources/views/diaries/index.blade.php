@extends('layouts.app')

@section('title', '日記一覧 | '.config('app.name'))

@section('content')
    <div class="page-header">
        <h2>日記一覧</h2>
        @auth
            <a class="button" href="{{ route('diaries.create') }}">新規投稿</a>
        @endauth
    </div>

    {{-- 範囲外のページでも「日記がない」と誤解させないよう、総件数で判定する --}}
    @if ($diaries->total() === 0)
        <p class="empty">まだ日記がありません。</p>
    @else
        <ul class="diary-list">
            @foreach ($diaries as $diary)
                @include('diaries._item', ['diary' => $diary])
            @endforeach
        </ul>

        {{ $diaries->links() }}
    @endif
@endsection
