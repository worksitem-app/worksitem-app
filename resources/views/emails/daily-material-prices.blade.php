<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<title>WORKSITEM Günlük Malzeme Fiyatları</title>
</head>

<body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0"
       style="padding:24px 12px;background:#f3f4f6;">
<tr>
<td align="center">

<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width:760px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">

<tr>
<td style="padding:24px 28px;background:#111827;color:#ffffff;">

<div style="font-size:12px;font-weight:bold;letter-spacing:1px;">
WORKSITEM
</div>

<h1 style="margin:8px 0;font-size:22px;">
Günlük Malzeme Fiyatları
</h1>

<div style="font-size:13px;">
{{ $from->format('d.m.Y H:i') }}
—
{{ $to->format('d.m.Y H:i') }}
</div>

</td>
</tr>

<tr>
<td style="padding:24px 28px;">

<table width="100%" cellpadding="8" cellspacing="0"
       style="margin-bottom:20px;background:#f9fafb;border-radius:8px;">

<tr>
<td>
<strong>{{ $rows->count() }}</strong><br>
<span style="font-size:12px;color:#6b7280;">
Yeni fiyat kaydı
</span>
</td>

<td>
<strong>{{ $approvedCount }}</strong><br>
<span style="font-size:12px;color:#6b7280;">
Onaylı
</span>
</td>

<td>
<strong>{{ $reviewCount }}</strong><br>
<span style="font-size:12px;color:#6b7280;">
Kontrol bekliyor
</span>
</td>
</tr>

</table>

@if($rows->isEmpty())

<div style="padding:20px;background:#f9fafb;border-radius:10px;">
Son 24 saatte yeni malzeme fiyatı eklenmedi.
</div>

@else

@foreach($rows as $row)

<div style="padding:18px 0;border-bottom:1px solid #e5e7eb;">

<div style="font-size:16px;font-weight:bold;">
{{ $row->name }}
</div>

<div style="margin-top:7px;font-size:22px;font-weight:bold;">
{{ number_format((float) $row->price, 0, ',', '.') }}
{{ $row->unit }}
</div>

<div style="margin-top:8px;font-size:13px;color:#4b5563;">

Bölge:
{{ $row->region }}

<br>

Kaynak:
{{ $row->source_name ?: 'Belirtilmedi' }}

<br>

Kaynak tarihi:
{{ optional($row->source_date)->format('d.m.Y') ?: 'Belirtilmedi' }}

<br>

KDV:
{{ $row->vat_included ? 'Dahil' : 'Hariç / Belirsiz' }}

<br>

Güven:
{{ $row->confidence_score ?? '—' }}/100

<br>

Durum:
<strong>
@if($row->verification_status === 'approved')
Onaylı
@elseif($row->verification_status === 'needs_review')
Kontrol Bekliyor
@else
{{ $row->verification_status }}
@endif
</strong>

<br>

Kayıt nedeni:
{{ $row->snapshot_reason }}

</div>

@if($row->source_url)

<div style="margin-top:10px;">
<a href="{{ $row->source_url }}"
   style="font-size:13px;">
Kaynağı aç
</a>
</div>

@endif

</div>

@endforeach

@endif

</td>
</tr>

@if (! empty($robot) && count($robot))
<tr>
<td style="padding:18px 28px;border-top:1px solid #e5e7eb;">
<div style="font-size:15px;font-weight:800;color:#111827;margin-bottom:8px;">Fiyat robotu · son 24 saat</div>
<table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;color:#374151;">
@foreach ($robot as $r)
<tr>
<td style="padding:5px 0;border-bottom:1px solid #f3f4f6;">{{ $r->label }} <span style="color:#9ca3af">· {{ $r->tool_slug }}</span></td>
<td style="padding:5px 0;border-bottom:1px solid #f3f4f6;text-align:right;white-space:nowrap;">
@if ($r->proposed_value)
<b style="color:#b45309">Onay bekliyor: {{ \App\Models\PriceRobotItem::fmt($r->proposed_value) }} TL</b>
@elseif ($r->status === 'live')
<b style="color:#047857">{{ \App\Models\PriceRobotItem::fmt($r->value) }} TL</b> <span style="color:#9ca3af">(önce {{ \App\Models\PriceRobotItem::fmt($r->previous_value) }})</span>
@else
<span style="color:#b91c1c">Bulunamadı</span>
@endif
</td>
</tr>
@endforeach
</table>
<div style="margin-top:8px;font-size:12px;color:#6b7280;">Kontrol ve düzeltme: Admin › Fiyat Merkezi › Fiyat Robotu</div>
</td>
</tr>
@endif

<tr>
<td style="padding:17px 28px;background:#f9fafb;color:#6b7280;font-size:11px;">
Bu rapor yalnız son 24 saatte WORKSITEM malzeme fiyat geçmişine eklenen kayıtları içerir.
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
