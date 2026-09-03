<?php

namespace Database\Seeders;

use App\Models\Diary;
use Database\Factories\DiaryFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DiarySeeder extends Seeder
{
    /**
     * ページネーション (5 件ごと) の確認用に、3 ページ分になる 12 件を投入する
     */
    public function run(): void
    {
        // 例文が重複しないよう、シャッフルした例文を順番に割り当てる
        $contents = collect(DiaryFactory::SAMPLE_CONTENTS)->shuffle()->take(12)
            ->map(fn (string $content) => ['content' => $content])
            ->all();

        Diary::factory()->count(12)->state(new Sequence(...$contents))->create();
    }
}
