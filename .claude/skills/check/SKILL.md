---
name: check
description: ローカル環境の動作確認を一通り行う。コンテナ稼働、マイグレーション差分、Pint、テスト、主要 URL の応答を確認し、結果を短く報告する。push 前や PR 作成前に使う。
---

すべてリポジトリ直下で実行する。1 つでも失敗したら原因を直してから再実行する。

```bash
docker compose ps --format 'table {{.Name}}\t{{.Status}}'          # 3 コンテナが Up、db が healthy
docker compose exec -T app php artisan migrate --force              # 未適用のマイグレーションを適用 (差分のみ)
docker compose exec -T app vendor/bin/pint --test                   # 整形チェック
docker compose exec -T app php artisan test                         # テスト。接続先は diary_test であること
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8081/up   # 200
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8081/          # 200 (一覧)
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8081/diaries   # 301 (旧 URL → /)
```

確認のポイント
- テストの前後で開発用 DB `diary` の件数が変わっていないこと（変わっていたら接続先を疑う）。
- 画像機能を触った PR では `public/storage` のリンクと `storage/app/public/diaries/` の実ファイルも見る。
- ブラウザで見てほしいときは、URL と何が見えるはずかを一緒に伝える。
