@extends('layouts.app')

@section('title', '日記を編集 | '.config('app.name'))

@section('content')
    <nav class="text-xs text-on-surface-variant" aria-label="現在地">
        <a href="{{ route('diaries.index') }}" class="hover:text-primary">日誌一覧</a>
        <span class="mx-1.5">/</span>
        <a href="{{ route('diaries.show', $diary) }}" class="hover:text-primary">{{ $diary->diary_date->isoFormat('YYYY年M月D日') }}の日記</a>
        <span class="mx-1.5">/</span>
        <span class="text-on-surface">編集</span>
    </nav>

    <section class="card flex flex-col gap-6 sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="flex items-center gap-2 text-2xl font-bold tracking-tight">
                    <span class="h-2.5 w-2.5 rounded-full bg-primary"></span>
                    開発日誌の編集
                </h1>
                <p class="text-sm text-on-surface-variant">投稿: {{ $diary->created_at->isoFormat('YYYY年M月D日 HH:mm') }} ・ 最終更新: {{ $diary->updated_at->isoFormat('YYYY年M月D日 HH:mm') }}</p>
            </div>
            @include('diaries._delete_form', ['diary' => $diary, 'label' => 'この日記を削除'])
        </div>

        <form method="POST" action="{{ route('diaries.update', $diary) }}" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            @method('PUT')
            @include('diaries._form', ['diary' => $diary])
            <div class="flex flex-col-reverse gap-3 border-t border-outline-variant/30 pt-6 sm:flex-row sm:justify-between">
                <a href="{{ route('diaries.show', $diary) }}" class="btn-tonal">キャンセル</a>
                <button type="submit" class="btn-primary px-6">
                    <x-icon name="check-circle" class="h-4 w-4"/>
                    <span>変更を保存する</span>
                </button>
            </div>
        </form>
    </section>
@endsection
