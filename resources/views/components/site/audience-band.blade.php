{{-- Üç kitleye giriş bandı: ev sahibi / firma / marka --}}
<section class="ws-aud" aria-label="Size uygun başlangıç" data-kainta-auto-link="off">
<div class="container ws-aud-in">
<a class="ws-aud-c" href="#proje-hesaplayici"><span>Ev sahibiyim</span><b>Bütçemi ücretsiz hesapla →</b></a>
@if (\App\Support\Pilot::firmSignupOpen())<a class="ws-aud-c" href="{{ route('firms.page') }}"><span>Usta / firmayım</span><b>Bölgemdeki işleri al →</b></a>@else<a class="ws-aud-c" href="/maliyet-hesapla#calculator"><span>{{ \App\Support\Pilot::areaLabel() }}'dayım</span><b>Hesapla, keşif iste →</b></a>@endif
<a class="ws-aud-c" href="{{ route('sponsor.page') }}#basvuru"><span>Markayım</span><b>Sponsor firma kaydı →</b></a>
</div>
@once
<style>
.ws-aud{padding:14px 0 6px}.ws-aud-in{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.ws-aud-c{display:block;border:1.5px solid #E3E6EA;border-radius:14px;padding:12px 16px;background:#fff;text-decoration:none;color:#101318}
.ws-aud-c:hover{border-color:#FFB800;background:#FFF8E1}.ws-aud-c span{display:block;font-size:12px;font-weight:700;color:#8A6200;text-transform:uppercase;letter-spacing:.05em}.ws-aud-c b{font-size:15.5px}
@media (max-width:720px){.ws-aud-in{grid-template-columns:1fr}}
html[data-theme="dark"] .ws-aud-c{background:#141A24;border-color:#232C3A;color:#E6E9EE}
</style>
@endonce
</section>
