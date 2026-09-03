@extends('layouts.app')

@section('title', '日記を編集 | '.config('app.name'))

@push('head')
    <x-seo :title="'日記を編集 | '.config('app.name')" description="日記の編集" :noindex="true"/>
@endpush

@section('content')
    <div class="flex flex-col gap-1.5">
        <x-breadcrumbs :items="[['label' => '日誌一覧', 'url' => route('diaries.index')], ['label' => $diary->diary_date->isoFormat('YYYY年M月D日').'の日記', 'url' => route('diaries.show', $diary)], ['label' => '編集']]"/>
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
