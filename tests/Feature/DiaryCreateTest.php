<?php

namespace Tests\Feature;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiaryCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsOwner();
    }

    /** 妥当な投稿データ */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'diary_date' => '2026-09-03',
            'content' => 'N+1 を 1 か所つぶした。',
        ], $overrides);
    }

    /** 実際に JPEG として読めるフィクスチャ (mimes / mimetypes の検査を通すため) */
    private function jpeg(string $name = 'photo.jpg'): UploadedFile
    {
        return new UploadedFile(base_path('tests/fixtures/sample.jpg'), $name, 'image/jpeg', null, true);
    }

    public function test_create_page_shows_form_with_today_as_default(): void
    {
        $this->travelTo('2026-09-03 10:00:00');

        $this->get(route('diaries.create'))
            ->assertOk()
            ->assertSee('value="2026-09-03"', false)
            ->assertSee('maxlength="100"', false);
    }

    public function test_stores_diary_without_image(): void
    {
        $response = $this->post(route('diaries.store'), $this->validData());

        $response->assertRedirect(route('diaries.index'))
            ->assertSessionHas('status', '投稿しました。');
        $this->assertDatabaseHas('diaries', [
            'diary_date' => '2026-09-03',
            'content' => 'N+1 を 1 か所つぶした。',
            'image_path' => null,
        ]);
    }

    public function test_stores_diary_with_jpeg_image(): void
    {
        Storage::fake(Diary::IMAGE_DISK);

        $this->post(route('diaries.store'), $this->validData(['image' => $this->jpeg()]))
            ->assertRedirect(route('diaries.index'));

        $diary = Diary::sole();
        // diaries/<ULID>.jpg に保存され、ファイルが実在する
        $this->assertMatchesRegularExpression('#\Adiaries/[0-9A-Z]{26}\.jpg\z#', $diary->image_path);
        Storage::disk(Diary::IMAGE_DISK)->assertExists($diary->image_path);
    }

    public function test_rejects_png_even_if_named_jpg(): void
    {
        Storage::fake(Diary::IMAGE_DISK);
        // 中身は PNG、名前だけ .jpg。実 MIME の検査で弾く
        $png = new UploadedFile(base_path('tests/fixtures/sample.png'), 'photo.jpg', 'image/jpeg', null, true);

        $this->post(route('diaries.store'), $this->validData(['image' => $png]))
            ->assertSessionHasErrors('image');
        $this->assertDatabaseCount('diaries', 0);
        $this->assertSame([], Storage::disk(Diary::IMAGE_DISK)->allFiles());
    }

    public function test_rejects_image_larger_than_5mb(): void
    {
        Storage::fake(Diary::IMAGE_DISK);
        $big = UploadedFile::fake()->create('big.jpg', 5121, 'image/jpeg');

        $this->post(route('diaries.store'), $this->validData(['image' => $big]))
            ->assertSessionHasErrors(['image' => '画像は5120KB以下のファイルを選んでください。']);
        $this->assertDatabaseCount('diaries', 0);
    }

    public function test_accepts_image_of_exactly_5mb(): void
    {
        Storage::fake(Diary::IMAGE_DISK);
        // 実 JPEG の末尾にパディングを足して 5120KB ちょうどにする (JPEG 判定は先頭のヘッダで行われる)
        $jpeg = file_get_contents(base_path('tests/fixtures/sample.jpg'));
        $exact = UploadedFile::fake()->createWithContent(
            'exact.jpg',
            $jpeg.str_repeat("\0", 5120 * 1024 - strlen($jpeg))
        );

        $this->post(route('diaries.store'), $this->validData(['image' => $exact]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('diaries', 1);
    }

    public function test_rejects_empty_and_too_long_content(): void
    {
        $this->post(route('diaries.store'), $this->validData(['content' => '']))
            ->assertSessionHasErrors(['content' => '本文は必須です。']);

        $this->post(route('diaries.store'), $this->validData(['content' => str_repeat('あ', 101)]))
            ->assertSessionHasErrors(['content' => '本文は100文字以内で入力してください。']);

        // ちょうど 100 文字は通る
        $this->post(route('diaries.store'), $this->validData(['content' => str_repeat('あ', 100)]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('diaries', 1);
    }

    public function test_rejects_content_with_newline(): void
    {
        $this->post(route('diaries.store'), $this->validData(['content' => "1行目\n2行目"]))
            ->assertSessionHasErrors(['content' => '本文に改行は使えません。']);
        $this->assertDatabaseCount('diaries', 0);
    }

    public function test_rejects_missing_or_invalid_date(): void
    {
        $this->post(route('diaries.store'), $this->validData(['diary_date' => '']))
            ->assertSessionHasErrors(['diary_date' => '日付は必須です。']);

        $this->post(route('diaries.store'), $this->validData(['diary_date' => '2026/09/03']))
            ->assertSessionHasErrors(['diary_date' => '日付の形式が正しくありません。']);
        $this->assertDatabaseCount('diaries', 0);
    }

    public function test_keeps_input_and_shows_errors_on_form(): void
    {
        $this->from(route('diaries.create'))
            ->post(route('diaries.store'), $this->validData(['content' => '']))
            ->assertRedirect(route('diaries.create'));

        // リダイレクト先のフォームに入力値とエラーが出る
        $this->get(route('diaries.create'))
            ->assertSee('value="2026-09-03"', false)
            ->assertSee('本文は必須です。');
    }
}
