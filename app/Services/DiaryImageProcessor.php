<?php

namespace App\Services;

use App\Models\Diary;
use GdImage;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * アップロードされた JPEG から、配信用の軽量版 (WebP / AVIF、幅 480 と 1200) を作る。
 * 元の JPEG はそのまま残し、OGP など互換性が要る場面で使う。
 */
class DiaryImageProcessor
{
    /** 生成する幅 (px)。一覧・前後導線は 480、詳細は 1200 */
    public const WIDTHS = [480, 1200];

    /** 生成する形式。imageavif が無い環境では AVIF を飛ばす */
    public const FORMATS = ['avif', 'webp'];

    private const WEBP_QUALITY = 80;

    private const AVIF_QUALITY = 55;

    /** AVIF のエンコード速度 (0 = 最高画質で遅い 〜 10 = 速い)。6 で 1200px が 0.1 秒台 */
    private const AVIF_SPEED = 6;

    /**
     * 軽量版を生成し、元画像の縦横サイズを日記に持たせる (save は呼び出し側で行う)
     */
    public function process(Diary $diary): void
    {
        if ($diary->image_path === null) {
            return;
        }

        $disk = Storage::disk(Diary::IMAGE_DISK);
        $source = @imagecreatefromjpeg($disk->path($diary->image_path));
        if ($source === false) {
            throw new RuntimeException('画像を読み込めませんでした。');
        }

        $width = imagesx($source);
        $height = imagesy($source);

        foreach (self::WIDTHS as $targetWidth) {
            // 元より大きくはしない (拡大すると容量だけ増える)
            $w = min($targetWidth, $width);
            $h = (int) round($height * $w / $width);
            $resized = imagescale($source, $w, $h, IMG_BICUBIC);
            if ($resized === false) {
                throw new RuntimeException('画像の縮小に失敗しました。');
            }

            foreach (self::availableFormats() as $format) {
                $this->write($resized, $format, $disk->path(self::variantPath($diary->image_path, $targetWidth, $format)));
            }
            imagedestroy($resized);
        }
        imagedestroy($source);

        $diary->image_width = $width;
        $diary->image_height = $height;
    }

    /**
     * 軽量版のパス。元が diaries/X.jpg なら diaries/X-480.webp のような名前
     */
    public static function variantPath(string $originalPath, int $width, string $format): string
    {
        return preg_replace('/\.jpg$/', '', $originalPath)."-{$width}.{$format}";
    }

    /**
     * 元画像に対応する軽量版のパスをすべて返す (削除・差替時に使う)
     *
     * @return list<string>
     */
    public static function variantPaths(string $originalPath): array
    {
        $paths = [];
        foreach (self::WIDTHS as $width) {
            foreach (self::FORMATS as $format) {
                $paths[] = self::variantPath($originalPath, $width, $format);
            }
        }

        return $paths;
    }

    /**
     * この環境で生成できる形式
     *
     * @return list<string>
     */
    public static function availableFormats(): array
    {
        return array_values(array_filter(self::FORMATS, fn (string $f) => function_exists('image'.$f)));
    }

    private function write(GdImage $image, string $format, string $path): void
    {
        $ok = match ($format) {
            'webp' => imagewebp($image, $path, self::WEBP_QUALITY),
            'avif' => imageavif($image, $path, self::AVIF_QUALITY, self::AVIF_SPEED),
            default => false,
        };
        if (! $ok) {
            throw new RuntimeException("{$format} の書き出しに失敗しました。");
        }
    }
}
