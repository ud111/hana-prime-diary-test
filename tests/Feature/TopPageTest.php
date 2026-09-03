<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_page_shows_diary_list(): void
    {
        $this->get('/')->assertOk()->assertSee('日記一覧');
    }

    public function test_old_list_url_redirects_to_top(): void
    {
        $this->get('/diaries')->assertRedirect('/')->assertStatus(301);
    }
}
