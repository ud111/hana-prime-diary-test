<?php

namespace App\Console\Commands;

use App\Models\Diary;
use App\Services\DiaryImageProcessor;
use Illuminate\Console\Command;

class RegenerateDiaryImages extends Command
{
    protected $signature = 'diaries:regenerate-images {--force : 生成済みの日記も作り直す}';

    protected $description = '日記の画像から軽量版 (WebP / AVIF) を生成し直す。軽量化の導入前にアップロードした画像に使う';

    public function handle(DiaryImageProcessor $processor): int
    {
        // image_width が null = 軽量版が未生成 (導入前にアップロードした画像)。--force なら生成済みも作り直す
        $query = Diary::query()->whereNotNull('image_path');
        if (! $this->option('force')) {
            $query->whereNull('image_width');
        }

        $count = 0;
        foreach ($query->cursor() as $diary) {
            try {
                $processor->process($diary);
                $diary->save();
                $count++;
                $this->line("生成: {$diary->image_path}");
            } catch (\Throwable $e) {
                $this->warn("失敗: {$diary->image_path} ({$e->getMessage()})");
            }
        }

        $this->info("{$count} 件の軽量版を生成しました。");

        return self::SUCCESS;
    }
}
