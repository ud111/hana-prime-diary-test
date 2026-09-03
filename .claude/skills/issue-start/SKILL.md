---
name: issue-start
description: Issue 番号を受け取り、main を最新にしてから作業ブランチ feat/<番号>-<内容> を切り、Issue の本文を表示して着手する。新しい Issue に取りかかるときに使う。
---

引数: Issue 番号（例: `/issue-start 5`）

1. 作業ツリーに未コミットの変更が無いことを `git status --short` で確認する。あれば止まって報告する。
2. `git switch main && git pull --ff-only && git fetch -p` で main を最新にする。
3. `gh issue view <番号> --json title,body` で Issue を読み、タイトルから英小文字のスラッグ（例: `diary-list`）を決める。
4. `git switch -c feat/<番号>-<スラッグ>` でブランチを切る。
5. Issue の「作業」チェックリストと「完了条件」を要約して示し、そのまま実装に入る。

注意
- 既に同名ブランチがあるときは作らず、その旨を報告する。
- ブランチ名の番号は Issue 番号であって PR 番号ではない。
