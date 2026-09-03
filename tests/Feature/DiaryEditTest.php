<?php

namespace Tests\Feature;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiaryEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsOwner();
        Storage::fake(Diary::IMAGE_DISK);
    }

    /** 実際に JPEG として読めるフィクスチャ */
    private function jpeg(string $name = 'photo.jpg'): UploadedFile
    {
        return new UploadedFile(base_path('tests/fixtures/sample.jpg'), $name, 'image/jpeg', null, true);
    }

    /** 画像ファイル付きの日記を用意する (ファイルも fake ディスクに置く) */
    private function diaryWithImage(): Diary
    {
        $path = Diary::IMAGE_DIR.'/existing.jpg';
        Storage::disk(Diary::IMAGE_DISK)->put($path, file_get_contents(base_path('tests/fixtures/sample.jpg')));

        return Diary::factory()->withImage($path)->create();
    }

    public function test_edit_page_shows_current_values_and_image(): void
    {
        $diary = $this->diaryWithImage();
        $diary->update(['diary_date' => '2026-09-01', 'content' => '編集前の本文']);

        $this->get(route('diaries.edit', $diary))
            ->assertOk()
            ->assertSee('value="2026-09-01"', false)
            ->assertSee('編集前の本文')
            ->assertSee('/storage/diaries/existing.jpg')
            ->assertSee('画像を削除する');
    }

    public function test_edit_page_without_image_has_no_remove_checkbox(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('diaries.edit', $diary))
            ->assertOk()
            ->assertDontSee('画像を削除する');
    }

    public function test_updates_date_and_content_and_keeps_image(): void
    {
        $diary = $this->diaryWithImage();

        $this->put(route('diaries.update', $diary), [
            'diary_date' => '2026-09-02',
            'content' => '編集後の本文',
        ])->assertRedirect(route('diaries.index'))
            ->assertSessionHas('status', '更新しました。');

        $diary->refresh();
        $this->assertSame('2026-09-02', $diary->diary_date->toDateString());
        $this->assertSame('編集後の本文', $diary->content);
        // 画像は触っていないのでそのまま
        $this->assertSame(Diary::IMAGE_DIR.'/existing.jpg', $diary->image_path);
        Storage::disk(Diary::IMAGE_DISK)->assertExists($diary->image_path);
    }

    public function test_replaces_image_and_deletes_old_file(): void
    {
        $diary = $this->diaryWithImage();

        $this->put(route('diaries.update', $diary), [
            'diary_date' => '2026-09-02',
            'content' => '画像を差し替えた',
            'image' => $this->jpeg('new.jpg'),
        ])->assertRedirect(route('diaries.index'));

        $diary->refresh();
        $this->assertNotSame(Diary::IMAGE_DIR.'/existing.jpg', $diary->image_path);
        Storage::disk(Diary::IMAGE_DISK)->assertExists($diary->image_path);
        // 旧ファイルは消えている
        Storage::disk(Diary::IMAGE_DISK)->assertMissing(Diary::IMAGE_DIR.'/existing.jpg');
    }

    public function test_removes_image_when_checked(): void
    {
        $diary = $this->diaryWithImage();

        $this->put(route('diaries.update', $diary), [
            'diary_date' => '2026-09-02',
            'content' => '画像を消した',
            'remove_image' => '1',
        ])->assertRedirect(route('diaries.index'));

        $this->assertNull($diary->refresh()->image_path);
        Storage::disk(Diary::IMAGE_DISK)->assertMissing(Diary::IMAGE_DIR.'/existing.jpg');
    }

    public function test_new_image_wins_over_remove_checkbox(): void
    {
        $diary = $this->diaryWithImage();

        $this->put(route('diaries.update', $diary), [
            'diary_date' => '2026-09-02',
            'content' => '両方指定',
            'image' => $this->jpeg('new.jpg'),
            'remove_image' => '1',
        ])->assertRedirect(route('diaries.index'));

        $diary->refresh();
        $this->assertNotNull($diary->image_path);
        Storage::disk(Diary::IMAGE_DISK)->assertExists($diary->image_path);
    }

    public function test_validation_errors_keep_existing_data(): void
    {
        $diary = $this->diaryWithImage();
        // キャスト前の生の値で比べる (Carbon はインスタンスが変わると同値でも identical にならない)
        $before = Arr::only($diary->refresh()->getAttributes(), ['diary_date', 'content', 'image_path']);

        $this->from(route('diaries.edit', $diary))
            ->put(route('diaries.update', $diary), [
                'diary_date' => '2026-09-02',
                'content' => str_repeat('あ', 101),
                'remove_image' => '1',
            ])->assertRedirect(route('diaries.edit', $diary))
            ->assertSessionHasErrors('content');

        // 失敗したときは本文も画像もそのまま
        $this->assertSame($before, Arr::only($diary->refresh()->getAttributes(), ['diary_date', 'content', 'image_path']));
        Storage::disk(Diary::IMAGE_DISK)->assertExists(Diary::IMAGE_DIR.'/existing.jpg');
    }

    public function test_returns_404_for_missing_diary(): void
    {
        $this->get(route('diaries.edit', 9999))->assertNotFound();
        $this->put(route('diaries.update', 9999), [
            'diary_date' => '2026-09-02',
            'content' => 'なし',
        ])->assertNotFound();
    }
}
