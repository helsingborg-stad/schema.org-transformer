#!/bin/sh
# Gemensam körare för cron-jobb. Används av de andra skripten i den här mappen.
# Användning: run-job.sh <run-skript> [extra argument]
# Exempel:    run-job.sh run/Event.php
#
# Miljövariabler som kan överstyras:
#   PHP_BIN  sökväg till php (standard: php)
#   TARGET   console eller typesense (standard: typesense)

set -eu

# Cron har minimal PATH. På FreeBSD ligger php i /usr/local/bin.
PATH="/usr/local/bin:/usr/bin:/bin:$PATH"
export PATH

if [ "$#" -lt 1 ]; then
    echo "Användning: $0 <run-skript> [argument]" >&2
    exit 64
fi

JOB="$1"
shift

# Projektroten är mappen ovanför cron/
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if [ ! -f "$ROOT/.env" ]; then
    echo "Hittar inte $ROOT/.env" >&2
    exit 66
fi

if [ ! -f "$ROOT/$JOB" ]; then
    echo "Hittar inte $ROOT/$JOB" >&2
    exit 66
fi

# Läs in .env och exportera alla variabler
set -a
. "$ROOT/.env"
set +a

exec "${PHP_BIN:-php}" "$ROOT/$JOB" --target="${TARGET:-typesense}" "$@"
