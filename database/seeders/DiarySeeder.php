<?php

namespace Database\Seeders;

use App\Models\Diary;
use Illuminate\Database\Seeder;

class DiarySeeder extends Seeder
{
    /**
     * ページネーション (5 件ごと) の確認用に、3 ページ分になる 12 件を投入する
     */
    public function run(): void
    {
        Diary::factory()->count(12)->create();
    }
}
