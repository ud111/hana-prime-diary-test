<?php

namespace Tests\Feature;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryListTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_empty_message_when_no_diaries(): void
    {
        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee('まだ日記がありません');
    }

    public function test_paginates_five_per_page(): void
    {
        // 6 件あれば 1 ページ目に 5 件、2 ページ目に 1 件
        Diary::factory()->count(6)->create();

        $page1 = $this->get(route('diaries.index'))->assertOk();
        $this->assertCount(5, $page1->viewData('diaries'));
        $page1->assertSee('次へ');

        $page2 = $this->get(route('diaries.index', ['page' => 2]))->assertOk();
        $this->assertCount(1, $page2->viewData('diaries'));
    }

    public function test_orders_by_date_desc_then_id_desc(): void
    {
        $old = Diary::factory()->create(['diary_date' => '2026-09-01', 'content' => '古い日記']);
        $newFirst = Diary::factory()->create(['diary_date' => '2026-09-03', 'content' => '同じ日の先']);
        $newLast = Diary::factory()->create(['diary_date' => '2026-09-03', 'content' => '同じ日の後']);

        $response = $this->get(route('diaries.index'))->assertOk();

        // 新しい日付が先、同じ日付なら後から登録したものが先
        $this->assertSame(
            [$newLast->id, $newFirst->id, $old->id],
            $response->viewData('diaries')->pluck('id')->all()
        );
        $response->assertSeeInOrder(['同じ日の後', '同じ日の先', '古い日記']);
    }

    public function test_shows_image_when_attached(): void
    {
        Diary::factory()->withImage('diaries/sample.jpg')->create();

        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee('/storage/diaries/sample.jpg');
    }

    public function test_escapes_content(): void
    {
        Diary::factory()->create(['content' => '<script>alert(1)</script>']);

        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
