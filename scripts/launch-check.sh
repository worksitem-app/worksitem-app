#!/bin/bash
# Worksitem canlı "motor" kontrolü — yalnız okur, hiçbir şeyi değiştirmez.
# Kullanım: bash ~/apps/worksitem-staging/scripts/launch-check.sh
LIVE=~/apps/worksitem
URL=https://worksitem.com
ok() { printf "  \033[32m✔\033[0m %s\n" "$1"; }
no() { printf "  \033[31m✘\033[0m %s\n" "$1"; }
code() { curl -s -o /dev/null -w "%{http_code}" "$URL$1"; }

echo "▶ Sürüm"
cd "$LIVE" || exit 1
echo "  Canlı kod: $(cd ~/apps/worksitem-staging && git log -1 --format='%s' | cut -c1-90)"

echo "▶ Sayfalar"
for p in / /araclar /maliyet-hesapla /firma-uyeligi /firma-paneli/giris /markalar-icin /legal/firma-uyelik-kosullari /legal/teklif-alma-kosullari /araclar/banyo-maliyet-hesaplayici /araclar/boya-hesaplayici /araclar/mutfak-planlayici /sitemap.xml /llms.txt /robots.txt; do
  c=$(code "$p"); [ "$c" = "200" ] && ok "$c $p" || no "$c $p"
done
c=$(code /teklif-al); [ "$c" = "404" ] && ok "404 /teklif-al (avukat onayına kadar kapalı)" || no "$c /teklif-al (kapalı olmalıydı)"

echo "▶ SEO ve yapay zekâ"
curl -s "$URL/araclar/boya-hesaplayici" | grep -q 'BreadcrumbList' && ok "Gezinti yolu işaretlemesi" || no "Gezinti yolu yok"
curl -s "$URL/araclar/boya-hesaplayici" | grep -q 'kaç TL? 20' && ok "Fiyatlı arama açıklaması" || no "Fiyatlı açıklama yok"
n=$(curl -s "$URL/sitemap.xml" | grep -c '<loc>'); [ "$n" -gt 50 ] && ok "Site haritası: $n adres" || no "Site haritası az: $n"
curl -s "$URL/llms.txt" | grep -q 'Kimler için' && ok "llms.txt güncel" || no "llms.txt eski"
curl -sI -H 'Accept-Encoding: gzip' "$URL/" | grep -qi 'content-encoding' && ok "Sıkıştırma açık" || no "Sıkıştırma yok"

echo "▶ Uygulama (PWA / Google Play)"
curl -s "$URL/sw.js" | grep -q 'cevrimdisi' && ok "Servis çalışanı (sw.js)" || no "sw.js yok"
curl -s "$URL/site.webmanifest" | grep -q '"maskable"' && ok "Uygulama manifesti" || no "Manifest eski"
curl -s "$URL/" | grep -q 'serviceWorker.register' && ok "Sayfalarda uygulama kaydı" || no "Sayfalarda sw kaydı yok"
a=$(curl -s "$URL/.well-known/assetlinks.json"); [ "$a" != "[]" ] && echo "$a" | grep -q 'sha256' && ok "Google Play bağlantısı (assetlinks)" || echo "  · Google Play bağlantısı henüz yok (TWA_SHA256 boş)"

D=~/domains/worksitem.com/public_html; miss=""
for p in "$LIVE"/public/*; do n=$(basename "$p"); case "$n" in index.php|.htaccess|storage|hot|robots.txt|llms.txt|sitemap.xml) continue;; esac; [ -e "$D/$n" ] || miss="$miss $n"; done
[ -z "$miss" ] && ok "Yayın klasörü kısayolları tam" || no "Yayın klasöründe eksik:$miss (çözüm: bash scripts/link-public.sh $D $LIVE --url $URL --apply)"
for n in llms.txt sitemap.xml; do [ -e "$LIVE/public/$n" ] || [ -e "$D/$n" ] && no "$n statik dosya, güncel sürümü gölgeliyor"; done

curl -s "$URL/robots.txt" | grep -q 'firma-paneli' && ok "robots.txt güncel" || no "robots.txt eski (yayın klasöründeki kopya)"
curl -sI "$URL/sw.js" | grep -qi 'cache-control: no-cache' && ok "sw.js önbelleğe alınmıyor" || echo "  · sw.js için no-cache başlığı yok (yayın klasörü .htaccess)"

echo "▶ Sistem"
php artisan app:preflight 2>/dev/null | grep -E "Sonuç|FAIL|WARN" | sed 's/^/  /'
php artisan about 2>/dev/null | grep -i "queue" | sed 's/^ */  Kuyruk: /'
tail -n 1 ~/cron-worksitem.log 2>/dev/null | cut -c1-120 | sed 's/^/  Son cron: /'

echo "▶ Bugünkü durum"
php artisan tinker --execute='
$q = fn ($t) => \Illuminate\Support\Facades\Schema::hasTable($t);
echo "  Araç yayında: ".\App\Models\ToolStatus::query()->where("active", true)->count().PHP_EOL;
echo "  Firma: ".($q("firms") ? \App\Models\Firm::query()->count()." (onay bekleyen ".\App\Models\Firm::query()->where("status","pending")->count().")" : "-").PHP_EOL;
echo "  Sponsor kaydı: ".($q("sponsor_applications") ? \App\Models\SponsorApplication::query()->count()." (yeni ".\App\Models\SponsorApplication::query()->where("status","new")->count().")" : "-").PHP_EOL;
echo "  Son 7 gün teklif talebi (lead): ".\App\Models\Lead::query()->where("created_at", ">", now()->subDays(7))->count().PHP_EOL;
echo "  Fiyat robotu onay bekleyen: ".($q("price_robot_items") ? \App\Models\PriceRobotItem::query()->where("status","needs_review")->count() : "-").PHP_EOL;
echo "  Bildirim: ".(\App\Services\Push\WebPush::enabled() ? "açık" : "anahtar yok").($q("push_subscriptions") ? " · bildirim açan cihaz: ".\App\Models\PushSubscription::query()->count() : "").PHP_EOL;
echo "  Kesilecek fatura: ".($q("orders") ? \App\Models\Order::query()->where("status","paid")->whereNull("invoiced_at")->count() : "-").PHP_EOL;
echo "  Online ödeme: ".(app(\App\Services\Payments\OnlinePayment::class)->enabled() ? "açık" : "kapalı")." · Yapay zekâ: ".(config("services.ai_spend_enabled") ? "açık" : "kapalı")." · Teklif pazaryeri (kullanıcı): ".(config("worksitem_market.enabled") ? "açık" : "kapalı").PHP_EOL;
' 2>/dev/null
echo "Bitti."
