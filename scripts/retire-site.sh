#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
#  Kullanılmayan bir alan adını kalıcı olarak (301) worksitem.com'a yönlendirir.
#
#    bash scripts/retire-site.sh insaatvtadilat.com            # DENEME: ne yapılacağını gösterir
#    bash scripts/retire-site.sh insaatvtadilat.com --apply    # onay ister (YONLENDIR yazın)
#
#  Sıra: 1) eski sitenin veritabanı dökümü  2) eski dosyaların arşivi (vendor hariç) ve doğrulama
#        3) public_html boşaltılır  4) tüm adresler 301 ile hedefe gider (.htaccess + index.php yedeği)
#  Geri alma: arşiv klasöründeki GERI-AL.txt
# ═══════════════════════════════════════════════════════════════
set -uo pipefail

DOM="${1:-}"; MODE="dry"; [ "${2:-}" = "--apply" ] && MODE="apply"
TARGET="${TARGET:-https://worksitem.com}"
H="$HOME"; PH="$H/domains/$DOM/public_html"
[ -n "$DOM" ] || { echo "Kullanım: bash $0 alanadi.com [--apply]"; exit 1; }
case "$DOM" in worksitem.com|app.worksitem.com|www.worksitem.com) echo "Bu alan adı yönlendirilemez: $DOM"; exit 1;; esac
[ -d "$PH" ] && [ ! -L "$PH" ] || { echo "Klasör bulunamadı: $PH"; exit 1; }

env_val() { { grep -E "^$2=" "$1" 2>/dev/null || true; } | tail -n1 | cut -d= -f2- | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"; }
E="$PH/.env"; DB=""
if [ -f "$E" ] && case "$(env_val "$E" DB_CONNECTION)" in mysql|mariadb) true;; *) false;; esac; then DB="$(env_val "$E" DB_DATABASE)"; fi

echo "Alan adı      : $DOM"
echo "Klasör        : $PH ($(du -sh "$PH" 2>/dev/null | cut -f1), $(find "$PH" -type f 2>/dev/null | wc -l) dosya)"
echo "Veritabanı    : ${DB:-yok} $( [ -n "$DB" ] && echo '(dökümü alınacak; veritabanı silinmez)')"
echo "Yönlendirme   : https://$DOM/herhangi-bir-sayfa  →  $TARGET/  (301 kalıcı)"
echo "Şu anki yanıt : $(curl -s -o /dev/null -w '%{http_code}' "https://$DOM/" 2>/dev/null)"

if [ "$MODE" = "dry" ]; then
  echo; echo "DENEME MODU: hiçbir şeye dokunulmadı. Uygulamak için:  bash $0 $DOM --apply"; exit 0
fi

read -r -p "$DOM eski site arşivlenip $TARGET adresine yönlendirilsin mi? Onay için YONLENDIR yazın: " ok
[ "$ok" = "YONLENDIR" ] || { echo "Vazgeçildi."; exit 0; }

A="$H/backups/arsiv-$DOM-$(date +%Y%m%d-%H%M%S)"; mkdir -p "$A"
# 1) veritabanı dökümü
if [ -n "$DB" ] && command -v mysqldump >/dev/null 2>&1; then
  if MYSQL_PWD="$(env_val "$E" DB_PASSWORD)" mysqldump --single-transaction --quick --no-tablespaces \
       -h "$(env_val "$E" DB_HOST)" -P "$( v="$(env_val "$E" DB_PORT)"; echo "${v:-3306}")" -u "$(env_val "$E" DB_USERNAME)" "$DB" 2>/dev/null | gzip > "$A/db.sql.gz" \
     && [ "$(gzip -dc "$A/db.sql.gz" | head -c 100 | wc -c)" -gt 0 ]; then
    echo "✓ veritabanı dökümü: $(du -h "$A/db.sql.gz" | cut -f1)"
  else
    echo "✘ veritabanı dökümü alınamadı — işlem durduruldu, hiçbir dosyaya dokunulmadı."; exit 1
  fi
fi
# 2) dosya arşivi (vendor ve node_modules hariç; composer.lock arşivde, gerekirse yeniden kurulur)
if tar -czf "$A/site.tar.gz" --exclude='./vendor' --exclude='./node_modules' -C "$PH" . 2>/dev/null && tar -tzf "$A/site.tar.gz" >/dev/null 2>&1; then
  echo "✓ dosya arşivi: $(du -h "$A/site.tar.gz" | cut -f1)"
else
  echo "✘ arşiv oluşturulamadı — işlem durduruldu, hiçbir dosyaya dokunulmadı."; rm -f "$A/site.tar.gz"; exit 1
fi
# 3) public_html boşaltılır, 4) yönlendirme yazılır
find "$PH" -mindepth 1 -maxdepth 1 -exec rm -rf {} +
cat > "$PH/.htaccess" <<EOF
# $DOM → $TARGET (kalıcı yönlendirme, $(date +%Y-%m-%d)). Eski site: $A
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^ $TARGET/ [R=301,L]
</IfModule>
EOF
cat > "$PH/index.php" <<EOF
<?php
header('Location: $TARGET/', true, 301);
exit;
EOF
cat > "$A/GERI-AL.txt" <<EOF
Eski siteyi geri koymak için:
  rm -rf "$PH"/* "$PH"/.htaccess && tar -xzf "$A/site.tar.gz" -C "$PH" && cd "$PH" && composer install --no-dev
Veritabanı silinmedi ($DB); döküm: $A/db.sql.gz
EOF
echo "✓ yönlendirme yazıldı"
sleep 1
echo "Kontrol: https://$DOM/            → $(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "https://$DOM/")"
echo "Kontrol: https://$DOM/eski-sayfa  → $(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "https://$DOM/eski-sayfa")"
echo "Arşiv: $A  (bilgisayarınıza indirin)"
