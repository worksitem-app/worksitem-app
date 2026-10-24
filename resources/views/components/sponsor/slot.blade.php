{{--
    Sponsor kartları (marka reklamı). Her kartta "Sponsorlu" etiketi bulunur; bağlantılar rel="sponsored".
    Kullanım: <x-sponsor.slot tool="banyo-maliyet-hesaplayici" title="Banyonuz için markalar" />
--}}
@props(['tool', 'title' => 'Öne çıkan markalar', 'categories' => null])
@php
    $toolCfg = config("worksitem_sponsors.tools.$tool");
    $cards = collect();
    if ($toolCfg && \Illuminate\Support\Facades\Schema::hasTable('sponsors')) {
        $q = \App\Models\Sponsor::query()->live()->where('tool_slug', $tool);
        if ($categories) {
            $q->whereIn('category', (array) $categories);
        }
        // Aynı öncelikte olanlar her gösterimde karışık sırayla (adil dönüşüm)
        $cards = \App\Models\Sponsor::applyExclusive($q->get())->sortBy(fn ($s) => [$s->priority, mt_rand()])->take((int) config('worksitem_sponsors.max_cards', 3))->values();
        // Arama motoru ve önizleme botları markaya raporlanan gösterime sayılmaz (v113)
        if ($cards->isNotEmpty() && ! preg_match('/bot|crawl|spider|slurp|preview|lighthouse|headless/i', (string) request()->userAgent())) {
            \App\Models\Sponsor::query()->whereIn('id', $cards->pluck('id'))->increment('impressions');
        }
    }
@endphp
@if ($cards->isNotEmpty() || ($toolCfg && config('worksitem_sponsors.house_ad')))
<section class="ws-sp" aria-label="{{ $title }}" data-kainta-auto-link="off">
    <div class="ws-sp-head"><h3>{{ $title }}</h3><span class="ws-sp-tag">Sponsorlu</span></div>
    <div class="ws-sp-grid">
        @forelse ($cards as $s)
            <a class="ws-sp-card" href="{{ route('sponsor.go', $s) }}" target="_blank" rel="sponsored nofollow noopener" data-sponsor="{{ $s->id }}">
                <span class="ws-sp-cat">{{ $toolCfg['categories'][$s->category] ?? 'Sponsor' }}</span>
                <span class="ws-sp-brand">
                    @if ($s->logo_url)<img src="{{ $s->logo_url }}" alt="{{ $s->brand }}" loading="lazy" width="96" height="28">@else<b>{{ $s->brand }}</b>@endif
                </span>
                <strong>{{ $s->headline }}</strong>
                @if ($s->body)<span class="ws-sp-body">{{ $s->body }}</span>@endif
                <span class="ws-sp-cta">{{ $s->cta_label }} →</span>
            </a>
        @empty
            <a class="ws-sp-card ws-sp-house" href="{{ route('sponsor.page', ['arac' => $tool]) }}#basvuru" data-brand-cta="{{ $tool }}">
                <span class="ws-sp-cat">Markalar için</span>
                <strong>Markanız burada görünsün</strong>
                <span class="ws-sp-body">Tadilat bütçesini hesaplayan, satın almaya hazır kullanıcılara tam bu anda ulaşın.</span>
                <span class="ws-sp-cta">Bu araçta yer alın →</span>
            </a>
        @endforelse
    </div>
    <p class="ws-sp-note">Sponsorlu içerikler reklamdır; hesaplama sonuçlarını ve fiyatları etkilemez.</p>
</section>
@once
<style>
.ws-sp{margin:18px 0 0;padding:16px;border:1px solid #E3E6EA;border-radius:16px;background:#fff;text-align:left}
.ws-sp-head{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:12px}
.ws-sp-head h3{margin:0;font-size:15px;font-weight:800;color:#101318}
.ws-sp-tag{font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#5B6472;background:#F4F5F7;border:1px solid #E3E6EA;border-radius:999px;padding:3px 9px}
.ws-sp-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px}
.ws-sp-card{display:flex;flex-direction:column;gap:6px;padding:14px;border:1px solid #E3E6EA;border-radius:12px;text-decoration:none;color:#101318;background:#FAFAFB;transition:.15s}
.ws-sp-card:hover{border-color:#101318;transform:translateY(-1px)}
.ws-sp-cat{font-size:11px;font-weight:700;color:#8A6200;text-transform:uppercase;letter-spacing:.04em}
.ws-sp-brand img{height:28px;width:auto;max-width:120px;object-fit:contain}
.ws-sp-brand b{font-size:15px}
.ws-sp-card strong{font-size:14px;line-height:1.35}
.ws-sp-body{font-size:13px;color:#5B6472;line-height:1.45}
.ws-sp-cta{margin-top:auto;font-size:13px;font-weight:800}
.ws-sp-house{border-style:dashed;background:#FFF8E1}
.ws-sp-note{margin:10px 0 0;font-size:11.5px;color:#8b93a0}
html[data-theme="dark"] .ws-sp{background:#141A24;border-color:#232C3A}
html[data-theme="dark"] .ws-sp-card{background:#0f141d;border-color:#232C3A;color:#E6E9EE}
html[data-theme="dark"] .ws-sp-head h3{color:#fff}
html[data-theme="dark"] .ws-sp-house{background:#2A2410}
</style>
<script>
document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('[data-sponsor]');
    if (a && window.wsTrack) wsTrack('select_promotion', { promotion_id: a.getAttribute('data-sponsor'), creative_slot: 'tool_result' });
});
</script>
@endonce
@endif
