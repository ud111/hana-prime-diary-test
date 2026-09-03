<?php

namespace App\Models;

use Database\Factories\DiaryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * 1行日記
 *
 * @property int $id
 * @property Carbon $diary_date
 * @property string $content
 * @property string|null $image_path
 */
#[Fillable(['diary_date', 'content', 'image_path'])]
class Diary extends Model
{
    /** @use HasFactory<DiaryFactory> */
    use HasFactory;

    /** 本文の最大文字数。バリデーションとフォームの maxlength で共有する */
    public const CONTENT_MAX_LENGTH = 100;

    /** 画像を保存するディスク名と、その中のディレクトリ */
    public const IMAGE_DISK = 'public';

    public const IMAGE_DIR = 'diaries';

    protected function casts(): array
    {
        return [
            // 日付だけを扱うので時刻を持たない date キャストにする
            'diary_date' => 'date',
        ];
    }

    /**
     * 画像の公開 URL。画像がなければ null
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image_path === null
                ? null
                : Storage::disk(self::IMAGE_DISK)->url($this->image_path)
        );
    }

    /**
     * 一覧の並び順: 日付の新しい順、同じ日付なら id の大きい (後から登録した) 順
     *
     * @param  Builder<Diary>  $query
     * @return Builder<Diary>
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('diary_date')->orderByDesc('id');
    }

    /**
     * 画像が添付されているか
     */
    public function hasImage(): bool
    {
        return $this->image_path !== null;
    }
}
