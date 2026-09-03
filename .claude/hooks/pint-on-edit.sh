#!/usr/bin/env bash
# PostToolUse(Edit|Write) フック: アプリの PHP ファイルを編集したら Laravel Pint で整形する。
# CI の Pint チェックで落ちるのを防ぐ。app コンテナが動いていないときは何もしない。
set -u
input=$(cat)

if command -v jq >/dev/null 2>&1; then
  file=$(printf '%s' "$input" | jq -r '.tool_input.file_path // empty')
else
  exit 0
fi

root=$(cd "$(dirname "$0")/../.." && pwd)
rel=${file#"$root/"}

# 対象はアプリのコードだけ。vendor/ などは触らない
case "$rel" in
  app/*.php|bootstrap/*.php|config/*.php|database/*.php|routes/*.php|tests/*.php) ;;
  *) exit 0 ;;
esac

if docker compose -f "$root/compose.yaml" ps --status running app 2>/dev/null | grep -q app; then
  docker compose -f "$root/compose.yaml" exec -T app vendor/bin/pint "$rel" >/dev/null 2>&1 \
    && echo "Pint で整形しました: $rel"
fi
exit 0
