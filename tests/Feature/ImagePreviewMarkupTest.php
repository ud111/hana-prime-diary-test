<?php

namespace Tests\Feature;

use App\Models\Diary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * プレビューはブラウザの JS で動くため、ここでは JS が使う要素が両画面にそろっていることだけを確かめる
 */
class ImagePreviewMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_has_preview_elements(): void
    {
        $this->actingAsOwner();

        $this->get(route('diaries.create'))
            ->assertOk()
            ->assertSee('data-preview', false)
            ->assertSee('data-preview-clear', false)
            ->assertSee('この画像を添付します');
    }

    public function test_edit_form_marks_current_image_and_remove_checkbox(): void
    {
        $this->actingAsOwner();
        $diary = Diary::factory()->withImage('diaries/sample.jpg')->create();

        $this->get(route('diaries.edit', $diary))
            ->assertOk()
            ->assertSee('data-current-image', false)
            ->assertSee('data-remove-image', false)
            ->assertSee('この画像に差し替えます');
    }
}
