@php
    $o = \App\Support\InspectionOffer::orderData($lead);
    $co = config('worksitem_company');
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Keşif Sözleşmesi</title>
<style>
    @page { margin: 26px 34px 46px; }
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { color: #1F2430; font-size: 9.6px; line-height: 1.5; }
    .head td { vertical-align: middle; }
    .head .r { text-align: right; color: #5B6472; font-size: 9px; }
    .band { background: #101318; color: #fff; border-radius: 8px; padding: 12px 16px; margin: 8px 0 12px; }
    .band .t { font-size: 17px; font-weight: bold; }
    .band .s { color: #C9CED6; font-size: 9.5px; margin-top: 3px; }
    .band .y { color: #FFB800; font-weight: bold; }
    h1.doc { font-size: 14px; margin: 14px 0 4px; padding-bottom: 4px; border-bottom: 2px solid #FFB800; page-break-after: avoid; }
    h2 { font-size: 11px; margin: 12px 0 4px; color: #101318; page-break-after: avoid; }
    p { margin: 0 0 6px; }
    ul, ol { margin: 0 0 6px; padding-left: 16px; }
    li { margin-bottom: 3px; }
    a { color: #101318; text-decoration: none; }
    table.lg-kv { width: 100%; border-collapse: collapse; margin: 4px 0 8px; }
    table.lg-kv td { border-bottom: 1px solid #E3E6EA; padding: 4px 6px; vertical-align: top; }
    table.lg-kv td:first-child { width: 32%; color: #5B6472; }
    .lg-note { border-left: 3px solid #FFB800; background: #FFF8E1; padding: 7px 10px; margin: 6px 0 8px; }
    .lg-warn { border-left: 3px solid #DC2626; background: #FEF2F2; padding: 7px 10px; margin: 6px 0 8px; color: #7F1D1D; }
    .lg-field { color: #8b93a0; font-style: italic; border-bottom: 1px dashed #8b93a0; }
    .lg-filled { font-weight: bold; color: #101318; }
    .pb { page-break-before: always; }
    .foot { position: fixed; bottom: -28px; left: 0; right: 0; font-size: 8px; color: #8b93a0; border-top: 1px solid #E3E6EA; padding-top: 4px; }
</style>
</head>
<body>
<table class="head" style="width:100%">
    <tr>
        <td>@if ($logo)<img src="{{ $logo }}" style="height:30px">@else<b style="font-size:16px">worksitem</b>@endif</td>
        <td class="r">Mesafeli satış belgeleri · Sürüm {{ config('worksitem_inspection.terms_version') }}<br>{{ $co['legal_name'] ?? '' }}</td>
    </tr>
</table>
<div class="band">
    <div class="t">Yerinde Keşif Sözleşmesi</div>
    <div class="s">
        @if ($o['filled'])Talep no <span class="y">{{ $o['order_no'] }}</span> · {{ $o['name'] }} · {{ $o['date'] }}@else Şablon — alıcı bilgileri sipariş sırasında doldurulur @endif
        · Toplam <span class="y">{{ \App\Support\InspectionOffer::tl(\App\Support\InspectionOffer::amounts()['total']) }}</span> ({{ \App\Support\InspectionOffer::feeLabel() }})
    </div>
</div>

<h1 class="doc">Bölüm I — Ön Bilgilendirme Formu</h1>
@include('legal.kesif._obf', ['lead' => $lead])

<h1 class="doc pb">Bölüm II — Mesafeli Hizmet Sözleşmesi</h1>
@include('legal.kesif._mhs', ['lead' => $lead])

<div class="foot">worksitem.com · {{ $co['legal_name'] ?? '' }} · {{ $co['phone'] ?? '' }} · {{ $co['email'] ?? '' }} · Bu belge elektronik ortamda oluşturulmuştur ({{ now()->format('d.m.Y H:i') }}).</div>
</body>
</html>
