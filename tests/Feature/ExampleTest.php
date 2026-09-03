<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_top_page_redirects_to_diary_list(): void
    {
        $this->get('/')->assertRedirect('/diaries');
    }
}
