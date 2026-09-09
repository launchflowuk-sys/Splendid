#!/bin/bash
# Build the installable theme and plugin ZIPs into dist/.
# No credentials or local configuration are included.
set -e
REPO="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$REPO/dist"

rm -rf "$DIST"
mkdir -p "$DIST"

cd "$REPO/wp-content/themes"
zip -rq "$DIST/splendid-theme.zip" splendid -x "*.DS_Store"

cd "$REPO/wp-content/plugins"
zip -rq "$DIST/splendid-core.zip" splendid-core -x "*.DS_Store"

cd "$DIST"
ls -lh
echo
echo "Install: Appearance > Themes > Add New > Upload, and Plugins > Add New > Upload."
