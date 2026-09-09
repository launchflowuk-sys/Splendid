#!/bin/bash
# Copy the theme and plugin from the repository into the local test WordPress.
set -e
REPO="$(cd "$(dirname "$0")/.." && pwd)"
SITE="${1:-/home/user/wp-site}"

rm -rf "$SITE/wp-content/themes/splendid" "$SITE/wp-content/plugins/splendid-core"
cp -a "$REPO/wp-content/themes/splendid" "$SITE/wp-content/themes/"
cp -a "$REPO/wp-content/plugins/splendid-core" "$SITE/wp-content/plugins/"
echo "synced to $SITE"
