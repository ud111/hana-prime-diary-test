<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

// 一覧はトップページ (/) で表示する。サービスとしてトップ URL に中身があるようにするため
Route::get('/', [DiaryController::class, 'index'])->name('diaries.index');

// /diaries は一覧の旧 URL。トップへ恒久リダイレクトする
Route::permanentRedirect('/diaries', '/');

// 新規投稿 (#6)・編集 (#7)・削除 (#8) は各 Issue で Route::resource の only() に追加していく
