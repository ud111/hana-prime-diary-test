@extends('layouts.app')

@section('title', $diary->diary_date->isoFormat('YYYY年M月D日').' の日記 | '.config('app.name'))

@section('content')
    <div class="page-header">
        <a href="{{ route('diaries.index') }}">一覧へ戻る</a>
        {{-- 編集・削除は持ち主だけ --}}
        @auth
            <div class="diary-actions">
                <a href="{{ route('diaries.edit', $diary) }}">編集</a>
                <form method="POST" action="{{ route('diaries.destroy', $diary) }}"
                      onsubmit="return confirm('この日記を削除します。よろしいですか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="link-button">削除</button>
                </form>
            </div>
        @endauth
    </div>

    <article class="diary-detail">
        <h2><time datetime="{{ $diary->diary_date->toDateString() }}">{{ $diary->diary_date->isoFormat('YYYY年M月D日(ddd)') }}</time></h2>
        <p class="diary-content">{{ $diary->content }}</p>
        @if ($diary->hasImage())
            <img class="diary-image-large" src="{{ $diary->image_url }}" alt="{{ $diary->diary_date->toDateString() }} の写真">
        @endif
    </article>
@endsection
