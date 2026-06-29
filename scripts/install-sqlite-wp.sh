#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

if ! command -v wp >/dev/null 2>&1; then
  echo "wp-cli is not installed."
  exit 1
fi

echo "Installing SQLite Database Integration plugin..."
wp plugin install sqlite-database-integration --activate --allow-root || true

cat <<'MSG'

SQLite Database Integration was requested via wp-cli.
Check the installed plugin README for the current db.php drop-in and wp-config.php requirements.
Do not continue the WordPress installer until the drop-in is correctly configured.

MSG
