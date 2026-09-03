<?php

namespace Tests\Feature;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_has_meta_canonical_and_website_json_ld(): void
    {
        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('<link rel="canonical" href="'.route('diaries.index').'">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:image" content="'.asset('images/ogp.png').'">', false)
            ->assertSee('"@type":"WebSite"', false)
            ->assertDontSee('noindex');
    }

    public function test_show_has_blog_posting_json_ld_and_uses_attached_image_for_ogp(): void
    {
        $diary = Diary::factory()->withImage('diaries/sample.jpg')->create(['diary_date' => '2026-09-03', 'content' => 'OGP のテスト']);

        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertSee('<meta name="description" content="OGP のテスト">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:image" content="'.$diary->image_url.'">', false)
            ->assertSee('<link rel="canonical" href="'.route('diaries.show', $diary).'">', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('"headline":"OGP のテスト"', false)
            ->assertSee('"datePublished":"2026-09-03"', false)
            ->assertSee('"@type":"BreadcrumbList"', false);
    }

    public function test_show_without_image_falls_back_to_default_ogp_image(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('diaries.show', $diary))
            ->assertSee('<meta property="og:image" content="'.asset('images/ogp.png').'">', false);
    }

    public function test_json_ld_cannot_break_out_of_script_tag(): void
    {
        $diary = Diary::factory()->create(['content' => '</script><script>alert(1)</script>']);

        // JSON-LD 内では < > が \u003C \u003E にエスケープされ、本文で script を閉じられない
        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertSee('"headline":"\\u003C/script\\u003E\\u003Cscript\\u003Ealert(1)\\u003C/script\\u003E"', false)
            ->assertDontSee('"headline":"</script>', false);
    }

    public function test_private_pages_are_noindex(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('login'))->assertSee('<meta name="robots" content="noindex, nofollow">', false)->assertDontSee('rel="canonical"', false);

        $this->actingAsOwner();
        $this->get(route('diaries.create'))->assertSee('noindex, nofollow');
        $this->get(route('diaries.edit', $diary))->assertSee('noindex, nofollow');
    }

    public function test_absolute_urls_ignore_host_header(): void
    {
        // Host ヘッダを偽装しても canonical / sitemap は APP_URL 基準になる
        $diary = Diary::factory()->create();
        $expected = rtrim(config('app.url'), '/');

        $this->withHeaders(['Host' => 'evil.example'])->get('/diaries/'.$diary->id)
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.$expected.'/diaries/'.$diary->id.'">', false)
            ->assertDontSee('evil.example');

        $this->withHeaders(['Host' => 'evil.example'])->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<loc>'.$expected.'</loc>', false)
            ->assertDontSee('evil.example');
    }

    public function test_index_page_two_has_self_referencing_canonical(): void
    {
        Diary::factory()->count(6)->create();

        $this->get(route('diaries.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('diaries.index').'?page=2">', false);
    }

    public function test_robots_txt(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /login')
            ->assertSee('Disallow: /diaries/*/edit')
            ->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_sitemap_lists_top_and_diaries_and_is_invalidated_on_save(): void
    {
        $first = Diary::factory()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('diaries.index').'</loc>', false)
            ->assertSee('<loc>'.route('diaries.show', $first).'</loc>', false)
            ->assertSee('<lastmod>'.$first->updated_at->toAtomString().'</lastmod>', false);

        // キャッシュされるが、日記を追加すると破棄されて新しい URL が載る
        $this->assertTrue(Cache::has('sitemap.xml'));
        $second = Diary::factory()->create();
        $this->assertFalse(Cache::has('sitemap.xml'));
        $this->get('/sitemap.xml')->assertSee('<loc>'.route('diaries.show', $second).'</loc>', false);
    }
}
