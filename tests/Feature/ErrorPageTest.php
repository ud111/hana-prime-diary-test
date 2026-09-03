<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_url_shows_japanese_404(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee('ページが見つかりません')
            ->assertSee('一覧へ戻る');
    }

    public function test_missing_diary_shows_japanese_404_for_owner(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('diaries.edit', 9999))
            ->assertNotFound()
            ->assertSee('ページが見つかりません');
    }

    public function test_419_and_500_views_exist(): void
    {
        // 419 (CSRF 切れ) と 500 はテストで再現しにくいので、ビューが描画できることだけ確かめる
        $this->assertStringContainsString('フォームの有効期限が切れました', view('errors.419')->render());
        $this->assertStringContainsString('エラーが発生しました', view('errors.500')->render());
    }
}
