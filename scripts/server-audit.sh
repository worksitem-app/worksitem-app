#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
#  Worksitem — sunucu envanteri (YALNIZCA OKUR, hiçbir dosyayı değiştirmez)
#
#    bash ~/apps/worksitem-staging/scripts/server-audit.sh
#
#  Ne yapar: ana dizin, uygulamalar (~/apps/*, eski kopyalar), alan adları
#  (public_html), yedekler, büyük dosyalar, artık dosyalar, cron, veritabanı
#  boyutları ve dosya sayısı (inode) raporu çıkarır.
#  Rapor: ~/backups/audit-TARIH.txt  → bu dosyayı Claude'a gönderin.
#  Şifre, anahtar, .env değerleri YAZDIRILMAZ (yalnızca güvenli ayar adları).
# ═══════════════════════════════════════════════════════════════
set -uo pipefail

H="$HOME"
mkdir -p "$H/backups"
STAMP="$(date +%Y%m%d-%H%M%S)"
OUT="$H/backups/audit-$STAMP.txt"
TMPD="$(mktemp -d)"; trap 'rm -rf "$TMPD"' EXIT

# Güvenli olarak gösterilebilecek .env ayarları (değerleriyle)
SAFE_KEYS="APP_ENV APP_DEBUG APP_URL QUEUE_CONNECTION CACHE_STORE SESSION_DRIVER FILESYSTEM_DISK DB_CONNECTION DB_HOST DB_DATABASE MAIL_MAILER LOG_CHANNEL LOG_LEVEL AI_SPEND_ENABLED IYZICO_KESIF_ENABLED WORKSITEM_TOOL_QA_STAGING_UNLOCK"

# Artık dosya kalıpları (uygulama içinde; vendor/node_modules hariç)
JUNK_FIND=( \( -name '*.bak' -o -name '*.bak[0-9]' -o -name '*.bak-*' -o -name '*.bak_*' -o -name '*.backup' -o -name '*.backup-*' -o -name '*.backup_*' \
  -o -name '*.save' -o -name '*.save.*' -o -name '*.before-*' -o -name '*.orig' -o -name '*.rej' -o -name '*.pre-fix-*' -o -name '*.pre-manual-*' \
  -o -name '*.price_check' -o -name 'price_stage_*' -o -name '.env.bozuk*' -o -name '.env.kurtar*' -o -name '.env.once*' -o -name '.env.backup*' -o -name '.env.bak*' \
  -o -name '*~' -o -name '.DS_Store' -o -name 'Thumbs.db' -o -name 'error_log' -o -name 'core.[0-9]*' \) )

sec() { printf '\n══════ %s ══════\n' "$*"; }
env_val() { { grep -E "^$2=" "$1" 2>/dev/null || true; } | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"; }
sz() { du -sh "$1" 2>/dev/null | cut -f1; }

{
echo "Worksitem sunucu envanteri — $STAMP"
echo "Kullanıcı: $(whoami)   Sunucu: $(hostname)"

sec "1. Ortam"
echo "PHP       : $(php -v 2>/dev/null | head -n1)"
echo "PHP ekleri: $(php -m 2>/dev/null | grep -v '^\[' | tr '\n' ' ' | head -c 900)"
echo "memory_limit=$(php -r 'echo ini_get("memory_limit");' 2>/dev/null)  max_execution_time=$(php -r 'echo ini_get("max_execution_time");' 2>/dev/null)  upload_max_filesize=$(php -r 'echo ini_get("upload_max_filesize");' 2>/dev/null)"
echo "Composer  : $(composer --version 2>/dev/null | head -n1 || echo yok)"
echo "MySQL     : $(mysql --version 2>/dev/null || echo yok)"
echo "Node      : $(node -v 2>/dev/null || echo yok)   Git: $(git --version 2>/dev/null || echo yok)"
echo "Ana dizin boyutu: $(sz "$H")"
echo "Dosya sayısı (inode, ana dizin): $(find "$H" -xdev 2>/dev/null | wc -l)"
echo "  vendor klasörleri dahil; paylaşımlı hostingte inode sınırı vardır."

sec "2. Ana dizin (boyuta göre)"
for p in "$H"/* "$H"/.[!.]*; do [ -e "$p" ] || continue; printf '%8s  %s%s\n' "$(sz "$p")" "$(basename "$p")" "$( [ -L "$p" ] && echo " -> $(readlink "$p")")"; done | sort -h -r | head -n 60

sec "3. Ana dizindeki artık dosyalar (yapıştırma parçaları, yamalar, arşivler)"
find "$H" -maxdepth 1 -type f \( -name 'v[0-9]*_*.b64' -o -name '*.b64' -o -name 'worksitem-v*.patch' -o -name '*.patch' -o -name 'paste*' -o -name '*.zip' -o -name '*.tar' -o -name '*.tar.gz' -o -name '*.tgz' -o -name '*.sql' -o -name '*.sql.gz' -o -name '*.txt' -o -name '*.log' -o -name '*.php' -o -name '*.sh' -o -name '*.py' -o -name '*.json' \) -printf '%TY-%Tm-%Td  %8s B  %f\n' 2>/dev/null | sort | tail -n 400
echo "toplam: $(find "$H" -maxdepth 1 -type f \( -name '*.b64' -o -name '*.patch' \) 2>/dev/null | wc -l) adet .b64/.patch"

sec "4. Laravel uygulamaları (artisan bulunan klasörler)"
find "$H" -maxdepth 4 -name artisan -type f -not -path '*/vendor/*' -not -path '*/backups/*' -not -path '*/node_modules/*' 2>/dev/null | sort > "$TMPD/apps"
cat "$TMPD/apps"
while IFS= read -r a; do
  d="$(dirname "$a")"
  echo; echo "── $d"
  echo "   toplam $(sz "$d") · vendor $(sz "$d/vendor") · storage $(sz "$d/storage") · logs $(sz "$d/storage/logs") · public $(sz "$d/public")"
  echo "   derlenmiş görünüm: $(find "$d/storage/framework/views" -type f 2>/dev/null | wc -l) · oturum dosyası: $(find "$d/storage/framework/sessions" -type f 2>/dev/null | wc -l) · cache dosyası: $(find "$d/storage/framework/cache" -type f 2>/dev/null | wc -l)"
  echo "   son değişen kod: $(find "$d/app" "$d/routes" "$d/resources" -type f -printf '%TY-%Tm-%Td %TH:%TM\n' 2>/dev/null | sort | tail -n1)"
  if [ -d "$d/.git" ]; then echo "   git: $(git -C "$d" log -1 --format='%h %cs %s' 2>/dev/null | head -c 100) · değişmiş dosya: $(git -C "$d" status --porcelain 2>/dev/null | wc -l)"; else echo "   git: yok"; fi
  if [ -f "$d/.env" ]; then
    echo "   .env anahtar sayısı: $(grep -cE '^[A-Z0-9_]+=' "$d/.env")"
    for k in $SAFE_KEYS; do v="$(env_val "$d/.env" "$k")"; [ -n "$v" ] && echo "     $k=$v"; done
  else echo "   .env YOK"; fi
  echo "   public/storage: $( [ -L "$d/public/storage" ] && readlink "$d/public/storage" || echo 'bağlantı yok')"
  echo "   storage/app/public: $(find "$d/storage/app/public" -type f 2>/dev/null | wc -l) dosya, $(sz "$d/storage/app/public")"
  for s in "$d"/storage/app/public/*/; do [ -d "$s" ] && echo "     $(sz "$s")  $(basename "$s")"; done
  echo "   kök dizindeki ekstra dosyalar (*.py, *.json, *.php, *.sh, *.sql, *.zip):"
  find "$d" -maxdepth 1 -type f \( -name '*.py' -o -name '*.json' -o -name '*.php' -o -name '*.sh' -o -name '*.sql*' -o -name '*.zip' -o -name '*.tar*' -o -name '*.txt' \) \
     -not -name composer.json -not -name package.json -not -name package-lock.json -not -name artisan -printf '     %TY-%Tm-%Td %8s B  %f\n' 2>/dev/null
  echo "   artık dosyalar (yedek/kaydet kopyaları):"
  find "$d" \( -path "$d/vendor" -o -path "$d/node_modules" -o -path "$d/storage/framework" \) -prune -o -type f "${JUNK_FIND[@]}" -printf '     %8s B  %P\n' 2>/dev/null | sort -k3 > "$TMPD/junk"
  head -n 150 "$TMPD/junk"; echo "     toplam: $(wc -l < "$TMPD/junk") dosya"
  echo "   eski loglar (14 günden eski): $(find "$d/storage/logs" -type f -mtime +14 2>/dev/null | wc -l) dosya"
done < "$TMPD/apps"

sec "5. Alan adları (public_html)"
for dd in "$H"/domains/*/; do
  [ -d "$dd" ] || continue
  echo; echo "── $(basename "$dd")  ($(sz "$dd"))"
  ph="$dd/public_html"
  if [ -L "$ph" ]; then echo "   public_html -> $(readlink "$ph")"; fi
  [ -d "$ph" ] || { echo "   public_html yok"; continue; }
  for e in "$ph"/* "$ph"/.[!.]*; do
    [ -e "$e" ] || [ -L "$e" ] || continue
    n="$(basename "$e")"
    if [ -L "$e" ]; then echo "   bağlantı  $n -> $(readlink "$e")"
    elif [ -d "$e" ]; then echo "   klasör    $n ($(sz "$e"))"
    else echo "   dosya     $n ($(stat -c %s "$e" 2>/dev/null) B)"; fi
  done
  if [ -f "$ph/index.php" ]; then echo "   index.php uygulama yolu: $(grep -oE "(apps|worksitem)[A-Za-z0-9_./-]*" "$ph/index.php" | sort -u | tr '\n' ' ')"; fi
  for o in "$dd"/*; do [ "$(basename "$o")" = public_html ] && continue; [ -e "$o" ] && echo "   (alan adı klasöründe) $(basename "$o") $(sz "$o")"; done
done

sec "6. Yedekler"
for b in "$H/backups" "$H/worksitem-backups"; do
  [ -d "$b" ] || continue
  echo "── $b ($(sz "$b"))"
  for e in "$b"/*; do [ -e "$e" ] && printf '   %8s  %s\n' "$(sz "$e")" "$(basename "$e")$( [ -L "$e" ] && echo " -> $(readlink "$e")")"; done | tail -n 60
done

sec "7. 10 MB üzeri dosyalar (vendor ve node_modules hariç)"
find "$H" \( -name vendor -o -name node_modules -o -name .cache \) -prune -o -type f -size +10M -printf '%10s B  %p\n' 2>/dev/null | sort -n -r | head -n 40

sec "8. Zamanlanmış görevler (cron)"
crontab -l 2>/dev/null || echo "(crontab okunamadı — hPanel › Gelişmiş › Cron Jobs ekranının görüntüsünü gönderin)"

sec "9. Veritabanları (boyut, en büyük tablolar)"
while IFS= read -r a; do
  d="$(dirname "$a")"; e="$d/.env"; [ -f "$e" ] || continue
  [ "$(env_val "$e" DB_CONNECTION)" = "mysql" ] || [ "$(env_val "$e" DB_CONNECTION)" = "mariadb" ] || { echo "── $d: $(env_val "$e" DB_CONNECTION) (MySQL değil)"; continue; }
  db="$(env_val "$e" DB_DATABASE)"; host="$(env_val "$e" DB_HOST)"; port="$(env_val "$e" DB_PORT)"
  echo "── $d → veritabanı: $db"
  MYSQL_PWD="$(env_val "$e" DB_PASSWORD)" mysql -h "${host:-localhost}" -P "${port:-3306}" -u "$(env_val "$e" DB_USERNAME)" -N -e "
    SELECT CONCAT('   toplam: ', ROUND(SUM(data_length+index_length)/1048576,1), ' MB, ', COUNT(*), ' tablo') FROM information_schema.tables WHERE table_schema=DATABASE();
    SELECT CONCAT('   ', LPAD(ROUND((data_length+index_length)/1048576,1),7,' '), ' MB  ', LPAD(IFNULL(table_rows,0),9,' '), ' satır  ', table_name) FROM information_schema.tables WHERE table_schema=DATABASE() ORDER BY (data_length+index_length) DESC LIMIT 15;
  " "$db" 2>&1 | sed -e 's/^ERROR.*/   (bağlanılamadı)/'
done < "$TMPD/apps"

sec "10. Özet"
echo "Uygulama sayısı: $(wc -l < "$TMPD/apps")"
echo "Ana dizindeki .b64/.patch: $(find "$H" -maxdepth 1 -type f \( -name '*.b64' -o -name '*.patch' \) 2>/dev/null | wc -l)"
echo "Bitti. Bu dosyayı Claude'a gönderin: $OUT"
} > "$OUT" 2>&1

echo "Rapor hazır: $OUT  ($(wc -l < "$OUT") satır)"
echo "Görmek için:  cat $OUT"
