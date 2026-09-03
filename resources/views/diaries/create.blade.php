@extends('layouts.app')

@section('title', '新規投稿 | '.config('app.name'))

@push('head')
    <x-seo :title="'新規投稿 | '.config('app.name')" description="日記の新規投稿" :noindex="true"/>
@endpush

@section('content')
    <div class="flex flex-col gap-1.5">
        <x-breadcrumbs :items="[['label' => '日誌一覧', 'url' => route('diaries.index')], ['label' => '新規投稿']]"/>
        <h1 class="text-[22px] font-bold tracking-tight">日記を書く</h1>
    </div>

    <form method="POST" action="{{ route('diaries.store') }}" enctype="multipart/form-data" class="card flex flex-col gap-6 p-5 sm:p-8">
        @csrf
        @include('diaries._form', ['diary' => null])
        <div class="flex flex-col-reverse gap-3 border-t border-outline-variant pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('diaries.index') }}" class="btn-secondary">キャンセル</a>
            <button type="submit" class="btn-primary px-6">投稿する</button>
        </div>
    </form>
@endsection
