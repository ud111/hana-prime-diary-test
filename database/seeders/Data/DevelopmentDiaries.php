<?php

namespace Database\Seeders\Data;

/**
 * シーダーで投入する「このサイトを作った開発の記録」。
 * 本文は 100 文字以内・改行なし。image は database/seeders/images/ 内のファイル名 (無ければ null)
 */
class DevelopmentDiaries
{
    /**
     * @return list<array{date: string, content: string, image: string|null}>
     */
    public static function all(): array
    {
        return [
            ['date' => '2026-08-21', 'content' => '課題を受け取って設計計画書を書いた。仕様に無いところは自分で決めると腹をくくる。', 'image' => '01-planning.jpg'],
            ['date' => '2026-08-22', 'content' => 'Docker で PHP 8.5 と MySQL 26.7 を立てた。OPcache が組み込み済みでビルドが一度こけた。', 'image' => '02-docker.jpg'],
            ['date' => '2026-08-23', 'content' => '初回コミットの前に手を止めた。コミット時刻が開発開始とみなされるかもしれないから。', 'image' => null],
            ['date' => '2026-08-24', 'content' => 'GitHub Actions で Pint とテストが初めて緑になった。バッジが誇らしい。', 'image' => '03-ci-green.jpg'],
            ['date' => '2026-08-25', 'content' => 'テストが開発用 DB に向いていてシードが消えた。接続先を _test に限定するガードを入れた。', 'image' => '04-test-db-guard.jpg'],
            ['date' => '2026-08-26', 'content' => '一覧は 5 件ごとにページ送り。範囲外のページでも「日記がない」と言わないように直した。', 'image' => null],
            ['date' => '2026-08-27', 'content' => '画像は拡張子だけでなく実ファイルの MIME も見る。PNG を .jpg に改名して弾かれるのを確認。', 'image' => '05-upload-validation.jpg'],
            ['date' => '2026-08-28', 'content' => '編集で画像を差し替えるときは、DB が保存できてから古いファイルを消す順序にした。', 'image' => null],
            ['date' => '2026-08-29', 'content' => '削除は確認ダイアログ付き。画像ファイルはレコードが消えてから消す。', 'image' => null],
            ['date' => '2026-08-30', 'content' => '一覧は公開のまま、投稿・編集・削除にログインを付けた。試行は 1 分 5 回まで。', 'image' => '06-login.jpg'],
            ['date' => '2026-08-31', 'content' => 'Stitch のデザインを MCP で取り込んで Tailwind で組んだ。仕様に無いタグや検索は置かない。', 'image' => '07-design.jpg'],
            ['date' => '2026-09-01', 'content' => 'robots.txt が nginx 経由だと 404 だった。テストは nginx を通らないので気づけない種類の問題。', 'image' => '08-robots.jpg'],
            ['date' => '2026-09-02', 'content' => 'JPEG から WebP と AVIF を保存時に生成。679KB の写真が 1200 幅で 82KB になった。', 'image' => '09-image-compression.jpg'],
            ['date' => '2026-09-03', 'content' => '選んだ画像をその場でプレビューできるようにした。あとはドキュメントを整えて提出する。', 'image' => '10-finish.jpg'],
        ];
    }
}
