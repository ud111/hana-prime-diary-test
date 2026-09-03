<?php

namespace App\Services;

use App\Exceptions\ImageProcessingException;
use App\Models\Diary;
use GdImage;
use Illuminate\Support\Facades\Storage;

/**
 * アップロードされた JPEG から、配信用の軽量版 (WebP / AVIF、幅 480 と 1200) を作る。
 * 元の JPEG はそのまま残し、OGP など互換性が要る場面で使う。
 */
class DiaryImageProcessor
{
    /** 生成する幅 (px)。一覧・前後導線は 480、詳細は 1200 */
    public const WIDTHS = [480, 1200];

    /** 生成する形式。imageavif が無い環境では AVIF を飛ばし、作れた形式を Diary::image_formats に残す */
    public const FORMATS = ['avif', 'webp'];

    /** デコードを許す最大の縦横 (px)。これを超える画像はメモリを使い切るため先に弾く */
    public const MAX_DIMENSION = 6000;

    private const WEBP_QUALITY = 80;

    private const AVIF_QUALITY = 55;

    /** AVIF のエンコード速度 (0 = 最高画質で遅い 〜 10 = 速い)。6 で 1200px が 0.1 秒台 */
    private const AVIF_SPEED = 6;

    /**
     * 軽量版を生成し、元画像の縦横サイズと生成できた形式を日記に持たせる (save は呼び出し側で行う)
     *
     * @throws ImageProcessingException 読み込めない・大きすぎる・書き出せないとき
     */
    public function process(Diary $diary): void
    {
        if ($diary->image_path === null) {
            return;
        }

        $file = Storage::disk(Diary::IMAGE_DISK)->path($diary->image_path);

        // デコード前に寸法だけ読み、巨大な画像でメモリを使い切らないようにする
        $info = @getimagesize($file);
        if ($info === false || $info[2] !== IMAGETYPE_JPEG) {
            throw new ImageProcessingException('画像を読み込めませんでした。別の JPEG ファイルを選んでください。');
        }
        if ($info[0] > self::MAX_DIMENSION || $info[1] > self::MAX_DIMENSION) {
            throw new ImageProcessingException('画像が大きすぎます。縦横それぞれ '.self::MAX_DIMENSION.'px 以内の画像を選んでください。');
        }

        $source = @imagecreatefromjpeg($file);
        if ($source === false) {
            throw new ImageProcessingException('画像を読み込めませんでした。別の JPEG ファイルを選んでください。');
        }

        // スマホの縦写真などは EXIF の向き情報だけで回転しているので、軽量版はピクセルを実際に回して正規化する。
        // 回転は元画像ではなく縮小後の小さい画像に対して行い、大きな画像でメモリを使い切らないようにする
        $orientation = $this->exifOrientation($file);
        $swapped = in_array($orientation, [5, 6, 7, 8], true);
        // 回転後の (利用者が見る向きの) 寸法
        $width = $swapped ? imagesy($source) : imagesx($source);
        $height = $swapped ? imagesx($source) : imagesy($source);
        $formats = self::availableFormats();

        foreach (self::WIDTHS as $targetWidth) {
            // 元より大きくはしない (拡大すると容量だけ増える)
            $w = min($targetWidth, $width);
            $h = (int) round($height * $w / $width);
            // 回転前の元画像を、回転後に w×h になる大きさへ縮小してから向きを直す
            $resized = imagescale($source, $swapped ? $h : $w, $swapped ? $w : $h, IMG_BICUBIC);
            if ($resized === false) {
                throw new ImageProcessingException('画像の縮小に失敗しました。');
            }
            $resized = $this->applyOrientation($resized, $orientation);
            foreach ($formats as $format) {
                $this->write($resized, $format, Storage::disk(Diary::IMAGE_DISK)->path(self::variantPath($diary->image_path, $targetWidth, $format)));
            }
        }

        $diary->image_width = $width;
        $diary->image_height = $height;
        $diary->image_formats = $formats;
    }

    /**
     * 軽量版のパス。元が diaries/X.jpg なら diaries/X-480.webp のような名前
     */
    public static function variantPath(string $originalPath, int $width, string $format): string
    {
        return preg_replace('/\.jpg$/', '', $originalPath)."-{$width}.{$format}";
    }

    /**
     * 元画像に対応しうる軽量版のパスをすべて返す (削除・差替時に使う。無いものは delete が無視する)
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

    /**
     * EXIF の Orientation (1〜8)。読めなければ 1 (そのまま)
     */
    private function exifOrientation(string $file): int
    {
        if (! function_exists('exif_read_data')) {
            return 1;
        }
        $exif = @exif_read_data($file);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        return ($orientation >= 1 && $orientation <= 8) ? $orientation : 1;
    }

    /**
     * Orientation に従って回転・反転し、向きをピクセルに焼き込む
     */
    private function applyOrientation(GdImage $image, int $orientation): GdImage
    {
        // 反転を含む値 (2, 4, 5, 7) はまず左右反転してから回転する
        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }
        $angle = match ($orientation) {
            3, 4 => 180,
            5, 6 => -90,
            7, 8 => 90,
            default => 0,
        };
        if ($angle !== 0) {
            $rotated = imagerotate($image, $angle, 0);
            if ($rotated !== false) {
                $image = $rotated;
            }
        }

        return $image;
    }

    private function write(GdImage $image, string $format, string $path): void
    {
        $ok = match ($format) {
            'webp' => imagewebp($image, $path, self::WEBP_QUALITY),
            'avif' => imageavif($image, $path, self::AVIF_QUALITY, self::AVIF_SPEED),
            default => false,
        };
        if (! $ok) {
            throw new ImageProcessingException('画像の書き出しに失敗しました。');
        }
    }
}
