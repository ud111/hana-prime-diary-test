#!/usr/bin/env bash
# PreToolUse(Bash) フック: データが消える DB コマンドを実行前に止める。
# exit 2 でツール実行をブロックし、stderr の内容が Claude にフィードバックされる。
# 背景: テストの接続先を誤ると開発用 DB のデータが消える (Issue #4 で実際に起きた)。
#
# 注意: コマンド文字列に禁止語が含まれていれば止める単純な一致なので、
#       grep やコミットメッセージに禁止語を書いた場合も止まる (安全側の割り切り)。
set -u
input=$(cat)

# jq があれば command だけを取り出す。無ければ JSON 全体を対象にして安全側に倒す
if command -v jq >/dev/null 2>&1; then
  cmd=$(printf '%s' "$input" | jq -r '.tool_input.command // empty')
else
  cmd=$input
fi

if printf '%s' "$cmd" | grep -iqE 'migrate:fresh|migrate:reset|migrate:refresh|migrate:rollback|db:wipe|drop[[:space:]]+(database|schema|table)|truncate[[:space:]]+table'; then
  echo "🚫 データが消える DB コマンドのため実行を止めました。" >&2
  echo "   migrate:fresh / migrate:reset / migrate:refresh / migrate:rollback / db:wipe / DROP / TRUNCATE は Claude からは実行しません。" >&2
  echo "   スキーマ変更は migrate (差分) を使い、本当に必要なときは人が手で実行してください。" >&2
  exit 2
fi
exit 0
