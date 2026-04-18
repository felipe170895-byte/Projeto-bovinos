#!/usr/bin/env bash
set -euo pipefail

for f in $(rg --files public config -g '*.php'); do
  php -l "$f" >/dev/null
  echo "OK: $f"
done

echo "Sintaxe PHP validada."
