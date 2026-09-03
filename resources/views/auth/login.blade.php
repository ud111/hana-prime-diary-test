@extends('layouts.app')

@section('title', 'ログイン | '.config('app.name'))

@section('content')
    <section class="card mx-auto mt-6 flex w-full max-w-md flex-col gap-6 sm:p-8">
        <div class="flex flex-col items-center gap-3 text-center">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-xl font-bold text-on-primary shadow-sm">1</span>
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold tracking-tight">{{ config('app.name') }}</h1>
                <p class="text-sm text-on-surface-variant">日々の技術的な気づきや学びを、1行でストック。</p>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf
            <div>
                <label for="email" class="field-label">メールアドレス</label>
                <input type="email" id="email" name="email" required autofocus autocomplete="username" class="field-input" value="{{ old('email') }}">
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="field-label">パスワード</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" class="field-input">
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-primary mt-1 w-full py-2.5">
                <span>ログイン</span>
                <x-icon name="login" class="h-4 w-4"/>
            </button>
        </form>
        <p class="text-center text-xs text-on-surface-variant">投稿・編集・削除は持ち主だけができます。一覧は誰でも見られます。</p>
    </section>
@endsection
