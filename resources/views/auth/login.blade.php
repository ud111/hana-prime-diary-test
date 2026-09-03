@extends('layouts.app')

@section('title', 'ログイン | '.config('app.name'))

@section('content')
    <section class="card m-auto mt-4 flex w-full max-w-sm flex-col gap-6 p-6 sm:p-8">
        <div class="flex flex-col items-center gap-3 text-center">
            <div class="flex flex-col gap-1">
                <h1 class="text-lg font-bold tracking-tight">ログイン</h1>
                <p class="text-[13px] text-on-surface-variant">日記の投稿・編集・削除は持ち主だけができます。</p>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf
            <div>
                <label for="email" class="field-label">メールアドレス</label>
                <input type="email" id="email" name="email" required autofocus autocomplete="username" class="field-input num" value="{{ old('email') }}">
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
            <button type="submit" class="btn-primary mt-1 w-full">ログイン</button>
        </form>
    </section>
@endsection
