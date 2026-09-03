<?php

namespace Tests\Feature;

use App\Models\Diary;
use App\Services\DiaryImageProcessor;
use Database\Seeders\Data\DevelopmentDiaries;
use Database\Seeders\DiarySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiarySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_development_diaries_with_images_and_variants(): void
    {
        Storage::fake(Diary::IMAGE_DISK);
        $entries = DevelopmentDiaries::all();

        $this->seed(DiarySeeder::class);

        $this->assertDatabaseCount('diaries', count($entries));
        foreach ($entries as $entry) {
            // 本文は課題の制約 (100 文字以内・改行なし) を守る
            $this->assertLessThanOrEqual(Diary::CONTENT_MAX_LENGTH, mb_strlen($entry['content']));
            $this->assertStringNotContainsString("\n", $entry['content']);
            $this->assertDatabaseHas('diaries', ['diary_date' => $entry['date'], 'content' => $entry['content']]);
        }

        // 画像付きの日記は元の JPEG と軽量版がそろい、寸法も入る
        $withImage = Diary::whereNotNull('image_path')->get();
        $this->assertCount(count(array_filter($entries, fn ($e) => $e['image'] !== null)), $withImage);
        foreach ($withImage as $diary) {
            Storage::disk(Diary::IMAGE_DISK)->assertExists($diary->image_path);
            Storage::disk(Diary::IMAGE_DISK)->assertExists(DiaryImageProcessor::variantPath($diary->image_path, 480, 'webp'));
            $this->assertTrue($diary->hasImageVariants());
        }
    }

    public function test_seeding_twice_does_not_duplicate(): void
    {
        Storage::fake(Diary::IMAGE_DISK);

        $this->seed(DiarySeeder::class);
        $this->seed(DiarySeeder::class);

        $this->assertDatabaseCount('diaries', count(DevelopmentDiaries::all()));
    }
}
