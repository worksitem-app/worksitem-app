#!/bin/sh
# KULLANIM DIŞI (2026-10): Raporlar artık Laravel zamanlayıcısında (routes/console.php).
# Sunucuda yalnızca şu cron satırı olmalı:
#   * * * * * cd /path/to/worksitem && php artisan schedule:run >> /dev/null 2>&1
# Bu betiğe bağlı eski cron satırını silin; aksi halde raporlar iki kez gider.
echo "daily-market-mail.sh kullanım dışı: raporlar schedule:run ile gönderiliyor." >&2
exit 0
