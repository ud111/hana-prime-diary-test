<?php

namespace App\Models;

use Database\Factories\DiaryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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

    protected static function booted(): void
    {
        // レコードの削除が成功してから画像ファイルを消す (先に消すと DB 側の失敗で画像だけ失う)
        static::deleted(function (Diary $diary): void {
            self::deleteImageFile($diary->image_path);
        });
    }

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
     * 一覧の並び (日付の新しい順) で 1 つ前 = より古い日記。無ければ null
     */
    public function older(): ?Diary
    {
        return static::query()
            ->where(function (Builder $q) {
                $q->where('diary_date', '<', $this->diary_date->toDateString())
                    ->orWhere(fn (Builder $q2) => $q2->where('diary_date', $this->diary_date->toDateString())->where('id', '<', $this->id));
            })
            ->latestFirst()
            ->first();
    }

    /**
     * 一覧の並びで 1 つ後 = より新しい日記。無ければ null
     */
    public function newer(): ?Diary
    {
        return static::query()
            ->where(function (Builder $q) {
                $q->where('diary_date', '>', $this->diary_date->toDateString())
                    ->orWhere(fn (Builder $q2) => $q2->where('diary_date', $this->diary_date->toDateString())->where('id', '>', $this->id));
            })
            ->orderBy('diary_date')->orderBy('id')
            ->first();
    }

    /**
     * 画像が添付されているか
     */
    public function hasImage(): bool
    {
        return $this->image_path !== null;
    }

    /**
     * アップロードされた画像を public ディスクに保存し、パスを差し替える (save は呼び出し側で行う)。
     * ファイル名は推測されにくいよう ULID にし、拡張子は検証済みの jpg に固定する。
     * 差替前のファイルはここでは消さない。DB の保存が成功してから deleteImageFile() で消す
     */
    public function attachImage(UploadedFile $file): static
    {
        $path = $file->storeAs(self::IMAGE_DIR, Str::ulid().'.jpg', self::IMAGE_DISK);
        if ($path === false) {
            throw new RuntimeException('画像の保存に失敗しました。');
        }
        $this->image_path = $path;

        return $this;
    }

    /**
     * 画像のパスを外す (save は呼び出し側で行う)。ファイルは DB 保存後に deleteImageFile() で消す
     */
    public function detachImage(): static
    {
        $this->image_path = null;

        return $this;
    }

    /**
     * 画像ファイルを public ディスクから削除する。パスが null なら何もしない
     */
    public static function deleteImageFile(?string $path): void
    {
        if ($path !== null) {
            Storage::disk(self::IMAGE_DISK)->delete($path);
        }
    }
}
