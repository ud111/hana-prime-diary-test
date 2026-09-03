<?php

namespace Database\Factories;

use App\Models\Diary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diary>
 */
class DiaryFactory extends Factory
{
    public function definition(): array
    {
        // 本文は日本語のダミー文 (ja_JP の realText)。改行を含まない 1 行に整えて上限に収める
        $content = mb_substr(
            str_replace(["\r", "\n"], '', fake()->realText(60)),
            0,
            Diary::CONTENT_MAX_LENGTH
        );

        return [
            // 直近 2 か月のどこかの日付。一覧の並び順を確認しやすいようにばらけさせる
            'diary_date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'content' => $content,
            'image_path' => null,
        ];
    }

    /**
     * 画像付きの状態。実ファイルは作らないので、ファイルが必要なテストでは Storage::fake と併用する
     */
    public function withImage(?string $path = null): static
    {
        return $this->state(fn () => [
            'image_path' => $path ?? Diary::IMAGE_DIR.'/'.fake()->uuid().'.jpg',
        ]);
    }
}
