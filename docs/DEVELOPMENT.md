# 開発の進め方

環境構築は [SETUP.md](SETUP.md)、仕様は [SPEC.md](SPEC.md)、判断の記録は [DECISIONS.md](DECISIONS.md)。

## 1. リポジトリ構成

```
.
├── compose.yaml              # web (nginx) / app (php-fpm) / db (MySQL) / node (CSS ビルド専用)
├── docker/                   # Dockerfile、nginx、php.ini、MySQL 初期化 SQL (テスト用 DB)
├── app/                      # Laravel (コントローラ、FormRequest、モデル、画像処理サービス、artisan コマンド)
├── resources/views/          # Blade (layouts / diaries / auth / errors / components / pagination / seo)
├── resources/css/app.css     # Tailwind の入力。出力 public/css/app.css をコミット
├── database/                 # マイグレーション、ファクトリ、シーダー (同梱画像は seeders/images/)
├── tests/Feature/            # Feature テスト (テスト用 DB diary_test)
├── docs/                     # このディレクトリ
├── CLAUDE.md, .claude/       # Claude Code の規約・フック・スキル・サブエージェント
└── .github/workflows/ci.yml  # CI
```

- リポジトリ直下が Laravel アプリで、Docker 関連は `compose.yaml` と `docker/` に集約。共有プロキシなどの前提は無く、`docker compose up -d --build` だけで動く。
- 主要な処理には、何をしているか・なぜかが分かる日本語コメントを付けている。

## 2. 流れ

- Issue 単位で進める。ブランチは `feat/<Issue番号>-<内容>`、PR 本文に `Closes #<番号>`、マージは Squash and merge のみ（main は 1 Issue = 1 コミット、作業コミットは PR 側に残る）。
- コミットメッセージは Conventional Commits（`feat:` `fix:` `docs:` `ci:` `chore:` `style:` `refactor:`）。本文は日本語。
- PR ごとに CI（Pint、PHPUnit、CSS 再ビルドの一致）が動く。PR のレビューは Claude Code が下書きを投稿し、指摘の採否・マージは人が行う。
- 起票した Issue と対応する内容は GitHub の Milestone「v1 提出」「v1 仕上げ」にまとまっている。

## 3. テスト

```bash
docker compose exec app php artisan test        # テスト用 DB diary_test を使う
docker compose exec app vendor/bin/pint --test  # コード整形チェック
```

- テストは `phpunit.xml` の設定でテスト用データベース `diary_test` に対して実行する。`tests/TestCase.php` のガードが `_test` 以外の接続先を拒否するので、開発用 DB のデータを消さない。
- DB 接続情報は `.env` だけに置く。`compose.yaml` の環境変数に `DB_*` を書くと phpunit.xml の設定より優先されてしまう。
- CI では `.env.example` から `.env` を作ってテストする（`php artisan test` が `.env` を読むため）。

## 4. CSS（Tailwind CSS 4）

- 入力は `resources/css/app.css`、出力は `public/css/app.css` で、出力もコミットする（審査者は Node 不要）。
- 編集したらコミット前に `docker compose --profile build run --rm node npm run build` を実行し、`git diff --stat public/css/app.css` で意図した差分だけであることを確認する。CI が再ビルドして一致を検査する。
- 編集しながら自動ビルドするには `npm run watch`（出力は非圧縮なので、コミット前に `build` を通す）。
- 色トークンは `@theme` で定義。主要ボタンと現在ページは濃いグレー、藍色はフォーカスなど状態の差し色だけに使う。

## 5. Claude Code の使い方

- 規約は `CLAUDE.md`。フック（データが消える DB コマンドの拒否、PHP 編集後の Pint）、スキル（`issue-start` / `pr-review` / `check`）、サブエージェント（`reviewer`）は `.claude/` にある。
- Laravel Boost の MCP（`.mcp.json`、app コンテナ内で起動）で Laravel のドキュメント検索や DB スキーマ参照を行う。ガイドライン生成と `.ai/rules` は使わない。
- 画面デザインは Google Stitch で作成し、MCP 経由で HTML とスクリーンショットを取得して Blade に移植した。
- シーダーの画像は Gemini（nano-banana MCP）で生成し、JPEG に変換して同梱した。生成ツールの出力先 `generated_imgs/` はコミットしない。
