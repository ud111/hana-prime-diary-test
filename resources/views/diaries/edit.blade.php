@extends('layouts.app')

@section('title', '日記を編集 | '.config('app.name'))

@section('content')
    <div class="flex flex-col gap-1.5">
        <a href="{{ route('diaries.show', $diary) }}" class="inline-flex items-center gap-1 self-start text-[13px] font-medium text-on-surface-variant hover:text-primary">
            <x-icon name="arrow-left" class="h-3.5 w-3.5"/>
            {{ $diary->diary_date->isoFormat('YYYY年M月D日') }}の日記へ戻る
        </a>
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-[22px] font-bold tracking-tight">日記を編集する</h1>
            @include('diaries._delete_form', ['diary' => $diary, 'label' => 'この日記を削除'])
        </div>
        <p class="num text-[13px] text-on-surface-variant">投稿 {{ $diary->created_at->isoFormat('YYYY.MM.DD HH:mm') }} ・ 最終更新 {{ $diary->updated_at->isoFormat('YYYY.MM.DD HH:mm') }}</p>
    </div>

    <form method="POST" action="{{ route('diaries.update', $diary) }}" enctype="multipart/form-data" class="card flex flex-col gap-6 p-5 sm:p-8">
        @csrf
        @method('PUT')
        @include('diaries._form', ['diary' => $diary])
        <div class="flex flex-col-reverse gap-3 border-t border-outline-variant pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('diaries.show', $diary) }}" class="btn-secondary">キャンセル</a>
            <button type="submit" class="btn-primary px-6">保存する</button>
        </div>
    </form>
@endsection
