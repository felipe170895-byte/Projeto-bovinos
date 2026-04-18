#!/usr/bin/env bash
set -euo pipefail

if [[ $# -lt 1 ]]; then
  echo "Uso: $0 <arquivo.dump>"
  exit 1
fi

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_NAME="${DB_NAME:-reprobov}"
DB_USER="${DB_USER:-postgres}"
DUMP_FILE="$1"

pg_restore -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" --clean --if-exists "$DUMP_FILE"

echo "Restore concluído de: $DUMP_FILE"
