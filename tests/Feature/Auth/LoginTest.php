<?php

namespace Tests\Feature\Auth;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['email' => 'owner@example.com', 'password' => 'secret-pass']);
    }

    public function test_login_page_is_shown_to_guests(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('ログイン');
    }

    public function test_login_page_redirects_authenticated_user_to_top(): void
    {
        $this->actingAs($this->owner());

        $this->get(route('login'))->assertRedirect(route('diaries.index'));
    }

    public function test_logs_in_with_valid_credentials(): void
    {
        $user = $this->owner();

        $this->post(route('login'), ['email' => 'owner@example.com', 'password' => 'secret-pass'])
            ->assertRedirect(route('diaries.index'))
            ->assertSessionHas('status', 'ログインしました。');
        $this->assertAuthenticatedAs($user);
    }

    public function test_rejects_wrong_password_without_revealing_which_field(): void
    {
        $this->owner();

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'owner@example.com', 'password' => 'wrong'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
        $this->assertGuest();
    }

    public function test_validates_required_fields(): void
    {
        $this->post(route('login'), ['email' => 'not-an-email', 'password' => ''])
            ->assertSessionHasErrors(['email' => 'メールアドレスの形式が正しくありません。', 'password' => 'パスワードは必須です。']);
    }

    public function test_throttles_after_five_failed_attempts(): void
    {
        $this->owner();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => 'owner@example.com', 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }

        // 6 回目は 429 で弾かれる (正しいパスワードでも)。エラーページは日本語
        $this->post(route('login'), ['email' => 'owner@example.com', 'password' => 'secret-pass'])
            ->assertStatus(429)
            ->assertSee('しばらく時間をおいてください');
        $this->assertGuest();
    }

    public function test_array_email_does_not_crash_rate_limiter(): void
    {
        // email[]=x のような配列でもレートリミッターが落ちず、通常のバリデーションエラーになる
        $this->post(route('login'), ['email' => ['x'], 'password' => 'y'])
            ->assertSessionHasErrors('email');
    }

    public function test_redirects_to_intended_page_after_login(): void
    {
        $this->owner();

        // 未ログインで投稿ページを開くとログインへ転送される
        $this->get(route('diaries.create'))->assertRedirect(route('login'));

        // ログイン後は元のページへ戻る
        $this->post(route('login'), ['email' => 'owner@example.com', 'password' => 'secret-pass'])
            ->assertRedirect(route('diaries.create'));
    }

    public function test_logs_out(): void
    {
        $this->actingAs($this->owner());

        $this->post(route('logout'))
            ->assertRedirect(route('diaries.index'))
            ->assertSessionHas('status', 'ログアウトしました。');
        $this->assertGuest();
    }

    public function test_guests_cannot_reach_write_routes(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('diaries.create'))->assertRedirect(route('login'));
        $this->post(route('diaries.store'), ['diary_date' => '2026-09-03', 'content' => 'x'])->assertRedirect(route('login'));
        $this->get(route('diaries.edit', $diary))->assertRedirect(route('login'));
        $this->put(route('diaries.update', $diary), ['diary_date' => '2026-09-03', 'content' => 'x'])->assertRedirect(route('login'));
        $this->delete(route('diaries.destroy', $diary))->assertRedirect(route('login'));

        // 何も変わっていない
        $this->assertDatabaseCount('diaries', 1);
        $this->assertDatabaseHas('diaries', ['id' => $diary->id, 'content' => $diary->content]);
    }

    public function test_guest_sees_list_without_write_links(): void
    {
        $diary = Diary::factory()->create();

        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee($diary->content)
            ->assertSee('ログイン')
            ->assertDontSee('新規投稿')
            ->assertDontSee(route('diaries.edit', $diary))
            ->assertDontSee('name="_method" value="DELETE"', false);
    }

    public function test_owner_sees_write_links(): void
    {
        $diary = Diary::factory()->create();
        $this->actingAs($this->owner());

        $this->get(route('diaries.index'))
            ->assertOk()
            ->assertSee('ログアウト')
            ->assertSee('新規投稿')
            ->assertSee(route('diaries.edit', $diary))
            ->assertSee('name="_method" value="DELETE"', false);
    }
}
