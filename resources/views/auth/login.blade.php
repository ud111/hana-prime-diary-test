@extends('layouts.app')

@section('title', 'ログイン | '.config('app.name'))

@section('content')
    <div class="page-header">
        <h2>ログイン</h2>
        <a href="{{ route('diaries.index') }}">一覧へ戻る</a>
    </div>

    <form method="POST" action="{{ route('login') }}" class="diary-form">
        @csrf
        <div class="field">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" required autofocus autocomplete="username" value="{{ old('email') }}">
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div class="field">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="button">ログイン</button>
    </form>
@endsection
