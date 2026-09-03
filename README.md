# 1行日記サイト「ひとこと開発日誌」

[![CI](https://github.com/ud111/hana-prime-diary-test/actions/workflows/ci.yml/badge.svg)](https://github.com/ud111/hana-prime-diary-test/actions/workflows/ci.yml)

Laravel で作った 1 行日記サイトです。開発者が 1 日 1 行だけ書く日誌、というテーマで作りました。

- 一覧（5 件ごとのページネーション、画像のサムネイル）、詳細（大きな画像、シェア、前後の日記）
- 新規投稿・編集（日付・本文・jpg 画像 1 枚、画像の差替と削除、その場でプレビュー）、削除（確認ダイアログ付き）
- ログイン（一覧と詳細は公開、投稿・編集・削除は持ち主のみ）
- 画像は保存時に WebP / AVIF を生成して軽く配信。各ページに OGP と構造化データ、`robots.txt` と `sitemap.xml`

## 動作環境

| 項目 | バージョン | 選定理由 |
|---|---|---|
| PHP | 8.5.10 | 公式 Docker イメージの最新安定版 |
| MySQL | 26.7.0（Innovation） | `mysql:latest` が指す最新リリース。LTS 9.7 でも動作確認済み |
| Laravel | 13.x | 最新のメジャーバージョン |

すべて Docker Compose で動きます。ホストに PHP・MySQL・Node を入れる必要はありません。

## 起動方法

```bash
git clone https://github.com/ud111/hana-prime-diary-test.git
cd hana-prime-diary-test
docker compose up -d --build
docker compose exec app composer run setup   # 初回のみ: composer install / .env 作成 / key:generate / migrate / storage:link
docker compose exec app php artisan db:seed  # 持ち主アカウントと、このサイトを作った開発の記録 14 件 (画像付き)
```

http://localhost:8081 を開いてください。投稿・編集・削除は次のアカウントでログインしてください（ローカル確認用の値です。公開環境では必ず変更してください）。

| メールアドレス | パスワード |
|---|---|
| admin@example.com | password |

詳しい手順とつまずきやすい点は [docs/SETUP.md](docs/SETUP.md) にあります。

## ドキュメント

| ドキュメント | 内容 |
|---|---|
| [docs/SETUP.md](docs/SETUP.md) | 環境構築と運用（テスト、CSS のビルド、画像の軽量版、つまずきやすい点） |
| [docs/SPEC.md](docs/SPEC.md) | 仕様（画面、データモデルと ER 図、バリデーション、画像、ログイン、SEO） |
| [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md) | 開発の進め方（Issue と PR の流れ、テスト、CSS、Claude Code の使い方） |
| [docs/DECISIONS.md](docs/DECISIONS.md) | 判断の記録（バージョン選定、未記載仕様の判断、途中で変えたこと、起きた問題） |

## AI ツールの利用について

- **AI ツールの使用有無**: あり
- **使用したAIツールの名称**: Claude Code（Anthropic）、Laravel Boost（Laravel 公式の MCP サーバー。ドキュメント検索と DB スキーマ参照に使用）、Google Stitch（画面デザインの作成。MCP 経由で Claude Code から取得）、Gemini（シーダーに同梱した画像の生成）
- **どのように利用したか（使用範囲）**: 設計計画書の作成、Docker 環境の構築、コードとテストの実装、PR のレビュー下書きの作成（読み取り専用のサブエージェントで差分を検証）、ドキュメント作成に Claude Code を対話的に使いました。デザインは Google Stitch で作成し、細部は本人が調整しました。仕様の判断、Issue の起票、PR の作成とレビュー指摘の採否、マージ、動作確認は本人が行っています。Claude Code の設定（規約、フック、スキル）はリポジトリの `CLAUDE.md` と `.claude/` に置いています。
