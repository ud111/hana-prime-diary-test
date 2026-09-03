<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 生成できた軽量版の形式 (例: ["avif","webp"]) を持たせる。
     * 生成時の環境に AVIF が無かった画像に AVIF の <source> を出さないようにするため
     */
    public function up(): void
    {
        Schema::table('diaries', function (Blueprint $table) {
            $table->json('image_formats')->nullable()->after('image_height')->comment('生成済みの軽量版の形式 (JSON 配列)');
        });
    }

    public function down(): void
    {
        Schema::table('diaries', function (Blueprint $table) {
            $table->dropColumn('image_formats');
        });
    }
};
