@extends('layouts.app')

@section('title', '新規投稿 | '.config('app.name'))

@section('content')
    <div class="page-header">
        <h2>新規投稿</h2>
        <a href="{{ route('diaries.index') }}">一覧へ戻る</a>
    </div>

    <form method="POST" action="{{ route('diaries.store') }}" enctype="multipart/form-data" class="diary-form">
        @csrf
        @include('diaries._form', ['diary' => null])
        <button type="submit" class="button">投稿する</button>
    </form>
@endsection
