# 環境構築手順

Docker Desktop（または Docker Engine + Compose v2）だけあれば動きます。PHP、Composer、Node、MySQL をホストに入れる必要はありません。

## 1. 起動

```bash
git clone https://github.com/ud111/hana-prime-diary-test.git
cd hana-prime-diary-test
docker compose up -d --build
```

3 つのコンテナが起動します。

| サービス | 内容 | ホスト側 |
|---|---|---|
| web | nginx | http://localhost:8081 |
| app | PHP 8.5.10 (php-fpm) + Composer 2 | なし |
| db | MySQL 26.7.0 | localhost:3327（user: `diary` / pass: `secret`） |

`db` が healthy になるまで初回は 20〜30 秒かかります。`docker compose ps` で確認できます。

## 2. アプリの初期化

```bash
docker compose exec app composer run setup
```

このコマンドが順に行うこと:

1. `composer install`
2. `.env` が無ければ `.env.example` からコピー
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `php artisan storage:link`（アップロード画像を公開するためのシンボリックリンク）

続けてダミーデータを入れます。持ち主のログインアカウントと、ページネーション確認用の日記 12 件が作られます。

```bash
docker compose exec app php artisan db:seed
```

http://localhost:8081 を開くと一覧が表示されます。ログインは README の「ログイン」を参照してください。

## 3. テストと整形チェック

```bash
docker compose exec app php artisan test        # テスト (テスト用 DB diary_test を使う)
docker compose exec app vendor/bin/pint --test  # コード整形チェック
```

- テストは `phpunit.xml` の設定でテスト用データベース `diary_test` に対して実行します。`diary_test` は MySQL コンテナの初回起動時に `docker/mysql/init/01-test-database.sql` で作られます。
- `tests/TestCase.php` のガードにより、接続先のデータベース名が `_test` で終わらない場合はテストが中止されます。開発用 DB `diary` のデータをテストで消さないための仕組みです。
- GitHub Actions でも同じテストと Pint を実行しています（`.github/workflows/ci.yml`）。

## 4. よくあるつまずき

**Linux で uid が 1000 以外のユーザーの場合**
app コンテナはホストの uid/gid 1000 で動きます。ホストの uid が違うと bind mount への書き込みで失敗するので、プロジェクト直下の `.env` に次を追加してから `docker compose up -d --build` をやり直してください（compose はこの `.env` を変数展開に使います）。

```
UID=1001
GID=1001
```

macOS / Windows の Docker Desktop はファイル所有権を吸収するので、この設定は不要です。

**画像が表示されない**
`public/storage` のシンボリックリンクが無い状態です。`docker compose exec app php artisan storage:link` を実行してください。

**テスト用 DB `diary_test` が無い**
初期化 SQL は MySQL のデータディレクトリが空のとき（ボリューム初回作成時）にしか実行されません。作り直すには次を実行してください。

```bash
docker compose exec db mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS diary_test; GRANT ALL ON diary_test.* TO 'diary'@'%';"
```

**アップロードが 413 になる**
nginx と PHP の上限は 12MB です。アプリ側の上限（5MB）を大きく超えるファイルを送ると、バリデーションより前に nginx が 413 を返します。

## 5. MySQL 9.7 LTS で動かす場合

課題の「最新リリース」を Innovation 系列の 26.7 と解釈して採用していますが、LTS 系列の 9.7 でも動作を確認しています（`docs/DESIGN.md` §2）。9.7 で動かす場合は `compose.yaml` の `db` の `image` を `mysql:9.7` に変えてください。

注意: 26.7 で作ったデータディレクトリは 9.7 では開けません。既にボリューム `hana_prime_diary_test_db_data` がある場合は、別名のボリュームに変えるか、データが不要であればボリュームを削除してから起動してください。

## 6. 停止

```bash
docker compose down        # コンテナを停止 (データは残る)
```

データベースのデータは named volume `hana_prime_diary_test_db_data` に保存されています。
