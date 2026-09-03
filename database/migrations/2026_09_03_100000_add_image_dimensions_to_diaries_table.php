<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 画像の縦横サイズを持たせる。width / height 属性の出力 (レイアウトシフト対策) と、
     * 「軽量版 (WebP / AVIF) が生成済みか」の判定に使う (null なら未生成)
     */
    public function up(): void
    {
        Schema::table('diaries', function (Blueprint $table) {
            $table->unsignedInteger('image_width')->nullable()->after('image_path')->comment('元画像の幅 (px)。軽量版生成済みなら非 null');
            $table->unsignedInteger('image_height')->nullable()->after('image_width')->comment('元画像の高さ (px)');
        });
    }

    public function down(): void
    {
        Schema::table('diaries', function (Blueprint $table) {
            $table->dropColumn(['image_width', 'image_height']);
        });
    }
};
