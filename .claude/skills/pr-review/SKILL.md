---
name: pr-review
description: PR 番号を受け取り、CI の完了を待ってから差分をレビューし、GitHub に Claude Code 名義のレビューを投稿する。人が PR を作成したあとに使う。
---

引数: PR 番号（例: `/pr-review 14`）

1. `gh pr view <番号> --json headRefName,headRefOid,body` で対象を確認し、本文に `Closes #<Issue番号>` があるかを見る。無ければ指摘に含める。
2. `gh run list --workflow ci.yml --branch <ブランチ> --limit 1` で CI を探し、`gh run watch <id> --exit-status` で完了を待つ。失敗していたら原因を調べ、修正して同じブランチに push する。
3. `reviewer` サブエージェントに差分のレビューを依頼する（読み取り専用）。観点は、仕様との一致（`docs/DESIGN.md` §5）、データが消える操作の有無、テストの妥当性、コメントの有無。
4. 指摘を「必須 / 任意」に分け、GitHub API でレビューを投稿する。

```bash
gh api -X POST repos/<owner>/<repo>/pulls/<番号>/reviews --input review.json
```

`review.json` は `{"event":"COMMENT","body":"...","comments":[{"path":..,"line":..,"side":"RIGHT","body":".."}]}` の形。作者自身の PR は承認できないため `event` は常に `COMMENT`。

投稿の形式（CLAUDE.md の規約）
- 見出しは `## 🤖 Claude Code レビュー (<短縮ハッシュ>)`。Claude によるレビューだと分かるようにし、採否は人が決める。
- 先頭にマージ可否の結論。指摘は必須と任意に分け、1 件 2〜3 行で根拠を示す。

5. チャットには結論とレビュー URL、必須指摘の有無だけを短く報告する。
