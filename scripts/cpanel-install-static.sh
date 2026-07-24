#!/usr/bin/env bash
set -Eeuo pipefail

ARCHIVE="${1:?release archive is required}"
HOME_ROOT="/home1/gorkhal1"
PUBLIC_ROOT="$HOME_ROOT/public_html"
LARAVEL_ROOT="$HOME_ROOT/gorkhali-laravel"
STAMP="$(date +%Y%m%d%H%M%S)"
STAGING="$HOME_ROOT/gorkhali-static-staging-$STAMP"
BACKUP="$HOME_ROOT/public_html.backup-$STAMP"

cleanup() {
  if [[ -d "$STAGING" ]]; then
    rm -rf -- "$STAGING"
  fi
}
trap cleanup EXIT

test -f "$ARCHIVE"
test -f "$LARAVEL_ROOT/public/index.php"
test -d "$PUBLIC_ROOT"

mkdir -p "$STAGING"
tar -xzf "$ARCHIVE" -C "$STAGING"
test -f "$STAGING/index.html"
test -f "$STAGING/.htaccess"

cp "$LARAVEL_ROOT/public/index.php" "$STAGING/laravel.php"
sed -i \
  -e "s#__DIR__.'/../storage#__DIR__.'/../gorkhali-laravel/storage#g" \
  -e "s#__DIR__.'/../vendor#__DIR__.'/../gorkhali-laravel/vendor#g" \
  -e "s#__DIR__.'/../bootstrap#__DIR__.'/../gorkhali-laravel/bootstrap#g" \
  "$STAGING/laravel.php"

mv "$PUBLIC_ROOT" "$BACKUP"
if ! mv "$STAGING" "$PUBLIC_ROOT"; then
  mv "$BACKUP" "$PUBLIC_ROOT"
  echo "Deployment failed; restored $PUBLIC_ROOT" >&2
  exit 1
fi

rm -f -- "$ARCHIVE"
trap - EXIT
printf 'DEPLOYED=%s\nBACKUP=%s\n' "$PUBLIC_ROOT" "$BACKUP"
