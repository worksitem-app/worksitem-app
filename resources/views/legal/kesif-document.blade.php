@php
    $isMhs = $doc === 'mhs';
    $personal = (bool) $lead;
@endphp
<x-legal.document
    :title="$isMhs ? 'Keşif Mesafeli Hizmet Sözleşmesi' : 'Keşif Ön Bilgilendirme Formu'"
    :description="$isMhs
        ? 'Kainta İnşaat yerinde keşif hizmeti için mesafeli hizmet sözleşmesi: bedel, randevu, mahsup, cayma hakkı ve sorumluluk.'
        : 'Yerinde keşif siparişinden önce okunması gereken ön bilgilendirme formu: hizmet kapsamı, KDV dahil fiyat, ödeme, cayma hakkı.'"
    :path="$isMhs ? '/legal/kesif-mesafeli-hizmet-sozlesmesi' : '/legal/kesif-on-bilgilendirme-formu'"
    type="Mesafeli satış"
    :version="config('worksitem_inspection.terms_version')"
    effective="12.10.2026"
    :robots="$personal ? 'noindex, nofollow' : 'index, follow'"
    :pdf="route('kesif.contract_pdf')"
>
@if ($personal)
<div class="lg-note"><strong>Size özel kopya.</strong> Bu belgedeki alıcı ve sipariş bilgileri {{ $lead->public_id }} numaralı talebinizden doldurulmuştur. Bilgiler yalnızca sizin tarayıcı oturumunuzda görünür.</div>
@endif
@include($isMhs ? 'legal.kesif._mhs' : 'legal.kesif._obf', ['lead' => $lead])
</x-legal.document>
