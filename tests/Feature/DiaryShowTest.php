<?php

namespace Tests\Feature;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_diary_to_guests_without_write_links(): void
    {
        $diary = Diary::factory()->create(['diary_date' => '2026-09-03', 'content' => '詳細ページの本文']);

        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertSee('2026年9月3日（木曜日）')
            ->assertSee('詳細ページの本文')
            ->assertSee('一覧へ戻る')
            ->assertDontSee(route('diaries.edit', $diary))
            ->assertDontSee('name="_method" value="DELETE"', false);
    }

    public function test_shows_image_when_attached(): void
    {
        $diary = Diary::factory()->withImage('diaries/sample.jpg')->create();

        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertSee('/storage/diaries/sample.jpg');
    }

    public function test_owner_sees_write_links(): void
    {
        $diary = Diary::factory()->create();
        $this->actingAs(User::factory()->create());

        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertSee(route('diaries.edit', $diary))
            ->assertSee('name="_method" value="DELETE"', false);
    }

    public function test_list_links_to_detail(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('diaries.index'))->assertOk()->assertSee(route('diaries.show', $diary));
    }

    public function test_escapes_content(): void
    {
        $diary = Diary::factory()->create(['content' => '<script>alert(1)</script>']);

        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_returns_404_for_missing_diary(): void
    {
        $this->get(route('diaries.show', 9999))->assertNotFound()->assertSee('ページが見つかりません');
    }
}
