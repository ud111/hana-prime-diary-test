<?php

use Illuminate\Support\Facades\Route;

// トップは日記一覧へ (一覧ルートは Issue #5 で実装)
Route::redirect('/', '/diaries');
