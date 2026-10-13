#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
#  Worksitem — staging'den canlıya geçiş
#
#    bash scripts/golive-from-staging.sh check                      # SADECE kontrol, hiçbir şeyi değiştirmez
#    bash scripts/golive-from-staging.sh deploy                     # yedek + kodu canlıya taşı
#    bash scripts/golive-from-staging.sh deploy --preset ilk-dalga  # + araçları yayına al
#    bash scripts/golive-from-staging.sh rollback                   # son yedeğe dön
#    bash scripts/golive-from-staging.sh rollback 20261013-120000   # belirli yedeğe dön
#
#  Varsayılan yollar (değiştirmek için: STAGING=... LIVE=... bash ...):
#    staging : ~/apps/worksitem-staging
#    canlı   : ~/apps/worksitem
#    yedek   : ~/backups/golive-TARIH/ (kod arşivi + veritabanı dökümü + .env)
#
#  Canlının .env dosyasının DEĞERLERİ hiçbir zaman ekrana yazılmaz.
#  Sadece staging'de olup canlıda olmayan anahtarların ADLARI gösterilir.
#  Yalnızca tar/find/cmp kullanır (rsync gerekmez).
# ═══════════════════════════════════════════════════════════════
set -euo pipefail

STAGING="${STAGING:-$HOME/apps/worksitem-staging}"
LIVE="${LIVE:-$HOME/apps/worksitem}"
BACKUPS="${BACKUPS:-$HOME/backups}"
PHP="${PHP:-php}"
COMPOSER="${COMPOSER:-composer}"
SELF="$0"

# Canlıya kopyalanmayacak .env anahtarları (ortama özel olanlar)
ENV_DENY='^(APP_|DB_|SESSION_|MAIL_|LOG_|REDIS_|CACHE_|QUEUE_|STAGING|WORKSITEM_TOOL_QA_|GA4_|GA_|GOOGLE_ADS|GTM_|IYZICO_BASE_URL|IYZICO_KESIF_ENABLED|AI_SPEND_ENABLED|TRUSTED_PROXIES|FILESYSTEM_|.*TEST_MODE|.*SANDBOX|.*_DEBUG)'

# Kopyalanmayan yollar (ortama ait veriler)
EXCL_RE='^(\.env|\.env\.backup.*|public/hot|bootstrap/cache/.*|.*\.sqlite|.*\.sqlite-journal|.*\.save|.*\.before-.*|\.phpunit.*|.*\.(bak|orig|rej|swp)|.*\.bak[-_.].*|.*\.backup[-_.].*|.*~)$'

# Geçici dosyalar (/dev/fd gerektiren <(...) kullanılmaz; bazı sunucularda yok)
TMPD="$(mktemp -d)"
trap 'rm -rf "$TMPD"' EXIT

# Bu klasörlerde canlıda kalmış ama staging'de artık olmayan dosyalar silinir
CODE_RE='^(app|bootstrap|config|database|lang|resources|routes)/'

c_y=$'\033[1;33m'; c_g=$'\033[1;32m'; c_r=$'\033[1;31m'; c_0=$'\033[0m'
step() { printf '\n%s▶ %s%s\n' "$c_y" "$*" "$c_0"; }
ok()   { printf '  %s✔%s %s\n' "$c_g" "$c_0" "$*"; }
warn() { printf '  %s!%s %s\n' "$c_y" "$c_0" "$*"; }
bad()  { printf '  %s✘%s %s\n' "$c_r" "$c_0" "$*"; }
die()  { bad "$*"; exit 1; }

env_keys() { grep -E '^[A-Za-z_][A-Za-z0-9_]*=' "$1" | cut -d= -f1 | LC_ALL=C sort -u; }
env_val()  { { grep -E "^$2=" "$1" || true; } | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//'; }
# mysql / mysqldump bağlantı argümanları (şifre MYSQL_PWD ile verilir, ekrana yazılmaz)
db_host() { local v; v="$(env_val "$LIVE/.env" DB_HOST)"; echo "${v:-localhost}"; }
db_port() { local v; v="$(env_val "$LIVE/.env" DB_PORT)"; echo "${v:-3306}"; }
art()      { (cd "$1" && shift && "$PHP" artisan "$@"); }

new_env_keys() {
    env_keys "$STAGING/.env" > "$TMPD/env.s"
    env_keys "$LIVE/.env" > "$TMPD/env.l"
    LC_ALL=C comm -23 "$TMPD/env.s" "$TMPD/env.l" | grep -Ev "$ENV_DENY" || true
}

# Uygulama dosyaları (vendor, storage, .git, node_modules, tests hariç), göreli yol, sıralı
list_files() {
    (cd "$1" && find . \( -path ./.git -o -path ./storage -o -path ./node_modules -o -path ./vendor \
        -o -path ./tests -o -path ./public/storage \) -prune -o \( -type f -o -type l \) -print) \
        | sed 's#^\./##' | grep -Ev "$EXCL_RE" | LC_ALL=C sort || true
}

list_vendor() { [[ -d "$1/vendor" ]] && (cd "$1" && find vendor \( -type f -o -type l \) | LC_ALL=C sort) || true; }

# $1 kaynak, $2 hedef, $3 dosya listesi → farklı / yeni olanlar
changed_files() {
    local f
    while IFS= read -r f; do
        cmp -s "$1/$f" "$2/$f" 2>/dev/null || printf '%s\n' "$f"
    done < "$3"
}

# $1 kaynak, $2 hedef, $3 liste dosyası
copy_files() { [[ -s "$3" ]] || return 0; tar -C "$1" -cf - -T "$3" | tar -C "$2" -xpf -; }

# Hedefte olup kaynakta olmayan kod dosyaları
stale_files() {
    (list_files "$1" | grep -E "$CODE_RE" || true) > "$TMPD/stale.s"
    (list_files "$2" | grep -E "$CODE_RE" || true) > "$TMPD/stale.l"
    LC_ALL=C comm -13 "$TMPD/stale.s" "$TMPD/stale.l"
}

# $1 kaynak → $2 hedef: kodu eşitle (yeni/değişen kopyala, eski kod dosyalarını sil)
sync_tree() {
    local tmp="$TMPD/sync.list"
    list_files "$1" > "$tmp"
    copy_files "$1" "$2" "$tmp"
    local n=0 f
    stale_files "$1" "$2" > "$TMPD/stale.del"
    while IFS= read -r f; do rm -f "$2/$f"; n=$((n + 1)); done < "$TMPD/stale.del"
    rm -f "$tmp"
    echo "$n"
}

# $1 kaynak → $2 hedef: vendor klasörünü eşitle
sync_vendor() {
    [[ -d "$1/vendor" ]] || die "vendor bulunamadı: $1/vendor"
    tar -C "$1" -cf - vendor | tar -C "$2" -xpf -
    local f
    list_vendor "$1" > "$TMPD/vendor.s"
    list_vendor "$2" > "$TMPD/vendor.l"
    LC_ALL=C comm -13 "$TMPD/vendor.s" "$TMPD/vendor.l" > "$TMPD/vendor.del"
    while IFS= read -r f; do rm -f "$2/$f"; done < "$TMPD/vendor.del"
}

clear_bootstrap_cache() { rm -f "$1"/bootstrap/cache/{config,routes-v7,routes,events,packages,services}.php; }

# ───────────────────────────── check ─────────────────────────────
cmd_check() {
    local fails=0
    step "Klasörler"
    if [[ -f "$STAGING/artisan" ]]; then ok "staging: $STAGING"; else bad "staging bulunamadı: $STAGING"; fails=1; fi
    if [[ -f "$LIVE/artisan" ]]; then ok "canlı:   $LIVE"; else bad "canlı bulunamadı: $LIVE"; fails=1; fi
    if [[ -f "$LIVE/.env" ]]; then ok "canlı .env var"; else bad "canlı .env yok"; fails=1; fi
    if (( fails )); then die "Yollar hatalı. Örnek: STAGING=~/... LIVE=~/... bash $SELF check"; fi

    step "Araçlar ve sürüm"
    ok "PHP: $("$PHP" -r 'echo PHP_VERSION;')"
    if command -v "$COMPOSER" >/dev/null 2>&1; then ok "composer var (canlıda composer install --no-dev çalışacak)"
    else warn "composer yok: vendor klasörü staging'den kopyalanacak"; fi
    if command -v mysqldump >/dev/null 2>&1; then ok "mysqldump var"; else bad "mysqldump yok (yedek alınamaz)"; fails=1; fi
    if [[ -d "$STAGING/.git" ]]; then ok "staging git kaydı (yamalar üstüne uygulanmış olabilir): $(git -C "$STAGING" log -1 --format='%h %s' 2>/dev/null | cut -c1-90)"; fi
    if art "$STAGING" list --raw 2>/dev/null | grep -q '^tools:publish'; then ok "staging v23 kurulu (tools:publish var)"
    else bad "staging'de tools:publish yok: önce v18–v23 paketlerini staging'e kurun"; fails=1; fi

    step "Disk"
    ok "canlı kod boyutu: $(du -sh --exclude=storage --exclude=node_modules "$LIVE" 2>/dev/null | cut -f1)"
    ok "boş alan: $(df -h "$HOME" | awk 'NR==2{print $4}')"

    step "Kod farkı (staging → canlı)"
    local tmp="$TMPD/check.list" ch st
    list_files "$STAGING" > "$tmp"
    ch=$(changed_files "$STAGING" "$LIVE" "$tmp")
    rm -f "$tmp"
    ok "değişecek / yeni dosya: $(printf '%s' "$ch" | grep -c . || true)"
    printf '%s\n' "$ch" | grep -E '^(routes|config|database/migrations)/' | head -30 | sed 's/^/      /' || true
    st=$(stale_files "$STAGING" "$LIVE")
    if [[ -n "$st" ]]; then
        warn "canlıda silinecek eski kod dosyaları ($(printf '%s\n' "$st" | wc -l)):"
        printf '%s\n' "$st" | head -40 | sed 's/^/      /'
    else ok "silinecek eski dosya yok"; fi
    if ! cmp -s "$STAGING/composer.lock" "$LIVE/composer.lock"; then warn "composer.lock farklı: bağımlılıklar güncellenecek"; fi

    step "Veritabanı (canlı)"
    ok "canlıda şu an bekleyen migration: $(art "$LIVE" migrate:status 2>/dev/null | grep -ci 'pending' || true)"
    ls "$LIVE/database/migrations" | LC_ALL=C sort > "$TMPD/mig.l"
    ls "$STAGING/database/migrations" | grep -E '\.php$' | LC_ALL=C sort > "$TMPD/mig.s"
    LC_ALL=C comm -13 "$TMPD/mig.l" "$TMPD/mig.s" > "$TMPD/mig.new"
    ok "staging'den gelecek yeni migration dosyası: $(wc -l < "$TMPD/mig.new")"
    sed 's/^/      /' "$TMPD/mig.new"

    step ".env (sadece anahtar ADLARI)"
    local nk; nk=$(new_env_keys)
    if [[ -n "$nk" ]]; then
        warn "staging'de olup canlıda olmayan anahtarlar (deploy sırasında onayınızla eklenir):"
        printf '%s\n' "$nk" | sed 's/^/      /'
    else ok "eklenecek yeni anahtar yok"; fi
    local k
    # Gizli olmayan anahtarların DEĞERİ gösterilir (açık/kapalı bayraklar)
    for k in APP_ENV APP_DEBUG AI_SPEND_ENABLED IYZICO_KESIF_ENABLED TILE_VISUALIZER_TEST_MODE WORKSITEM_TOOL_QA_STAGING_UNLOCK WORKSITEM_YFK_PERIOD; do
        if grep -qE "^$k=" "$LIVE/.env"; then ok "canlı $k = $(env_val "$LIVE/.env" "$k")"; else ok "canlı $k: tanımsız (varsayılan)"; fi
    done
    local iu; iu="$(env_val "$LIVE/.env" IYZICO_BASE_URL)"
    case "$iu" in
        *sandbox*) ok "canlı iyzico adresi: TEST (sandbox)" ;;
        "") ok "canlı iyzico adresi: tanımsız → TEST (sandbox)" ;;
        *) ok "canlı iyzico adresi: GERÇEK" ;;
    esac

    step "Araç ön ayarları"
    art "$STAGING" tools:publish --list 2>/dev/null || true
    warn "Bu liste STAGING durumudur; canlıda deploy --preset ile uygulanır"

    echo
    if (( fails )); then die "Kontrol BAŞARISIZ: yukarıdaki ✘ maddeleri giderin."; fi
    printf '%s✔ Kontrol tamam. Hiçbir şey değiştirilmedi.%s\n' "$c_g" "$c_0"
    echo "  Canlıya almak için:  bash $SELF deploy --preset ilk-dalga"
}

# ───────────────────────────── deploy ─────────────────────────────
cmd_deploy() {
    local preset="" yes=0
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --preset) preset="${2:-}"; shift 2 ;;
            --yes) yes=1; shift ;;
            *) die "Bilinmeyen seçenek: $1" ;;
        esac
    done
    [[ -f "$STAGING/artisan" && -f "$LIVE/artisan" && -f "$LIVE/.env" ]] || die "Önce: bash $SELF check"
    command -v mysqldump >/dev/null 2>&1 || die "mysqldump yok; yedeksiz canlıya çıkılmaz."
    art "$STAGING" list --raw 2>/dev/null | grep -q '^tools:publish' || die "staging'de v23 kurulu değil"
    if [[ -n "$preset" ]] && ! grep -q "'$preset'" "$STAGING/config/worksitem_golive.php"; then die "Ön ayar yok: $preset"; fi

    if (( ! yes )); then
        echo "Canlı site: $LIVE  ←  $STAGING"
        [[ -n "$preset" ]] && echo "Yayına alınacak araç ön ayarı: $preset"
        read -r -p "Canlıya almak için büyük harflerle CANLI yazın: " a
        [[ "$a" == "CANLI" ]] || die "İptal edildi."
    fi

    local stamp dir
    stamp="$(date +%Y%m%d-%H%M%S)"
    dir="$BACKUPS/golive-$stamp"
    mkdir -p "$BACKUPS"; chmod 700 "$BACKUPS"; mkdir -m 700 "$dir"

    step "1/10 Yedek: $dir"
    (umask 077
     cp "$LIVE/.env" "$dir/env.backup"
     MYSQL_PWD="$(env_val "$LIVE/.env" DB_PASSWORD)" mysqldump --single-transaction --quick --no-tablespaces \
         -h "$(db_host)" -P "$(db_port)" \
         -u "$(env_val "$LIVE/.env" DB_USERNAME)" "$(env_val "$LIVE/.env" DB_DATABASE)" | gzip > "$dir/db.sql.gz"
     tar -czf "$dir/code.tgz" -C "$LIVE" --exclude=./storage --exclude=./node_modules --exclude=./public/storage .) \
        || die "Yedek alınamadı (veritabanı veya kod). Canlıya dokunulmadı."
    [[ $(gzip -cd "$dir/db.sql.gz" | head -c 200 | wc -c) -gt 50 ]] || die "Veritabanı yedeği boş görünüyor; durduruldu (canlıya dokunulmadı)."
    ok "veritabanı: $(du -h "$dir/db.sql.gz" | cut -f1)"
    ok "kod: $(du -h "$dir/code.tgz" | cut -f1)"
    git -C "$STAGING" log -1 --format='%H %s' > "$dir/staging-commit.txt" 2>/dev/null || true
    echo "$dir" > "$BACKUPS/golive-latest"

    trap 'printf "\n%s✘ Yarıda kaldı (satır %s). Site BAKIM MODUNDA olabilir.%s\n  Düzeltip tekrar deneyin ya da geri dönün: bash %s rollback %s\n" "$c_r" "$LINENO" "$c_0" "$SELF" "$stamp"' ERR

    step "2/10 .env: yeni anahtarlar"
    local nk; nk=$(new_env_keys)
    if [[ -n "$nk" ]]; then
        printf '%s\n' "$nk" | sed 's/^/      /'
        local a="evet"
        (( yes )) || read -r -p "Bu anahtarlar staging'deki değerleriyle canlı .env'e eklensin mi? (evet/hayır) " a
        if [[ "$a" == "evet" ]]; then
            { echo; echo "# --- staging'den eklendi $stamp ---"; } >> "$LIVE/.env"
            local k
            while IFS= read -r k; do grep -E "^$k=" "$STAGING/.env" | tail -n1 >> "$LIVE/.env"; done <<< "$nk"
            ok "$(printf '%s\n' "$nk" | wc -l) anahtar eklendi"
        else warn "eklenmedi"; fi
    else ok "yeni anahtar yok"; fi

    step "3/10 Bakım modu"
    art "$LIVE" down --retry=60 --refresh=15 || true

    step "4/10 Kod kopyalanıyor"
    local removed; removed=$(sync_tree "$STAGING" "$LIVE")
    clear_bootstrap_cache "$LIVE"
    ok "kod eşitlendi (eski dosya silindi: $removed)"

    step "5/10 Bağımlılıklar"
    if command -v "$COMPOSER" >/dev/null 2>&1; then
        (cd "$LIVE" && "$COMPOSER" install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress)
    else
        sync_vendor "$STAGING" "$LIVE"
        warn "composer yok: vendor staging'den kopyalandı"
    fi

    step "6/10 Klasörler ve önbellek temizliği"
    mkdir -p "$LIVE/storage/fonts" "$LIVE/storage/app/public" "$LIVE/storage/framework/cache/data" \
        "$LIVE/storage/framework/sessions" "$LIVE/storage/framework/views" "$LIVE/storage/logs"
    clear_bootstrap_cache "$LIVE"
    art "$LIVE" optimize:clear >/dev/null

    step "7/10 Migration"
    art "$LIVE" migrate --pretend --force > "$dir/migrate-pretend.sql" 2>&1 || true
    ok "SQL önizleme: $dir/migrate-pretend.sql"
    art "$LIVE" migrate --force
    [[ -e "$LIVE/public/storage" ]] || art "$LIVE" storage:link

    step "8/10 Önbellekler"
    art "$LIVE" optimize
    art "$LIVE" filament:optimize >/dev/null 2>&1 || true

    step "9/10 Araçlar"
    if [[ -n "$preset" ]]; then
        art "$LIVE" tools:publish "$preset"
    else
        (art "$LIVE" tools:publish --list 2>/dev/null | head -n 1) || true
        warn "ön ayar verilmedi; araç yayın durumları değişmedi"
    fi

    step "10/10 Site açılıyor"
    art "$LIVE" up
    trap - ERR

    step "Kontroller"
    art "$LIVE" app:preflight || warn "preflight uyarı/FAIL verdi: yukarıyı okuyun"
    art "$LIVE" app:integrations 2>/dev/null || true
    smoke "$(env_val "$LIVE/.env" APP_URL)"

    printf '\n%s✔ Canlıya alındı.%s Yedek: %s\n' "$c_g" "$c_0" "$dir"
    echo "  Sorun olursa: bash $SELF rollback $stamp"
}

smoke() {
    local base="${1%/}"
    [[ -n "$base" ]] || { warn "APP_URL yok, duman testi atlandı"; return 0; }
    step "Duman testi: $base"
    local p code
    for p in / /up /araclar /araclar/banyo-maliyet-hesaplayici /araclar/seramik-maliyet-hesaplayici /maliyet-hesapla /admin/login; do
        code=$(curl -s -o /dev/null -m 20 -w '%{http_code}' "$base$p" 2>/dev/null) || true
        if [[ "$code" =~ ^(200|302)$ ]]; then ok "$code $p"; else warn "${code:-000} $p"; fi
    done
}

# ───────────────────────────── rollback ─────────────────────────────
cmd_rollback() {
    local dir
    if [[ -n "${1:-}" ]]; then dir="$BACKUPS/golive-$1"; else dir="$(cat "$BACKUPS/golive-latest" 2>/dev/null || true)"; fi
    [[ -n "$dir" && -f "$dir/code.tgz" ]] || die "Yedek bulunamadı: ${dir:-?}  (ls $BACKUPS)"
    echo "Geri dönülecek yedek: $dir"
    read -r -p "Kod bu yedeğe dönsün mü? Büyük harflerle GERI yazın: " a
    [[ "$a" == "GERI" ]] || die "İptal edildi."

    step "Bakım modu"
    art "$LIVE" down --retry=60 || true

    step "Kod geri yükleniyor"
    local tmp; tmp="$(mktemp -d "$BACKUPS/.restore-XXXX")"
    tar -xzf "$dir/code.tgz" -C "$tmp"
    sync_tree "$tmp" "$LIVE" >/dev/null
    if [[ -d "$tmp/vendor" ]]; then sync_vendor "$tmp" "$LIVE"; fi
    cp "$dir/env.backup" "$LIVE/.env"
    rm -rf "$tmp"
    clear_bootstrap_cache "$LIVE"
    ok "kod, vendor ve .env geri yüklendi"

    read -r -p "Veritabanı da yedeğe dönsün mü? (O andan sonraki talepler/kayıtlar SİLİNİR) (evet/hayır) " a
    if [[ "$a" == "evet" ]]; then
        gzip -cd "$dir/db.sql.gz" | MYSQL_PWD="$(env_val "$LIVE/.env" DB_PASSWORD)" mysql \
            -h "$(db_host)" -P "$(db_port)" \
            -u "$(env_val "$LIVE/.env" DB_USERNAME)" "$(env_val "$LIVE/.env" DB_DATABASE)"
        ok "veritabanı geri yüklendi"
    else
        warn "veritabanı olduğu gibi bırakıldı (yeni tablolar eski kodu etkilemez)"
    fi

    art "$LIVE" optimize:clear >/dev/null
    art "$LIVE" optimize
    art "$LIVE" up
    printf '\n%s✔ Geri dönüldü.%s\n' "$c_g" "$c_0"
}

case "${1:-}" in
    check) cmd_check ;;
    deploy) shift; cmd_deploy "$@" ;;
    rollback) shift; cmd_rollback "${1:-}" ;;
    *) sed -n '2,18p' "$SELF" | sed 's/^# \{0,1\}//'; exit 1 ;;
esac
