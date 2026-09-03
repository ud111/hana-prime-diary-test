# CLAUDE.md — 1行日記サイト

Laravel 13 / PHP 8.5 / MySQL 26.7 で作る 1 行日記サイト。仕様は `docs/SPEC.md`、開発の進め方は `docs/DEVELOPMENT.md`、判断の記録は `docs/DECISIONS.md`、環境構築は `docs/SETUP.md`。
このファイルは Claude Code がこのリポジトリで作業するときの規約。人が読んでも分かるように書く。

## 開発フロー

- Issue 単位で進める。ブランチは `feat/<Issue番号>-<内容>`、PR 本文に `Closes #<番号>`、マージは Squash and merge のみ。
- ブランチ作成は `/issue-start <番号>`、PR のレビューは `/pr-review <PR番号>`、ローカル確認は `/check` を使う。
- PR 作成とマージは人が行う。Claude は実装・コミット・push・レビューの下書き投稿まで。
- コミットメッセージは Conventional Commits（`feat:` `fix:` `docs:` `ci:` `chore:`）。本文は日本語。

## コードの書き方

- 主要な処理（コントローラのアクション、バリデーション、画像の保存と削除、クエリ）には、何をしているか・なぜかが分かる短い日本語コメントを付ける。自明な行には付けない。
- 整形は Laravel Pint。PHP を編集するとフックが自動で整形する。push 前に `vendor/bin/pint --test` が通ること。
- CSS は Tailwind の CLI ビルド。Blade か `resources/css/app.css` を変えたら、**commit の直前に** `docker compose --profile build run --rm node npm run build` を実行し、`git diff --stat public/css/app.css` で意図した差分だけであることを確認してから入力と出力を一緒に commit する（CI が再ビルドして一致を検査する）。`npm run watch` の出力は非圧縮なので、そのまま commit しない。
- 仕様の判断は `docs/SPEC.md` に従う。本文は 100 文字・改行なし、画像は jpg 1 枚 5MB まで。

## テストと DB

- テストは必ずテスト用 DB `diary_test` で実行する。`tests/TestCase.php` のガードが `_test` 以外の接続先を拒否する。
- 開発用 DB `diary` に対して fresh / reset / refresh / rollback / wipe 系の artisan コマンドは実行しない（フックが止める）。必要なら人が手で実行する。
- DB 接続情報は `.env` だけに置く。`compose.yaml` の環境変数に `DB_*` を書くと phpunit.xml の設定より優先されてしまう。
- CI では `.env.example` から `.env` を作ってテストする（`php artisan test` が `.env` を読むため）。ローカルの `.env` とは無関係。

## Laravel Boost（MCP）

- `.mcp.json` の `laravel-boost` は app コンテナ内で動く（`docker compose exec -T app php artisan boost:mcp`）。コンテナが起動していないと使えない。
- Laravel 13（#9 以降は Tailwind 4 も）の API や書き方に迷ったら、推測せず `search-docs` で確認する。調査には `database-schema`、`read-log-entries`、`last-error` を使う。
- `record-rule` と `.ai/rules` は使わない。規約はこのファイルに書く。Boost が入れたスキル（`laravel-best-practices`、`testing-best-practices`）は Laravel 公式の参考資料として必要なときに読む。テスト DB まわり（`RefreshDatabase` と `_test` ガード）はこのファイルの規約を優先する。

## 触らないもの

- `.env` は生成も編集もしない。必要なときは実行してほしいコマンドを提示する。
- `docker volume rm` などボリュームを消す操作はしない。

## 役割分担

- 仕様の判断、PR の作成、レビュー指摘の採否、マージは人が行う。
- Claude は実装の草案、テスト、レビューの下書きを担当する。出力は必ず人が確認してから採用する。

## 文章の書き方（PR・レビュー・Issue）

- 結論を先頭に、簡潔に書く。PR 本文は「何を・なぜ・どう確認したか」を書く。
- レビューは指摘を必須と任意に分け、根拠を示す。

## よく使うコマンド

```bash
docker compose up -d --build                       # 環境起動
docker compose exec app php artisan migrate        # マイグレーション (差分)
docker compose exec app php artisan db:seed        # ダミーデータ 12 件
docker compose exec app php artisan test           # テスト (diary_test)
docker compose exec app vendor/bin/pint --test     # 整形チェック
```
