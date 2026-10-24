{{-- Üç kitleye giriş bandı: ev sahibi / firma / marka --}}
<section class="ws-aud" aria-label="Size uygun başlangıç" data-kainta-auto-link="off">
<div class="container wk-container ws-aud-in">
<a class="ws-aud-c" href="#proje-hesaplayici"><span>Ev sahibiyim</span><b>Bütçemi ücretsiz hesapla →</b></a>
@if (\App\Support\Pilot::firmSignupOpen())<a class="ws-aud-c" href="{{ route('firms.page') }}"><span>Usta / firmayım</span><b>Bölgemdeki işleri al →</b></a>@else<a class="ws-aud-c" href="/maliyet-hesapla#calculator"><span>{{ \App\Support\Pilot::areaLabel() }}'dayım</span><b>Hesapla, keşif iste →</b></a>@endif
<a class="ws-aud-c" href="{{ route('sponsor.page') }}#basvuru"><span>Markayım</span><b>Sponsor firma kaydı →</b></a>
</div>
@once
<style>
/* v118: renkler tasarım token'larından (wk-tokens.css her sayfada); açık/koyu tema kendiliğinden */
.ws-aud{padding:var(--wk-space-5,24px) 0}.ws-aud-in{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--wk-space-3,12px)}
.ws-aud-c{display:block;border:1px solid var(--wk-line);border-radius:var(--wk-radius-lg,16px);padding:14px 18px;background:var(--wk-surface);text-decoration:none;color:var(--wk-ink)}
.ws-aud-c:hover{border-color:var(--wk-ink);background:var(--wk-surface)}
.ws-aud-c span{display:block;font-size:12px;font-weight:750;color:var(--wk-accent-text);text-transform:uppercase;letter-spacing:.08em}
.ws-aud-c b{font-size:16px;font-weight:700;color:var(--wk-ink)}
@media (max-width:720px){.ws-aud-in{grid-template-columns:1fr}}
</style>
@endonce
</section>
