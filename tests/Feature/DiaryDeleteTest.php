<?php

namespace Tests\Feature;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiaryDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(Diary::IMAGE_DISK);
    }

    public function test_list_has_delete_button_with_confirmation(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee(route('diaries.destroy', $diary))
            ->assertSee('この日記を削除します。よろしいですか？');
    }

    public function test_deletes_diary_and_its_image_file(): void
    {
        $path = Diary::IMAGE_DIR.'/to-delete.jpg';
        Storage::disk(Diary::IMAGE_DISK)->put($path, 'dummy');
        $diary = Diary::factory()->withImage($path)->create();

        $this->delete(route('diaries.destroy', $diary))
            ->assertRedirect(route('diaries.index'))
            ->assertSessionHas('status', '削除しました。');

        $this->assertDatabaseMissing('diaries', ['id' => $diary->id]);
        // レコードと一緒に画像ファイルも消える
        Storage::disk(Diary::IMAGE_DISK)->assertMissing($path);
    }

    public function test_deletes_diary_without_image(): void
    {
        $diary = Diary::factory()->create();
        Diary::factory()->count(2)->create();

        $this->delete(route('diaries.destroy', $diary))->assertRedirect(route('diaries.index'));

        // 他の日記には影響しない
        $this->assertDatabaseMissing('diaries', ['id' => $diary->id]);
        $this->assertDatabaseCount('diaries', 2);
    }

    public function test_does_not_delete_other_diaries_image(): void
    {
        $keep = Diary::IMAGE_DIR.'/keep.jpg';
        Storage::disk(Diary::IMAGE_DISK)->put($keep, 'dummy');
        Diary::factory()->withImage($keep)->create();
        $target = Diary::factory()->create();

        $this->delete(route('diaries.destroy', $target))->assertRedirect(route('diaries.index'));

        Storage::disk(Diary::IMAGE_DISK)->assertExists($keep);
    }

    public function test_returns_404_for_missing_diary(): void
    {
        $this->delete(route('diaries.destroy', 9999))->assertNotFound();
    }
}
