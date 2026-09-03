@extends('layouts.app')

@section('title', '日記を編集 | '.config('app.name'))

@section('content')
    <div class="page-header">
        <h2>日記を編集</h2>
        <a href="{{ route('diaries.index') }}">一覧へ戻る</a>
    </div>

    <form method="POST" action="{{ route('diaries.update', $diary) }}" enctype="multipart/form-data" class="diary-form">
        @csrf
        @method('PUT')
        @include('diaries._form', ['diary' => $diary])
        <button type="submit" class="button">更新する</button>
    </form>
@endsection
