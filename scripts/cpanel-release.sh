#!/usr/bin/env bash
set -Eeuo pipefail

APP_ROOT="/home1/gorkhal1/gorkhali-khabar"
ARCHIVE="${1:-/home1/gorkhal1/gorkhali-release.tgz}"
NODE_ENV_ACTIVATE="/home1/gorkhal1/nodevenv/gorkhali-khabar/22/bin/activate"
LOCK_DIR="/home1/gorkhal1/.gorkhali-khabar-release.lock"

if ! mkdir "$LOCK_DIR" 2>/dev/null; then
  echo "A Gorkhali Khabar release is already running." >&2
  exit 1
fi

cleanup() {
  rm -f "$ARCHIVE"
  cloudlinux-selector start --interpreter nodejs --user gorkhal1 --app-root "$APP_ROOT" >/dev/null 2>&1 || true
  rmdir "$LOCK_DIR"
}
trap cleanup EXIT

[[ -f "$ARCHIVE" ]] || { echo "Missing release archive: $ARCHIVE" >&2; exit 1; }
[[ -f "$NODE_ENV_ACTIVATE" ]] || { echo "Missing cPanel Node.js environment." >&2; exit 1; }

cloudlinux-selector stop --interpreter nodejs --user gorkhal1 --app-root "$APP_ROOT" >/dev/null 2>&1 || true
pkill -u gorkhal1 -f '/usr/local/lsws/fcgi-bin/lsnode.js' >/dev/null 2>&1 || true
/usr/bin/tar -xzf "$ARCHIVE" -C "$APP_ROOT"

cd "$APP_ROOT"
set +u
source "$NODE_ENV_ACTIVATE"
set -u
export NODE_OPTIONS="${NODE_OPTIONS:-} --v8-pool-size=1"
npm ci
[[ -f "$APP_ROOT/.next/BUILD_ID" ]] || { echo "Release does not contain a Next.js build." >&2; exit 1; }
mkdir -p "$APP_ROOT/tmp"
touch "$APP_ROOT/tmp/restart.txt"
