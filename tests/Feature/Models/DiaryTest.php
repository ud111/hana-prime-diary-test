<?php

namespace Tests\Feature\Models;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DiaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_a_valid_diary(): void
    {
        $diary = Diary::factory()->create();

        // 本文は 1 行 (改行なし) で上限文字数以内
        $this->assertStringNotContainsString("\n", $diary->content);
        $this->assertLessThanOrEqual(Diary::CONTENT_MAX_LENGTH, mb_strlen($diary->content));
        $this->assertNull($diary->image_path);
        $this->assertDatabaseCount('diaries', 1);
    }

    public function test_diary_date_is_cast_to_carbon(): void
    {
        $diary = Diary::factory()->create(['diary_date' => '2026-09-03']);

        $this->assertInstanceOf(Carbon::class, $diary->fresh()->diary_date);
        $this->assertSame('2026-09-03', $diary->fresh()->diary_date->toDateString());
    }

    public function test_image_url_is_null_without_image(): void
    {
        $diary = Diary::factory()->create();

        $this->assertFalse($diary->hasImage());
        $this->assertNull($diary->image_url);
    }

    public function test_image_url_points_to_public_disk(): void
    {
        $diary = Diary::factory()->withImage('diaries/sample.jpg')->create();

        $this->assertTrue($diary->hasImage());
        // public ディスクは /storage/... で公開される
        $this->assertStringEndsWith('/storage/diaries/sample.jpg', $diary->image_url);
    }

    public function test_content_max_length_is_100(): void
    {
        $this->assertSame(100, Diary::CONTENT_MAX_LENGTH);
    }
}
