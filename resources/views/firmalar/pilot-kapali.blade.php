<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firma Üyeliği: Pilot Dönem | Worksitem</title>
    <meta name="description" content="Worksitem pilot döneminde İstanbul'da keşif ve uygulama hizmetini {{ \App\Support\Pilot::provider() }} veriyor. Firma üyeliği bu dönemde kapalıdır.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="{{ url('/firma-uyeligi') }}">
    @include('firmalar._style')
</head>
<body>
<x-site.contech-header />

<header class="fm-hero" data-kainta-auto-link="off">
    <div class="fm-in">
        <span class="fm-chip">Pilot dönem</span>
        <h1>Firma üyeliği şu an kapalı.</h1>
        <p>Worksitem pilot döneminde keşif, teklif ve uygulama hizmetini yalnız {{ \App\Support\Pilot::areaLabel() }}'da {{ \App\Support\Pilot::provider() }} veriyor. Usta, firma ve tedarikçi üyeliğini hizmet bölgemiz genişlediğinde açacağız.</p>
    </div>
</header>

<section class="fm"><div class="fm-in">
    <div class="fm-grid">
        <div class="fm-card"><h3>Hesaplama araçları açık</h3><p>Maliyet, metraj ve planlama araçlarını tüm Türkiye'de ücretsiz kullanabilirsiniz.</p><p style="margin-top:10px"><a href="/araclar" style="color:inherit;font-weight:800">Araçlara git →</a></p></div>
        <div class="fm-card"><h3>Marka ve üreticiler</h3><p>Hesaplayıcılarda sponsor alanları açıktır. Ürünleriniz ilgili kalemin yanında "Sponsorlu" etiketiyle görünür.</p><p style="margin-top:10px"><a href="{{ route('sponsor.page') }}" style="color:inherit;font-weight:800">Markalar için →</a></p></div>
        <div class="fm-card"><h3>İletişim</h3><p>İş birliği için bize <a href="mailto:{{ config('worksitem_company.email', 'info@worksitem.com') }}" style="color:inherit;font-weight:700">{{ config('worksitem_company.email', 'info@worksitem.com') }}</a> adresinden yazabilirsiniz.</p></div>
    </div>
</div></section>

<x-site.contech-footer />
</body>
</html>
