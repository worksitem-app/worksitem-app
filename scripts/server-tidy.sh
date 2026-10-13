#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
#  Worksitem — sunucu temizliği (önce DENEME, sonra onayla arşive)
#
#    bash scripts/server-tidy.sh                 # DENEME: yalnızca listeler, hiçbir şeye dokunmaz
#    bash scripts/server-tidy.sh --apply         # onay ister (ARSIVLE yazın), sonra arşivler
#    bash scripts/server-tidy.sh --restore DIR   # bir arşivi eski yerlerine geri koyar
#
#  A) Eski uygulama kopyaları ve eski yedek klasörleri (~/.subversion/apps/*, ~/arsiv-*,
#     ~/live_backups, ~/seramik_visualizer_* …) → her klasör tek bir .tar.gz olur
#     (vendor hariç; composer ile yeniden kurulur), arşiv doğrulanınca klasör kaldırılır.
#  B) Ana dizindeki yapıştırma parçaları, yamalar, zip/sh artıkları → arşive taşınır.
#  C) Çalışan iki uygulamadaki (~/apps/worksitem, ~/apps/worksitem-staging) yedek/kaydet
#     kopyaları (*.bak, *.save, *.before-* …), public_html error_log'ları ve 14 günden eski
#     loglar → arşive taşınır.
#  DOKUNULMAYANLAR: çalışan uygulamaların .env'i, vendor'ı, storage/app'i (stage-sync hariç),
#  veritabanları, ~/backups, ~/.ssh, ~/bin, ~/.local, ~/.git, diğer alan adları.
#  Arşiv: ~/backups/arsiv-TARIH/  → bilgisayarınıza indirin; 2 hafta sorun yoksa silin.
# ═══════════════════════════════════════════════════════════════
set -uo pipefail

H="$HOME"
MODE="dry"; [ "${1:-}" = "--apply" ] && MODE="apply"
TMPD="$(mktemp -d)"; trap 'rm -rf "$TMPD"' EXIT
ACTIVE="$H/apps/worksitem $H/apps/worksitem-staging"

# ---------- geri alma ----------
if [ "${1:-}" = "--restore" ]; then
  A="${2:-}"; [ -f "$A/manifest.tsv" ] || { echo "Arşiv bulunamadı: $A (manifest.tsv yok)"; exit 1; }
  n=0
  while IFS=$'\t' read -r kind src dst; do
    if [ "$kind" = "DIR" ]; then
      [ -e "$src" ] && { echo "atlandı (klasör yerinde var): $src"; continue; }
      mkdir -p "$(dirname "$src")" && tar -xzf "$dst" -C "$(dirname "$src")" && n=$((n+1))
    else
      [ -e "$dst" ] || continue
      [ -e "$src" ] && { echo "atlandı (dosya yerinde var): $src"; continue; }
      mkdir -p "$(dirname "$src")" && mv "$dst" "$src" && n=$((n+1))
    fi
  done < "$A/manifest.tsv"
  echo "Geri konan: $n (klasörlerde vendor yoktur; gerekirse: composer install)"; exit 0
fi

JUNK_FIND=( \( -name '*.bak' -o -name '*.bak[0-9]' -o -name '*.bak-*' -o -name '*.bak_*' -o -name '*.backup' -o -name '*.backup-*' -o -name '*.backup_*' \
  -o -name '*.save' -o -name '*.save.*' -o -name '*.before-*' -o -name '*.orig' -o -name '*.rej' -o -name '*.pre-fix-*' -o -name '*.pre-manual-*' \
  -o -name '*.price_check' -o -name 'price_stage_*' -o -name '.env.bozuk*' -o -name '.env.kurtar*' -o -name '.env.once*' -o -name '.env.backup*' -o -name '.env.bak*' \
  -o -name '.env.before-*' -o -name '*~' -o -name '.DS_Store' -o -name 'Thumbs.db' -o -name 'error_log' -o -name 'core.[0-9]*' \) )

# ---------- A) eski klasörler ----------
: > "$TMPD/dirs"
for d in "$H"/.subversion/apps/* "$H"/arsiv-[0-9]* "$H"/arsiv-env-backups-* "$H"/live_backups "$H"/worksitem_backups "$H"/staging_backups \
         "$H"/deploy-backups "$H"/deploy-backup-* "$H"/backup-arsiv-* "$H"/seo*-backup* "$H"/seo11-safe-backup "$H"/worksitem-control-room-* \
         "$H"/seramik_visualizer_* "$H"/worksitem-poz-ui-v1 "$H"/worksitem-seramik-conversion-v8-1 "$H"/worksitem-backups \
         "$H"/apps/worksitem-staging/storage/app/stage-sync; do
  [ -d "$d" ] && [ ! -L "$d" ] && echo "$d" >> "$TMPD/dirs"
done
LC_ALL=C sort -u -o "$TMPD/dirs" "$TMPD/dirs"
# güvenlik: çalışan uygulamalar asla klasör olarak arşivlenmez
for a in $ACTIVE; do grep -vxF "$a" "$TMPD/dirs" > "$TMPD/d2"; mv "$TMPD/d2" "$TMPD/dirs"; done

# ---------- B) ana dizin artıkları ----------
find "$H" -maxdepth 1 -type f \( -name '*.b64' -o -name 'worksitem-*.patch' -o -name 'v[0-9]*p[0-9]*.txt' -o -name 'paste-v*.txt' \
     -o -name 'worksitem-full-source.txt' -o -name 'worksitem-part-*' -o -name 'worksitem-*.zip' -o -name 'apply-*.sh' -o -name 'worksitem-*cleanup*.sh' \
     -o -name 'fix-env.sh' -o -name 'seo_gece.log' \) -print 2>/dev/null > "$TMPD/files"
# ---------- C) çalışan uygulamalar ----------
for d in $ACTIVE; do
  [ -d "$d" ] || continue
  find "$d" \( -path "$d/vendor" -o -path "$d/node_modules" -o -path "$d/storage/framework" -o -path "$d/storage/app" -o -path "$d/.git" \) -prune -o -type f "${JUNK_FIND[@]}" -print 2>/dev/null
  find "$d/storage/logs" -type f -name '*.log' -mtime +14 -print 2>/dev/null
done >> "$TMPD/files"
for dom in worksitem.com app.worksitem.com; do
  ph="$H/domains/$dom/public_html"
  [ -d "$ph" ] && find "$ph" -maxdepth 4 -type f "${JUNK_FIND[@]}" -print 2>/dev/null
  [ -f "$H/domains/$dom/seo_gece.log" ] && echo "$H/domains/$dom/seo_gece.log"
done >> "$TMPD/files"
grep -v -E '/\.env$|^'"$H"'/backups/' "$TMPD/files" | LC_ALL=C sort -u > "$TMPD/final"

# ---------- rapor ----------
ND="$(wc -l < "$TMPD/dirs")"; NF="$(wc -l < "$TMPD/final")"
echo "A) Arşivlenecek eski klasör: $ND"
while IFS= read -r d; do printf '   %7s  %s\n' "$(du -sh "$d" 2>/dev/null | cut -f1)" "${d#$H/}"; done < "$TMPD/dirs"
echo
echo "B+C) Arşive taşınacak dosya: $NF"
sed -e "s#^$H/##" "$TMPD/final" | awk -F/ '{ if ($1=="apps") k=$1"/"$2; else if ($1=="domains") k=$1"/"$2; else k="(ana dizin)"; c[k]++ } END { for (k in c) printf "   %5d  %s\n", c[k], k }' | sort -k2
echo
echo "İncelenecek (bu betik dokunmaz):"
[ -d "$H/domains/worksitem.com/public_html/app" ] && echo "   worksitem.com/public_html/app klasörü (web'den erişilebilir): $(ls "$H/domains/worksitem.com/public_html/app" 2>/dev/null | head -n 8 | tr '\n' ' ')"
[ -d "$H/.git" ] && echo "   ana dizinde git deposu (~/.git, $(du -sh "$H/.git" 2>/dev/null | cut -f1)): $(git -C "$H" log -1 --format='%cs %s' 2>/dev/null | head -c 80)"
[ -d "$H/domains/insaatvtadilat.com/public_html" ] && echo "   insaatvtadilat.com: tüm Laravel projesi public_html içinde, APP_DEBUG açık — ayrı karar"

if [ "$MODE" = "dry" ]; then
  echo; echo "DENEME MODU: hiçbir şeye dokunulmadı. Onaydan sonra:  bash $0 --apply"
  exit 0
fi

# ---------- uygulama ----------
echo; read -r -p "Yukarıdakiler arşivlensin mi? Onay için ARSIVLE yazın: " ok
[ "$ok" = "ARSIVLE" ] || { echo "Vazgeçildi, hiçbir şeye dokunulmadı."; exit 0; }
A="$H/backups/arsiv-$(date +%Y%m%d-%H%M%S)"; mkdir -p "$A/files" "$A/klasorler"
nd=0; nf=0; fail=0
while IFS= read -r d; do
  name="$(echo "${d#$H/}" | tr '/' '_')"; t="$A/klasorler/$name.tar.gz"
  if tar -czf "$t" --exclude='./vendor' --exclude='*/vendor/*' --exclude='*/node_modules/*' -C "$(dirname "$d")" "$(basename "$d")" 2>/dev/null \
     && tar -tzf "$t" >/dev/null 2>&1; then
    rm -rf "$d" && printf 'DIR\t%s\t%s\n' "$d" "$t" >> "$A/manifest.tsv" && nd=$((nd+1))
    echo "   arşivlendi: ${d#$H/}  ($(du -h "$t" | cut -f1))"
  else
    rm -f "$t"; fail=$((fail+1)); echo "   ATLANDI (arşiv oluşturulamadı, klasör yerinde): ${d#$H/}"
  fi
done < "$TMPD/dirs"
while IFS= read -r f; do
  rel="${f#$H/}"; dst="$A/files/$rel"
  mkdir -p "$(dirname "$dst")" && mv "$f" "$dst" && printf 'FILE\t%s\t%s\n' "$f" "$dst" >> "$A/manifest.tsv" && nf=$((nf+1))
done < "$TMPD/final"
echo
echo "Bitti: $nd klasör, $nf dosya → $A ($(du -sh "$A" | cut -f1)); atlanan: $fail"
echo "Geri almak için:  bash $0 --restore $A"
echo "Bilgisayarınıza indirin; 2 hafta sorun çıkmazsa:  rm -rf $A"
