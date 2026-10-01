#!/bin/sh
APP_DIR="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
cd "$APP_DIR" || exit 1

/usr/bin/php artisan reports:daily-ceo
CEO_EXIT=$?

/usr/bin/php artisan prices:daily-material-report
MARKET_EXIT=$?

echo "CEO_EXIT=$CEO_EXIT"
echo "MARKET_EXIT=$MARKET_EXIT"

[ "$CEO_EXIT" -eq 0 ] && [ "$MARKET_EXIT" -eq 0 ]
