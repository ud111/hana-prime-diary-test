<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

// 一覧はトップページ (/) で表示する。サービスとしてトップ URL に中身があるようにするため
Route::get('/', [DiaryController::class, 'index'])->name('diaries.index');

// /diaries は一覧の旧 URL。GET だけをトップへ恒久リダイレクトする (POST /diaries は store が受ける)
Route::get('/diaries', fn () => redirect()->route('diaries.index', status: 301));

// 日記の作成・編集・削除。詳細ページは課題に無いので show は作らない
Route::resource('diaries', DiaryController::class)->except(['show']);
