<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

// 一覧はトップページ (/) で表示する。サービスとしてトップ URL に中身があるようにするため
Route::get('/', [DiaryController::class, 'index'])->name('diaries.index');

// /diaries は一覧の旧 URL。GET だけをトップへ恒久リダイレクトする (POST /diaries は store が受ける)
Route::get('/diaries', fn () => redirect()->route('diaries.index', status: 301));

// 日記の作成・編集・削除。一覧 (index) は上の / で受け、詳細ページ (show) は課題に無いので作らない
Route::resource('diaries', DiaryController::class)->except(['index', 'show']);
