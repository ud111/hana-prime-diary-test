@extends('layouts.app')

@section('title', '日記一覧 | '.config('app.name'))

@section('content')
    <div class="page-header">
        <h2>日記一覧</h2>
    </div>

    @if ($diaries->isEmpty())
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
