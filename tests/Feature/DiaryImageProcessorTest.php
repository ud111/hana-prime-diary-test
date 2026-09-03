<?php

namespace Tests\Feature;

use App\Models\Diary;
use App\Services\DiaryImageProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiaryImageProcessorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(Diary::IMAGE_DISK);
    }

    /** 実際に JPEG として読める 16x16 のフィクスチャ */
    private function jpeg(): UploadedFile
    {
        return new UploadedFile(base_path('tests/fixtures/sample.jpg'), 'photo.jpg', 'image/jpeg', null, true);
    }

    public function test_generates_variants_and_records_dimensions(): void
    {
        $diary = Diary::factory()->make();
        $diary->attachImage($this->jpeg());

        app(DiaryImageProcessor::class)->process($diary);
        $diary->save();

        // 元画像の寸法が入り、生成済みと判定される
        $this->assertSame(16, $diary->image_width);
        $this->assertSame(16, $diary->image_height);
        $this->assertTrue($diary->fresh()->hasImageVariants());

        // 幅ごと・形式ごとの軽量版ができる (この環境で作れる形式だけ)
        foreach (DiaryImageProcessor::WIDTHS as $width) {
            foreach (DiaryImageProcessor::availableFormats() as $format) {
                Storage::disk(Diary::IMAGE_DISK)->assertExists(DiaryImageProcessor::variantPath($diary->image_path, $width, $format));
            }
        }
        $this->assertContains('webp', DiaryImageProcessor::availableFormats());
    }

    public function test_store_generates_variants_and_list_uses_picture(): void
    {
        $this->actingAsOwner();

        $this->post(route('diaries.store'), [
            'diary_date' => '2026-09-03',
            'content' => '軽量版のテスト',
            'image' => $this->jpeg(),
        ])->assertRedirect(route('diaries.index'));

        $diary = Diary::sole();
        $this->assertNotNull($diary->image_width);

        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee('<source type="image/webp" srcset="'.$diary->imageVariantUrl(480, 'webp').'">', false)
            ->assertSee('src="'.$diary->image_url.'"', false);

        // 詳細は 1200 幅を優先読み込み
        $this->get(route('diaries.show', $diary))
            ->assertSee('srcset="'.$diary->imageVariantUrl(1200, 'webp').'"', false)
            ->assertSee('fetchpriority="high"', false);
    }

    public function test_legacy_image_without_variants_falls_back_to_plain_img(): void
    {
        // 軽量化の導入前にアップロードされた画像 (寸法が無い) は <picture> を使わない
        Storage::disk(Diary::IMAGE_DISK)->put('diaries/legacy.jpg', file_get_contents(base_path('tests/fixtures/sample.jpg')));
        $diary = Diary::factory()->withImage('diaries/legacy.jpg')->create();

        $this->get(route('diaries.show', $diary))
            ->assertOk()
            ->assertDontSee('<picture>', false)
            ->assertSee('src="'.$diary->image_url.'"', false);
    }

    public function test_deleting_diary_removes_variants_too(): void
    {
        $this->actingAsOwner();
        $diary = Diary::factory()->make();
        $diary->attachImage($this->jpeg());
        app(DiaryImageProcessor::class)->process($diary);
        $diary->save();
        $variants = DiaryImageProcessor::variantPaths($diary->image_path);

        $this->delete(route('diaries.destroy', $diary))->assertRedirect(route('diaries.index'));

        Storage::disk(Diary::IMAGE_DISK)->assertMissing($diary->image_path);
        foreach ($variants as $path) {
            Storage::disk(Diary::IMAGE_DISK)->assertMissing($path);
        }
    }

    public function test_regenerate_command_processes_legacy_images(): void
    {
        Storage::disk(Diary::IMAGE_DISK)->put('diaries/legacy.jpg', file_get_contents(base_path('tests/fixtures/sample.jpg')));
        $diary = Diary::factory()->withImage('diaries/legacy.jpg')->create();
        $this->assertFalse($diary->hasImageVariants());

        $this->artisan('diaries:regenerate-images')->assertSuccessful();

        $this->assertTrue($diary->fresh()->hasImageVariants());
        Storage::disk(Diary::IMAGE_DISK)->assertExists('diaries/legacy-480.webp');
    }
}
