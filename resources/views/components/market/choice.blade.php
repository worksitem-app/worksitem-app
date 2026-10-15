{{--
    Hesap sonrası iki yol: Kainta İnşaat Uzmanları (keşif + teklif) veya kayıtlı firmalardan teklif.
    Bağlantı, sayfadaki sonuç alanlarından (#leadMin, #leadMax, #leadCalculatedData) ve adres çubuğundaki seçimlerden kurulur.
    Kullanım: <x-market.choice tool="banyo-maliyet-hesaplayici" />
--}}
@props(['tool'])
@if (config('worksitem_market.enabled') && config("worksitem_market.tools.$tool"))
<div class="ws-mc" data-kainta-auto-link="off">
    <div class="ws-mc-h">Bu işi kime yaptırmak istersiniz?</div>
    <div class="ws-mc-g">
        <a class="ws-mc-o" href="#budgetConfirmBox" data-mc="kainta">
            <b>Kainta İnşaat Uzmanları yapsın</b>
            <span>Yerinde keşif, yazılı teklif ve uygulama tek elden.</span>
        </a>
        <a class="ws-mc-o" href="{{ route('market.request.create', ['arac' => $tool]) }}" data-mc="firms" id="wsMcFirms-{{ $tool }}">
            <b>Kayıtlı firmalardan teklif alayım</b>
            <span>İlçenizdeki onaylı firmalar teklif versin; telefonunuz yalnız seçtiğiniz firmayla paylaşılır.</span>
        </a>
    </div>
</div>
@once
<style>
.ws-mc{margin:0 0 16px;padding:18px;border:1px solid #E3E6EA;border-radius:18px;background:#fff}
.ws-mc-h{font-weight:800;font-size:17px;margin-bottom:10px;color:#101318}
.ws-mc-g{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.ws-mc-o{display:block;border:1.5px solid #E3E6EA;border-radius:14px;padding:14px;text-decoration:none;color:#101318;background:#FAFAFB}
.ws-mc-o:hover{border-color:#FFB800;background:#FFF8E1}
.ws-mc-o b{display:block;font-size:15px;margin-bottom:4px}
.ws-mc-o span{font-size:13px;color:#5B6472;line-height:1.5}
@media (max-width:640px){.ws-mc-g{grid-template-columns:1fr}}
html[data-theme="dark"] .ws-mc{background:#141A24;border-color:#232C3A}
html[data-theme="dark"] .ws-mc-h,html[data-theme="dark"] .ws-mc-o{color:#E6E9EE}
html[data-theme="dark"] .ws-mc-o{background:#0F141D;border-color:#232C3A}
html[data-theme="dark"] .ws-mc-o span{color:#97A1AF}
@media print{.ws-mc{display:none}}
</style>
<script>
document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('.ws-mc-o');
    if (!a) return;
    if (window.wsTrack) wsTrack('market_choice', { choice: a.getAttribute('data-mc') });
    if (a.getAttribute('data-mc') === 'kainta') {
        var box = document.getElementById('budgetConfirmBox');
        if (box) { e.preventDefault(); if (box.style.display !== 'block' && !(document.getElementById('leadBox') || {}).offsetParent) box.style.display = 'block'; box.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        return;
    }
    var v = function (id) { var el = document.getElementById(id); return el ? el.value : ''; };
    var u = new URL(a.href, location.href);
    new URLSearchParams(location.search).forEach(function (val, key) { if (!u.searchParams.has(key)) u.searchParams.set(key, val); });
    if (v('leadMin')) u.searchParams.set('min', v('leadMin'));
    if (v('leadMax')) u.searchParams.set('max', v('leadMax'));
    if (v('leadCalculatedData')) u.searchParams.set('ozet', v('leadCalculatedData'));
    a.href = u.toString();
});
</script>
@endonce
@endif
