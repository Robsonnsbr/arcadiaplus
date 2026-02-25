#!/usr/bin/env bash
set -euo pipefail

# Resolve directory of this script (works in Docker, CI and local)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

METABASE_JAR="metabase.jar"

if [[ ! -f "$METABASE_JAR" ]]; then
  echo "[Metabase] ERROR: $METABASE_JAR not found in $SCRIPT_DIR"
  exit 1
fi

# Network
export MB_JETTY_PORT="${MB_JETTY_PORT:-8088}"
export MB_JETTY_HOST="${MB_JETTY_HOST:-0.0.0.0}"

# Metadata DB (H2 embedded)
export MB_DB_TYPE=h2
export MB_DB_FILE=/app/metabase/metabase.db

# App behavior
export MB_EMOJI_IN_LOGS=false
export MB_COLORIZE_LOGS=false
export MB_ANON_TRACKING_ENABLED=false
export MB_CHECK_FOR_UPDATES=false
export MB_ENABLE_EMBEDDING=true
export MB_SITE_NAME="Arcádia Insights"
export MB_SITE_LOCALE="pt-BR"

echo "[Metabase] Starting..."
echo "[Metabase] Working directory: $SCRIPT_DIR"
echo "[Metabase] Port: $MB_JETTY_PORT"
echo "[Metabase] Metadata DB: H2 -> $MB_DB_FILE"

exec java \
  --add-opens java.base/java.nio=ALL-UNNAMED \
  -Xmx512m \
  -jar "$METABASE_JAR"
