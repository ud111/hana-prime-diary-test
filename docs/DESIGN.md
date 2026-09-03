# 1行日記サイト 設計計画書

作成日: 2026-09-03 / 状態: **環境構築済・初回コミット済・GitHub 作成済（Issue 単位で開発中）**

## 1. 課題要件（原文の要約）

| 区分 | 要件 |
|---|---|
| ページ | 一覧 / 新規投稿 / 編集 |
| 機能 | 一覧は5件ごとにページネーション / 日記ごとに jpg 画像1枚 / 一覧に画像表示 / 削除 |
| 環境 | PHP 最新リリース / MySQL 最新リリース / Laravel |
| その他 | HTML・CSS は最低限 / 未記載仕様は自己判断 / GitHub 等のリポジトリ URL で提出 / **開始時に最初のコミットを作ってから開発** / AI 利用の申告 |

## 2. バージョン選定（2026-09-03 に Docker Hub / Packagist / 公式ドキュメントで実測）

| 対象 | 採用 | 根拠 |
|---|---|---|
| PHP | **8.5.10**（`php:8.5.10-fpm-alpine`） | Docker Hub 公式イメージの最新安定版。8.4 系は 8.4.25 |
| MySQL | **26.7（Innovation、`mysql:26.7`）** ※決定 | Docker Hub `mysql:latest` = `innovation` = 26.7.0（8/18 に 26.7.1 リリース済）。`mysql:lts` = 9.7.2。MySQL 公式マニュアル「Innovation 系列 = 26.7、LTS 系列 = 9.7」 |
| Laravel | **13.x**（framework v13.30.1、skeleton v13.10.1、2026-09-01 時点） | PHP `^8.3` 要件。公式 CI は PHP 8.3/8.4/8.5 × `mysql:9.7` でテスト |
| Composer | 2 系（`composer:2` イメージから同梱） | |
| Web サーバ | nginx（`nginx:1-alpine`） | php-fpm と分離する一般的構成 |

MySQL の判断材料:
- 「最新リリース Ver.」を文字通り取ると **26.7**（`mysql:latest`）。
- Laravel 本体の CI が回っているのは **9.7 LTS**。26.7 は Laravel 側の動作保証外だが、Innovation の差分は追加機能中心で CRUD 用途では問題が出る可能性は低い。
- 推奨: **26.7 を採用**し README に「最新リリース = Innovation 26.7 を採用。LTS 9.7 でも動作確認」と明記（compose のタグ 1 行で切替可能なので両方で動作確認する）。

## 3. リポジトリ構成

- `hana-prime-diary-test/` 自体を **独立 git リポジトリ** にする（親 `passive_income` リポには `.gitignore` に `/hana-prime-diary-test/` を追記して除外。fsns_mobile 等と同方式）。
- Laravel アプリは **リポジトリ直下**（`app/` サブディレクトリにしない）。審査者が `composer` プロジェクトとして標準構成で読めるようにする。
- Docker 関連は `compose.yaml`（ルート）+ `docker/` に集約。**共有 Traefik には依存しない**（審査者環境で `docker compose up` だけで動くこと最優先。`proxy-net` 外部ネットワークが無いと起動失敗するため）。
- ブランチ: `main`。コミットは機能単位で細かく（審査者がコミット履歴で進め方を追えるように）。

```
hana-prime-diary-test/
├── compose.yaml                # name: hana_prime_diary_test を明示
├── docker/
│   ├── php/Dockerfile          # php:8.5.10-fpm-alpine + pdo_mysql + composer、UID/GID 引数
│   ├── php/php.ini             # upload_max_filesize 10M / post_max_size 12M
│   └── nginx/default.conf      # root public/, client_max_body_size 12m
├── docs/DESIGN.md              # 本書（リポに含めるかは要ヒアリング）
├── README.md                   # セットアップ手順・仕様・AI 利用申告
└── (Laravel 標準構成: app/ routes/ resources/ database/ tests/ ...)
```

コミット計画:
1. `Initial commit`（README 見出し + .gitignore のみ。「開発前に最初のコミット」要件を満たす）
2. Docker 環境（compose / Dockerfile / nginx）
3. Laravel 13 スケルトン導入（`composer create-project`）、不要な Vite/Node 依存を除去し素の CSS に
4. マイグレーション・モデル・ファクトリ
5. 一覧 + ページネーション
6. 新規投稿（画像アップロード含む）
7. 編集（画像差替・画像削除）
8. 削除
9. Feature テスト
10. README 整備（手順・仕様判断・AI 利用申告）

## 4. ローカル環境（Docker Compose）

| サービス | イメージ | ホスト公開 | 備考 |
|---|---|---|---|
| web | nginx:1-alpine | **8081** | `infra/registry.yml` web_laravel 帯の空き番号 |
| app | 自前 Dockerfile（php 8.5.10-fpm-alpine） | なし | ソースを bind mount、UID 1000 で実行し権限問題を回避 |
| db | mysql:26.7 | **3327** | named volume `hana_prime_diary_test_db_data`。初期化 SQL でテスト用 DB `diary_test` も作成 |

- 台帳 `infra/registry.yml` に `dir: hana-prime-diary-test / name: hana_prime_diary_test / ports {web: 8081, mysql: 3327} / domains: []` を追記し `make doctor` で衝突ゼロ確認。
- 共有 Traefik・phpMyAdmin・node コンテナは **付けない**（提出物を最小に保つ）。DB を GUI で見たい場合はホストの 3327 に直結。
- `.env` はグローバルルールにより **自動生成・編集しない**。`.env.example` を整備し、`cp .env.example .env` はユーザーに実行依頼する（README にも同手順を記載）。
- 審査者向け起動手順（README に記載予定）:
  ```bash
  docker compose up -d --build
  cp .env.example .env
  docker compose exec app composer install
  docker compose exec app php artisan key:generate
  docker compose exec app php artisan migrate
  docker compose exec app php artisan storage:link
  # http://localhost:8081
  ```

## 5. 仕様（未記載事項の自己判断）

### 5.1 データモデル `diaries`

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | bigint unsigned | PK | |
| diary_date | date | not null | 日記の日付（フォーム既定=今日、変更可） ※決定 |
| content | varchar(255) | not null | 本文。アプリ側で **最大 100 文字**・改行禁止（「1行」） |
| image_path | varchar(255) | nullable | `public` ディスク上の相対パス |
| created_at / updated_at | timestamp | | |

### 5.2 画面・ルーティング（`Route::resource('diaries', ...)->except(['show'])` + `/` → `/diaries` リダイレクト）

| メソッド | パス | 名前 | 画面/処理 |
|---|---|---|---|
| GET | /diaries | diaries.index | 一覧。新しい順（`diary_date` desc, `id` desc）。**5件ごと** `paginate(5)`。画像サムネイル表示。各行に「編集」「削除」 |
| GET | /diaries/create | diaries.create | 新規投稿フォーム（日付・本文・画像） |
| POST | /diaries | diaries.store | 保存 → 一覧へ redirect + フラッシュ「投稿しました」 |
| GET | /diaries/{diary}/edit | diaries.edit | 編集フォーム（現画像プレビュー、差替、「画像を削除」チェック） |
| PUT | /diaries/{diary} | diaries.update | 更新。画像差替時は旧ファイル削除 |
| DELETE | /diaries/{diary} | diaries.destroy | 削除（JS `confirm` で確認）。画像ファイルも削除 |

### 5.3 バリデーション（FormRequest、日本語メッセージ）

- `diary_date`: required / date
- `content`: required / string / max:100 / 改行を含まない（正規表現 or カスタムルール）
- `image`: nullable / file / `mimes:jpg,jpeg` / `mimetypes:image/jpeg`（拡張子偽装対策に実 MIME も検査）/ max:5120 KB
- 編集時 `remove_image`: boolean

### 5.4 画像保存

- `storage/app/public/diaries/{ULID}.jpg` に保存し `php artisan storage:link` で `public/storage` から配信。
- 一覧は CSS で `max-width` を指定したサムネイル表示（リサイズ処理は行わない = 「最低限」の範囲）。
- 削除・差替時にファイルも消す（Model の `deleting` イベント + update 時の明示削除）。

### 5.5 その他の判断

- **認証なし**（課題にユーザーの概念が無いため単一利用者の日記として扱う）
- 表示言語は日本語（`APP_LOCALE=ja`、`APP_TIMEZONE=Asia/Tokyo`）。
- CSS は `public/css/app.css` 1 ファイル（Vite/Tailwind/Node 不使用。審査者の Node 環境を不要にする）。
- ページネーションは Laravel 標準の `links()` を最小の自作ビュー（Tailwind 非依存）で描画。
- テスト: Feature テスト（一覧5件区切り・投稿/編集/削除・画像アップロード `Storage::fake`・バリデーション）。DB は **compose 内 MySQL のテスト用 DB `diary_test`**（`phpunit.xml` で `DB_DATABASE=diary_test` を指定、`RefreshDatabase` 使用） ※決定。
- コード整形は Laravel Pint（同梱）。
- シーダー: `DiaryFactory` で 12 件投入できる `DatabaseSeeder`（ページネーション確認用、任意実行）。

## 6. 提出物

- GitHub リポジトリ https://github.com/ud111/hana-prime-diary-test（public）
- README に「AI ツールの使用有無 / 名称 / 使用範囲」欄を設け、下記を記載する案:
  - 使用有無: あり
  - 名称: Claude Code（Anthropic、モデル Claude Fable 5.1）
  - 使用範囲: 環境構築（Docker）、設計、コード生成、テスト作成、README 作成の全工程で対話的に利用。仕様判断・レビュー・動作確認は本人が実施

## 7. ヒアリング結果（2026-09-03）

| # | 項目 | 決定 |
|---|---|---|
| 1 | MySQL 系列 | **26.7 Innovation**（README に根拠明記、9.7 LTS でも動作確認） |
| 2 | GitHub リポジトリ | **ud111/hana-prime-diary-test（public）**。初回コミット後に作成済 |
| 3 | 認証 | なし（推奨案どおり） |
| 4 | 日付フィールド `diary_date` | **持たせる** |
| 5 | 本文の文字数上限 | 100 文字（推奨案どおり、異論なし） |
| 6 | 画像上限・編集時の画像削除 | 5MB / 削除チェックあり（推奨案どおり） |
| 7 | Feature テスト | **実装する。compose 内 MySQL の `diary_test` DB で実行** |
| 8 | `docs/DESIGN.md` の同梱 | 含める（推奨案どおり） |
| 9 | AI 利用申告文 | README に含める（推奨案どおり） |

## 8. 初回コミットについての注記

課題文「開始時に最初のコミットを作成してから開発を開始」は、コミット時刻が開発開始時刻とみなされる可能性がある。
そのため環境ファイルはローカルに作成済みだが **`git init` と初回コミットはユーザーの GO サインまで行わない**。
GO 後の手順: `git init -b main` → README/.gitignore で `Initial commit` → 環境ファイルをコミット → GitHub リポ作成・push → Laravel 導入。

## 9. 開発フロー（2026-09-03 決定）

- **Issue 駆動**: セクションごとに Issue を起票し Milestone「v1 提出」に紐付ける。
- **ブランチ**: Issue ごとに `feat/<番号>-<内容>`（例 `feat/5-diary-list`）。
- **PR**: 本文に `Closes #<番号>`。マージ方式は **Squash and merge** のみ（main は 1 Issue = 1 コミット、作業コミットは PR 側に残す）。マージ後ブランチ自動削除。
- **CI**: GitHub Actions で PR / main push ごとに Pint（`--test`）と PHPUnit（MySQL 26.7 サービスコンテナ、`diary_test` DB）を実行。
- **コミット規約**: Conventional Commits（`feat:` `fix:` `docs:` `ci:` `chore:`）。
- **デザイン**: 別途作成中（形式未定）。機能は素の HTML + 最小 CSS で先に完成させ、レイアウト 1 枚 + ページ別部分ビューに分けておき、到着後に Issue「デザイン適用」で一括反映する。

| Issue | 内容 |
|---|---|
| #1 | Docker 環境（PHP 8.5 / MySQL 26.7 / nginx）+ 設計書 |
| #2 | Laravel 13 導入と初期設定（ja / Asia/Tokyo / Vite 除去 / .env.example） |
| #3 | CI（Pint + PHPUnit on MySQL） |
| #4 | diaries テーブル・モデル・ファクトリ・シーダ |
| #5 | 一覧ページ + 5 件ページネーション |
| #6 | 新規投稿（jpg 画像アップロード） |
| #7 | 編集（画像差替・画像削除） |
| #8 | 削除（画像ファイルも削除） |
| #9 | デザイン適用 |
| #10 | README 仕上げ（手順・仕様判断・AI 利用申告）、MySQL 9.7 LTS 動作確認 |
