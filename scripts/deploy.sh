#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
#  Worksitem — canlı ortam deploy betiği (SSH ile sunucuda çalıştırın)
#
#    cd ~/worksitem && bash scripts/deploy.sh            # git pull + deploy
#    cd ~/worksitem && bash scripts/deploy.sh --no-pull  # dosyalar elle yüklendiyse
#
#  Sıra: bakım modu → DB yedeği → kod → bağımlılık → migrate → cache → canlı → preflight
#  Bir adım hata verirse site BAKIM MODUNDA kalır; sorunu giderip betiği
#  yeniden çalıştırın ya da yedekten dönün (bkz. DEPLOY.md → Geri dönüş).
# ═══════════════════════════════════════════════════════════════
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

PHP="${PHP:-php}"
COMPOSER="${COMPOSER:-composer}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/worksitem-backups}"
PULL=1
[[ "${1:-}" == "--no-pull" ]] && PULL=0

step() { printf '\n\033[1;33m▶ %s\033[0m\n' "$*"; }
trap 'printf "\n\033[1;31m✘ Deploy yarıda kaldı (satır %s). Site bakım modunda. DEPLOY.md → Geri dönüş\033[0m\n" "$LINENO"' ERR

[[ -f .env ]] || { echo ".env yok. .env.production.example dosyasından oluşturun."; exit 1; }

env_val() { grep -E "^$1=" .env | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//'; }

step "Bakım moduna alınıyor"
"$PHP" artisan down --retry=60 --refresh=15 || true

step "Veritabanı yedeği"
mkdir -p "$BACKUP_DIR"
STAMP="$(date +%Y%m%d-%H%M%S)"
DUMP="$BACKUP_DIR/worksitem-$STAMP.sql.gz"
if command -v mysqldump >/dev/null 2>&1; then
    MYSQL_PWD="$(env_val DB_PASSWORD)" mysqldump --single-transaction --quick --no-tablespaces \
        -h "$(env_val DB_HOST)" -P "$(env_val DB_PORT)" -u "$(env_val DB_USERNAME)" "$(env_val DB_DATABASE)" \
        | gzip > "$DUMP"
    echo "Yedek: $DUMP ($(du -h "$DUMP" | cut -f1))"
    # Son 14 yedeği tut
    ls -1t "$BACKUP_DIR"/worksitem-*.sql.gz 2>/dev/null | tail -n +15 | xargs -r rm -f
else
    echo "UYARI: mysqldump bulunamadı. Devam etmeden önce hPanel'den manuel yedek alın."
    read -r -p "Yedek alındı mı? (evet/hayır) " ok
    [[ "$ok" == "evet" ]] || exit 1
fi

if [[ $PULL -eq 1 ]]; then
    step "Kod güncelleniyor (git pull --ff-only)"
    git pull --ff-only
fi

step "Composer bağımlılıkları (--no-dev)"
"$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress

step "Migration"
"$PHP" artisan migrate --force

step "storage:link"
[[ -e public/storage ]] || "$PHP" artisan storage:link

step "Cache'ler yeniden derleniyor"
"$PHP" artisan optimize:clear
"$PHP" artisan optimize
"$PHP" artisan filament:optimize 2>/dev/null || true

step "Site yeniden canlıda"
"$PHP" artisan up
trap - ERR

step "Canlı ortam kontrolü"
"$PHP" artisan app:preflight || {
    printf '\n\033[1;31mPreflight FAIL verdi; site açık ama yukarıdaki maddeleri hemen düzeltin.\033[0m\n'
    exit 1
}

printf '\n\033[1;32m✔ Deploy tamamlandı.\033[0m Duman testi: / , /araclar , /maliyet-hesapla , /admin , /up\n'
