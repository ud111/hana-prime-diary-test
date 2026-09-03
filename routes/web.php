<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

// 一覧はトップページ (/) で表示する。誰でも閲覧できる
Route::get('/', [DiaryController::class, 'index'])->name('diaries.index');

// /diaries は一覧の旧 URL。GET だけをトップへ恒久リダイレクトする (POST /diaries は store が受ける)
Route::get('/diaries', fn () => redirect()->route('diaries.index', status: 301));

// ログイン / ログアウト。ログイン試行は 1 分 5 回まで (AppServiceProvider の login リミッター)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// 日記の作成・編集・削除は持ち主 (ログイン済み) だけ。未ログインは login ルートへ転送される
// 一覧 (index) は上の / で受け、詳細ページ (show) は課題に無いので作らない
Route::resource('diaries', DiaryController::class)
    ->except(['index', 'show'])
    ->middleware('auth');
