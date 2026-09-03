# 1行日記サイト「ひとこと開発日誌」

[![CI](https://github.com/ud111/hana-prime-diary-test/actions/workflows/ci.yml/badge.svg)](https://github.com/ud111/hana-prime-diary-test/actions/workflows/ci.yml)

Laravel で作った 1 行日記サイトです。開発者が 1 日 1 行だけ書く日誌、というテーマで作りました。

- 一覧（5 件ごとのページネーション、画像のサムネイル表示）
- 新規投稿（日付・本文・jpg 画像 1 枚）
- 編集（画像の差替・削除）
- 削除（確認ダイアログ付き。画像ファイルも削除）
- ログイン（一覧は公開、投稿・編集・削除は持ち主のみ）

## 動作環境

| 項目 | バージョン | 選定理由 |
|---|---|---|
| PHP | 8.5.10 | 公式 Docker イメージの最新安定版 |
| MySQL | 26.7.0（Innovation） | `mysql:latest` が指す最新リリース。LTS 系列の 9.7 でも migrate とテストが通ることを確認済み |
| Laravel | 13.x | 最新のメジャーバージョン |
| Web サーバー | nginx | php-fpm と分離した一般的な構成 |

すべて Docker Compose で動きます。ホストに PHP や MySQL を入れる必要はありません。

## 起動方法

```bash
git clone https://github.com/ud111/hana-prime-diary-test.git
cd hana-prime-diary-test
docker compose up -d --build
docker compose exec app composer run setup   # 初回のみ: composer install / .env 作成 / key:generate / migrate / storage:link
docker compose exec app php artisan db:seed  # 持ち主アカウントとダミーの日記 12 件
```

http://localhost:8081 を開いてください。詳しい手順とつまずきやすい点は [docs/SETUP.md](docs/SETUP.md) にまとめています。

### ログイン

一覧は誰でも見られます。投稿・編集・削除は持ち主だけができます。`db:seed` で次のアカウントが作られます。

| メールアドレス | パスワード |
|---|---|
| admin@example.com | password |

ローカル確認用の値です。公開環境で使う場合は、次のように必ず変更してください。

```bash
docker compose exec app php artisan tinker --execute \
  "App\Models\User::where('email', 'admin@example.com')->first()->update(['password' => '新しいパスワード']);"
```

ログインの試行は、メールアドレスと IP の組み合わせごとに 1 分 5 回までです（成功した回数も含みます）。超えると 1 分間は 429 になります。

## テスト

```bash
docker compose exec app php artisan test        # 46 件
docker compose exec app vendor/bin/pint --test  # コード整形チェック
```

テストはテスト用データベース `diary_test` に対して実行し、開発用 DB のデータには触れません。GitHub Actions でも同じテストを MySQL 26.7.0 のサービスコンテナで実行しています。

## 仕様と判断したこと

課題に記載の無い仕様は次のように決めました。理由と詳細は [docs/DESIGN.md](docs/DESIGN.md) にあります。

- 本文は 100 文字まで・改行なし。日付は入力でき、既定は今日
- 画像は jpg のみ 1 枚、5MB まで。拡張子だけでなく実ファイルの MIME も検査し、ULID のファイル名で `storage/app/public/diaries/` に保存
- 一覧は日付の新しい順（同じ日付は後から登録した順）。トップページ `/` が一覧
- 削除・差替時は DB の保存が成功してから画像ファイルを消し、途中で失敗しても画像を失わない
- 第三者が勝手に変更できないよう、書き込みはログイン必須。ユーザー登録画面は作らず、持ち主 1 人をシーダーで作成
- HTML と CSS は最低限。デザインは別途反映予定

## 開発の進め方

- Issue ごとにブランチを切り、PR を作って Squash マージしています。CI（Pint と PHPUnit）が PR ごとに動きます
- 設計計画書 [docs/DESIGN.md](docs/DESIGN.md) を最初に書き、そこから Issue を起票しました

## AI ツールの利用について

- **AI ツールの使用有無**: あり
- **使用したAIツールの名称**: Claude Code（Anthropic）、Laravel Boost（Laravel 公式の MCP サーバー。ドキュメント検索と DB スキーマ参照に使用）
- **どのように利用したか（使用範囲）**: 設計計画書の作成、Docker 環境の構築、コードとテストの実装、PR のレビュー下書きの作成、ドキュメント作成に Claude Code を対話的に使いました。仕様の判断、Issue の起票、PR の作成とレビュー指摘の採否、マージ、動作確認は本人が行っています。Claude Code の設定（規約、フック、スキル）はリポジトリの `CLAUDE.md` と `.claude/` に置いています。
