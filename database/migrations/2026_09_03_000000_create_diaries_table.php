<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 1行日記のテーブルを作成する
     */
    public function up(): void
    {
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            // 日記の日付。フォームの既定値は今日で、過去日も選べる
            $table->date('diary_date')->comment('日記の日付');
            // 本文。文字数の上限 (100 文字) はアプリ側のバリデーションで担保する
            $table->string('content')->comment('本文 (1行)');
            // 添付画像の保存先。public ディスク上の相対パスで、画像なしの場合は null
            $table->string('image_path')->nullable()->comment('画像の保存パス (public ディスク相対)');
            $table->timestamps();

            // 一覧は日付の新しい順に並べるためのインデックス
            $table->index(['diary_date', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};
