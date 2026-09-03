<?php

use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

// トップは日記一覧へ
Route::redirect('/', '/diaries');

// 日記の CRUD。詳細ページは課題に無いので show は作らない。
// 新規投稿 (#6)・編集 (#7)・削除 (#8) は各 Issue で only() に追加していく
Route::resource('diaries', DiaryController::class)->only(['index']);
