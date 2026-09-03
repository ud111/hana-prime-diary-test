# 1行日記サイト

[![CI](https://github.com/ud111/hana-prime-diary-test/actions/workflows/ci.yml/badge.svg)](https://github.com/ud111/hana-prime-diary-test/actions/workflows/ci.yml)

Laravel で作る 1 行日記サイト（一覧 / 新規投稿 / 編集、5 件ごとのページネーション、jpg 画像 1 枚の添付、削除）。

セットアップ手順・仕様・AI ツール利用の申告は開発の進行に合わせて本 README に追記します。

## ログイン

一覧は誰でも見られます。投稿・編集・削除は持ち主だけができます。`php artisan db:seed` で次のアカウントが作られます。

| メールアドレス | パスワード |
|---|---|
| admin@example.com | password |

ローカル確認用の値です。公開環境で使う場合は必ず変更してください。
