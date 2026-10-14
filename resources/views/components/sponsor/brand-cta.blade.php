{{--
    Markalar için davet bandı: her araç sayfasında firmaların sponsorluk için bize ulaşmasını sağlar.
    Boş alan sayısı gerçek veriden (aktif sponsorlar) hesaplanır; uydurma istatistik yoktur.
    Kullanım: <x-sponsor.brand-cta tool="banyo-maliyet-hesaplayici" />
--}}
@props(['tool'])
@php
    $cfg = (array) config("worksitem_sponsors.tools.$tool", []);
    $cats = array_values(array_filter((array) ($cfg['categories'] ?? []), fn ($l, $k) => $k !== 'genel', ARRAY_FILTER_USE_BOTH));
    $label = $cfg['label'] ?? (config("worksitem_tools.tools.$tool.title") ?: 'Bu hesaplayıcı');
    $max = (int) config('worksitem_sponsors.max_cards', 3);
    $live = 0;
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('sponsors')) {
            $live = \App\Models\Sponsor::query()->live()->where('tool_slug', $tool)->count();
        }
    } catch (\Throwable) {
        $live = 0;
    }
    $free = max(0, $max - $live);
    $href = route('sponsor.page', ['arac' => $tool]).'#basvuru';
@endphp
<section class="ws-bc" aria-labelledby="wsBc-{{ $tool }}" data-kainta-auto-link="off">
    <div class="ws-bc-copy">
        <span class="ws-bc-kicker">Markalar ve üreticiler için</span>
        @if ($cfg)
        <h2 id="wsBc-{{ $tool }}">Ürününüz, bütçesini hesaplayan kullanıcının karşısına çıksın</h2>
        <p>{{ $label }} kullanıcıları malzeme ve marka seçmeden hemen önce buradadır. Sonuç ekranında, karar anında görünün. Her kalemde en fazla {{ $max }} marka gösterilir; hesap sonuçları ve fiyatlar sponsorluktan etkilenmez.</p>
        @else
        <h2 id="wsBc-{{ $tool }}">Markanız bu araçta görünsün</h2>
        <p>{{ $label }} aracını inşaat ve yapı sektöründe karar veren kişiler kullanıyor: mal sahipleri, müteahhitler, mühendisler ve ustalar. Bu sayfada markanız için sponsor alanı açılabilir; hesap sonuçları sponsorluktan etkilenmez.</p>
        @endif
        @if ($cats)
            <ul class="ws-bc-cats" aria-label="Sponsorluk kategorileri">@foreach (array_slice($cats, 0, 5) as $c)<li>{{ $c }}</li>@endforeach</ul>
        @endif
    </div>
    <div class="ws-bc-side">
        <div class="ws-bc-slots" aria-label="Sponsor alanı durumu">
            <b>{{ $free }} / {{ $max }}</b><span>{{ $free > 0 ? 'sponsor alanı boş' : 'alanlar dolu · bekleme listesi' }}</span>
        </div>
        <a class="ws-bc-btn" href="{{ $href }}" data-brand-cta="{{ $tool }}">Bu araçta yer alın →</a>
        <a class="ws-bc-link" href="{{ route('sponsor.page') }}#paketler">Paketleri inceleyin</a>
    </div>
</section>
@once
<style>
.ws-bc{margin:22px 0 0;padding:22px;border:1px solid #E7D7A6;border-radius:18px;background:linear-gradient(135deg,#FFFBEF 0%,#FFF4D1 100%);display:grid;grid-template-columns:1fr auto;gap:20px;align-items:center;text-align:left}
.ws-bc-kicker{display:inline-block;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#8A6200;margin-bottom:6px}
.ws-bc h2{margin:0 0 8px;font-size:19px;line-height:1.3;color:#101318}
.ws-bc p{margin:0;font-size:14px;line-height:1.6;color:#4A515C;max-width:640px}
.ws-bc-cats{display:flex;flex-wrap:wrap;gap:6px;margin:12px 0 0;padding:0;list-style:none}
.ws-bc-cats li{font-size:12px;font-weight:700;color:#5B4A12;background:#fff;border:1px solid #EAD9A0;border-radius:999px;padding:4px 10px}
.ws-bc-side{display:flex;flex-direction:column;align-items:stretch;gap:8px;min-width:210px}
.ws-bc-slots{display:flex;align-items:baseline;gap:8px;justify-content:center;padding:8px 12px;border-radius:12px;background:#fff;border:1px dashed #D9B95A}
.ws-bc-slots b{font-size:20px;color:#101318}
.ws-bc-slots span{font-size:12.5px;color:#5B6472;font-weight:600}
.ws-bc-btn{display:flex;align-items:center;justify-content:center;min-height:46px;padding:0 18px;border-radius:12px;background:#101318;color:#fff;font-weight:800;font-size:14.5px;text-decoration:none}
.ws-bc-btn:hover{background:#2a2f37}
.ws-bc-link{text-align:center;font-size:12.5px;font-weight:700;color:#5B4A12}
@media (max-width:720px){.ws-bc{grid-template-columns:1fr;padding:18px}.ws-bc-side{min-width:0}}
html[data-theme="dark"] .ws-bc{background:linear-gradient(135deg,#221d0e 0%,#2A2410 100%);border-color:#4a3d16}
html[data-theme="dark"] .ws-bc h2{color:#fff}
html[data-theme="dark"] .ws-bc p,html[data-theme="dark"] .ws-bc-slots span{color:#C9CFD8}
html[data-theme="dark"] .ws-bc-cats li,html[data-theme="dark"] .ws-bc-slots{background:#141A24;border-color:#4a3d16;color:#E8D9A8}
html[data-theme="dark"] .ws-bc-slots b{color:#fff}
html[data-theme="dark"] .ws-bc-btn{background:#FFB800;color:#101318}
html[data-theme="dark"] .ws-bc-link{color:#E8D9A8}
</style>
<script>
document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('[data-brand-cta]');
    if (a && window.wsTrack) wsTrack('sponsor_cta_click', { tool: a.getAttribute('data-brand-cta') });
});
</script>
@endonce
