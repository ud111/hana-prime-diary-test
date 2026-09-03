@extends('layouts.app')

@section('title', '新規投稿 | '.config('app.name'))

@section('content')
    <nav class="text-xs text-on-surface-variant" aria-label="現在地">
        <a href="{{ route('diaries.index') }}" class="hover:text-primary">日誌一覧</a>
        <span class="mx-1.5">/</span>
        <span class="text-on-surface">新規投稿</span>
    </nav>

    <section class="card flex flex-col gap-6 sm:p-8">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight">新規日誌の作成</h1>
            <p class="text-sm text-on-surface-variant">今日の学びや気づきを、削ぎ落とした1行の言葉で記録します。</p>
        </div>

        <form method="POST" action="{{ route('diaries.store') }}" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            @include('diaries._form', ['diary' => null])
            <div class="flex flex-col-reverse gap-3 border-t border-outline-variant/30 pt-6 sm:flex-row sm:justify-between">
                <a href="{{ route('diaries.index') }}" class="btn-tonal">キャンセル</a>
                <button type="submit" class="btn-primary px-6">
                    <x-icon name="check-circle" class="h-4 w-4"/>
                    <span>投稿する</span>
                </button>
            </div>
        </form>
    </section>
@endsection
