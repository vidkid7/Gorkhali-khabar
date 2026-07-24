#!/usr/bin/env bash
set -Eeuo pipefail

APP_ROOT="/home1/gorkhal1/gorkhali-khabar"
NODE_ENV_ACTIVATE="/home1/gorkhal1/nodevenv/gorkhali-khabar/22/bin/activate"
LOCK_DIR="/home1/gorkhal1/.gorkhali-khabar-deploy.lock"

if ! mkdir "$LOCK_DIR" 2>/dev/null; then
  echo "A Gorkhali Khabar deployment is already running." >&2
  exit 1
fi
cleanup() {
  rmdir "$LOCK_DIR"
}
trap cleanup EXIT

if [[ ! -f "$NODE_ENV_ACTIVATE" ]]; then
  echo "Missing cPanel Node.js environment: $NODE_ENV_ACTIVATE" >&2
  exit 1
fi

if [[ ! -f "$APP_ROOT/.env" && -f "/home1/gorkhal1/app/.env" ]]; then
  /bin/cp "/home1/gorkhal1/app/.env" "$APP_ROOT/.env"
fi

cd "$APP_ROOT"
source "$NODE_ENV_ACTIVATE"
npm ci
npx prisma generate
npm run build
mkdir -p "$APP_ROOT/tmp"
touch "$APP_ROOT/tmp/restart.txt"
