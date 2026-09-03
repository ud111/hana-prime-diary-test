<?php

namespace Database\Seeders;

use App\Models\Diary;
use App\Services\DiaryImageProcessor;
use Database\Seeders\Data\DevelopmentDiaries;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiarySeeder extends Seeder
{
    /** 同梱している画像の置き場 */
    public const IMAGE_DIR = __DIR__.'/images';

    /**
     * 「このサイトを作った開発の記録」を日記として投入する。
     * 同じ日付・本文の日記が既にあれば作らないので、何度実行しても増えない
     */
    public function run(DiaryImageProcessor $processor): void
    {
        foreach (DevelopmentDiaries::all() as $entry) {
            $diary = Diary::firstOrNew(['diary_date' => $entry['date'], 'content' => $entry['content']]);
            if ($diary->exists) {
                continue;
            }

            // 同梱画像を public ディスクへ ULID 名でコピーし、配信用の軽量版も作る
            if ($entry['image'] !== null) {
                $source = new File(self::IMAGE_DIR.'/'.$entry['image']);
                $diary->image_path = Storage::disk(Diary::IMAGE_DISK)->putFileAs(Diary::IMAGE_DIR, $source, Str::ulid().'.jpg');
                try {
                    $processor->process($diary);
                } catch (\Throwable $e) {
                    // 生成に失敗したらコピーした JPEG を残さない (投稿処理と同じ作法)
                    Diary::deleteImageFile($diary->image_path);
                    throw $e;
                }
            }

            $diary->save();
        }
    }
}
