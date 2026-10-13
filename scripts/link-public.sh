#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
#  Yayın klasörünü (docroot) uygulamanın public/ klasörüne kısayollarla bağlar.
#  Böylece css/js/görseller ve yüklenen dosyalar (storage) her kurulumda güncel kalır;
#  elle kopyalanmış eski dosyalar arşive taşınır.
#
#    bash scripts/link-public.sh DOCROOT UYGULAMA [--block-host alan.com]            # DENEME
#    bash scripts/link-public.sh DOCROOT UYGULAMA [--block-host alan.com] --apply    # uygular
#
#  Örnek (staging): app.worksitem.com'un gerçek yayın klasörü ~/domains/worksitem.com/public_html/app
#    bash scripts/link-public.sh ~/domains/worksitem.com/public_html/app ~/apps/worksitem-staging --block-host worksitem.com
#  --block-host: aynı klasörün worksitem.com/app/ adresinden açılmasını engeller (404).
#  Güvenlik: işlemden önce/sonra ana sayfa ve bir css dosyası kontrol edilir; ana sayfa
#  yanıtı değişirse her şey otomatik geri alınır.
# ═══════════════════════════════════════════════════════════════
set -uo pipefail

DOC="${1:-}"; APP="${2:-}"; shift 2 2>/dev/null || true
BLOCK=""; MODE="dry"; URL=""
while [ $# -gt 0 ]; do
  case "$1" in
    --block-host) BLOCK="${2:-}"; shift 2;;
    --apply) MODE="apply"; shift;;
    --url) URL="${2:-}"; shift 2;;
    *) shift;;
  esac
done
[ -d "$DOC" ] && [ -f "$APP/artisan" ] && [ -d "$APP/public" ] || { echo "Kullanım: bash $0 DOCROOT UYGULAMA [--block-host alan.com] [--url https://...] [--apply]"; exit 1; }
DOC="$(cd "$DOC" && pwd)"; APP="$(cd "$APP" && pwd)"
if [ -z "$URL" ] && grep -q "APP_URL=" "$APP/.env" 2>/dev/null; then URL="$(grep -E '^APP_URL=' "$APP/.env" | tail -n1 | cut -d= -f2- | tr -d '"'"'")"; fi

# bağlanacaklar: public/ altındaki her şey (index.php ve .htaccess hariç) + storage
: > /tmp/lp.$$
for p in "$APP"/public/* "$APP"/public/.well-known; do
  [ -e "$p" ] || continue; n="$(basename "$p")"
  case "$n" in index.php|.htaccess|storage|hot|robots.txt) continue;; esac   # robots.txt: her ortamın kendi dosyası (staging: Disallow)
  printf '%s\t%s\n' "$n" "$p" >> /tmp/lp.$$
done
printf '%s\t%s\n' storage "$APP/storage/app/public" >> /tmp/lp.$$

echo "Yayın klasörü : $DOC"
echo "Uygulama      : $APP"
echo "Kontrol adresi: ${URL:-yok}"
echo
while IFS=$'\t' read -r n t; do
  cur="$DOC/$n"
  if [ -L "$cur" ] && [ "$(readlink "$cur")" = "$t" ]; then echo "  doğru    $n"
  elif [ -L "$cur" ]; then echo "  DEĞİŞECEK $n  (şu an → $(readlink "$cur"))"
  elif [ -e "$cur" ]; then echo "  DEĞİŞECEK $n  (elle kopyalanmış $( [ -d "$cur" ] && du -sh "$cur" 2>/dev/null | cut -f1 || echo dosya) → arşiv)"
  else echo "  EKLENECEK $n"; fi
done < /tmp/lp.$$
[ -n "$BLOCK" ] && { grep -qF "# $BLOCK/... adresinden" "$DOC/.htaccess" 2>/dev/null && echo "  doğru    $BLOCK engeli" || echo "  EKLENECEK .htaccess: $BLOCK/... adresinden erişim 404"; }

if [ "$MODE" = "dry" ]; then rm -f /tmp/lp.$$; echo; echo "DENEME MODU: hiçbir şeye dokunulmadı. Uygulamak için sonuna --apply ekleyin."; exit 0; fi

code() { [ -n "$URL" ] && curl -s -o /dev/null -w '%{http_code}' "$1" || echo "-"; }
H0="$(code "$URL/")"
A="$HOME/backups/arsiv-docroot-$(basename "$DOC")-$(date +%Y%m%d-%H%M%S)"; mkdir -p "$A/eski"
: > "$A/manifest.tsv"
while IFS=$'\t' read -r n t; do
  cur="$DOC/$n"
  [ -L "$cur" ] && [ "$(readlink "$cur")" = "$t" ] && continue
  if [ -L "$cur" ]; then printf 'LINK\t%s\t%s\n' "$cur" "$(readlink "$cur")" >> "$A/manifest.tsv"; rm -f "$cur"
  elif [ -e "$cur" ]; then mv "$cur" "$A/eski/$n" && printf 'MOVE\t%s\t%s\n' "$cur" "$A/eski/$n" >> "$A/manifest.tsv"; fi
  ln -s "$t" "$cur" && printf 'NEW\t%s\t-\n' "$cur" >> "$A/manifest.tsv"
done < /tmp/lp.$$
rm -f /tmp/lp.$$
if [ -n "$BLOCK" ] && ! grep -qF "# $BLOCK/... adresinden" "$DOC/.htaccess" 2>/dev/null; then
  cp "$DOC/.htaccess" "$A/eski/.htaccess" 2>/dev/null; printf 'HTACCESS\t%s\t%s\n' "$DOC/.htaccess" "$A/eski/.htaccess" >> "$A/manifest.tsv"
  { printf '# %s/... adresinden bu klasöre erişim kapalı (yalnızca kendi alan adı)\n<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteCond %%{HTTP_HOST} ^(www\\.)?%s$ [NC]\nRewriteRule ^ - [R=404,L]\n</IfModule>\n\n' "$BLOCK" "${BLOCK//./\\.}"; cat "$A/eski/.htaccess" 2>/dev/null; } > "$DOC/.htaccess.new" && mv "$DOC/.htaccess.new" "$DOC/.htaccess"
fi
sleep 1
H1="$(code "$URL/")"; C1="$(code "$URL/css/ws-estimator.css")"
echo "Ana sayfa: önce=$H0 sonra=$H1 · css=$C1"
if [ -n "$URL" ] && [ "$H0" != "$H1" ]; then
  echo "FARK VAR → geri alınıyor"
  tac "$A/manifest.tsv" | while IFS=$'\t' read -r k a b; do
    case "$k" in NEW) rm -f "$a";; LINK) ln -s "$b" "$a";; MOVE) mv "$b" "$a";; HTACCESS) cp "$b" "$a";; esac
  done
  echo "Geri alındı; hiçbir değişiklik kalmadı."; exit 1
fi
echo "TAMAM. Eski dosyalar: $A/eski"
[ -n "$BLOCK" ] && echo "Kontrol: https://$BLOCK/$(basename "$DOC")/ → $(curl -s -o /dev/null -w '%{http_code}' "https://$BLOCK/$(basename "$DOC")/")"
