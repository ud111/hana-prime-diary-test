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
            ->assertSee('aria-label="パンくず"', false)
            ->assertSee('日記一覧')
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

    public function test_links_to_older_and_newer_diaries_in_list_order(): void
    {
        $older = Diary::factory()->create(['diary_date' => '2026-09-01', 'content' => '古い日記']);
        $current = Diary::factory()->create(['diary_date' => '2026-09-02', 'content' => '真ん中']);
        $sameDayLater = Diary::factory()->create(['diary_date' => '2026-09-02', 'content' => '同じ日の後']);

        // 同じ日付なら後から登録した方が「新しい」側
        $this->get(route('diaries.show', $current))
            ->assertOk()
            ->assertSee(route('diaries.show', $older))
            ->assertSee(route('diaries.show', $sameDayLater))
            ->assertSee('前の日記')
            ->assertSee('次の日記');

        // 端では案内文が出てリンクは無い
        $this->get(route('diaries.show', $older))
            ->assertSee('これより前の日記はありません')
            ->assertSee(route('diaries.show', $current));
        $this->get(route('diaries.show', $sameDayLater))
            ->assertSee('これより新しい日記はありません');
    }

    public function test_has_share_links(): void
    {
        $diary = Diary::factory()->create();
        $url = route('diaries.show', $diary);

        $this->get($url)
            ->assertOk()
            ->assertSee('twitter.com/intent/tweet', false)
            ->assertSee('facebook.com/sharer', false)
            ->assertSee('social-plugins.line.me', false)
            ->assertSee('data-copy-link="'.$url.'"', false);
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
