@props(['service' => 'genel'])

<div class="cta-box" style="background:var(--surface); border:2px solid var(--green); border-radius:16px; padding:24px; text-align:center; margin-top:25px; box-shadow:0 10px 15px -3px rgba(5,150,105,0.1);">
    <h3 style="color:var(--text-main); font-size:1.15rem; font-weight:800; margin-bottom:6px;">Projenizi Şansa Bırakmayın</h3>
    <p style="color:var(--text-muted); font-size:0.875rem; margin-bottom:18px; line-height:1.5;">
        ⚠️ Bu değerler ÇŞB 2026 rayiçleriyle hazırlanmış yaklaşık maliyettir. Kesin metraj ve fiyat, yerinde keşif sonrasında netleşir. <x-inspection-fee-note short /><br><span style="display:block;margin-top:8px;font-size:0.8rem">{{ config('worksitem_legal.result_short') }} <a href="/legal/kullanim-kosullari" style="color:inherit;text-decoration:underline">Koşullar</a></span>
    </p>
    <button type="button" onclick="openLeadModal('{{ $service }}', 'Araca özel hesaplama metrajı üzerinden keşif istendi.')" class="cta-btn" style="display:inline-block; width:100%; background:var(--green); color:#fff; font-weight:800; font-size:1.05rem; padding:16px; border-radius:10px; border:none; cursor:pointer; transition:all 0.25s ease; box-shadow:0 4px 6px -1px rgba(5,150,105,0.2);">
        Bütçeniz uygunsa Kainta İnşaat'tan net teklif alın →
    </button>
</div>
