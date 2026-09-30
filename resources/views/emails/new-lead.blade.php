<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<title>WORKSITEM Yeni Lead</title>
</head>

<body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0"
       style="padding:24px 12px;background:#f3f4f6;">
<tr>
<td align="center">

<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width:680px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">

<tr>
<td style="padding:24px 28px;background:#111827;color:#ffffff;">

<div style="font-size:12px;font-weight:bold;letter-spacing:1px;">
WORKSITEM
</div>

<h1 style="margin:8px 0;font-size:22px;">
Yeni müşteri talebi
</h1>

<div style="font-size:14px;">
{{ $qualityLabel }} · Skor {{ $lead->lead_score }}/100
</div>

</td>
</tr>

<tr>
<td style="padding:26px 28px;">

<table width="100%" cellpadding="8" cellspacing="0"
       style="font-size:14px;border-collapse:collapse;">

<tr>
<td width="35%" style="color:#6b7280;">Müşteri</td>
<td><strong>{{ $lead->name ?: 'Belirtilmedi' }}</strong></td>
</tr>

<tr>
<td style="color:#6b7280;">Telefon</td>
<td>
<a href="tel:+{{ $phoneNormalized }}">
{{ $lead->phone ?: 'Belirtilmedi' }}
</a>
</td>
</tr>

<tr>
<td style="color:#6b7280;">E-posta</td>
<td>
@if($lead->email)
<a href="mailto:{{ $lead->email }}">
{{ $lead->email }}
</a>
@else
Belirtilmedi
@endif
</td>
</tr>

<tr>
<td style="color:#6b7280;">Proje</td>
<td>{{ $lead->project_type ?: 'Belirtilmedi' }}</td>
</tr>

<tr>
<td style="color:#6b7280;">Konum</td>
<td>{{ $location }}</td>
</tr>

<tr>
<td style="color:#6b7280;">Alan</td>
<td>{{ $areaLabel }}</td>
</tr>

<tr>
<td style="color:#6b7280;">Yaklaşık bütçe</td>
<td><strong>{{ $budget }}</strong></td>
</tr>

<tr>
<td style="color:#6b7280;">Başlangıç</td>
<td>{{ $lead->timeline ?: 'Belirtilmedi' }}</td>
</tr>

<tr>
<td style="color:#6b7280;">Kaynak</td>
<td>{{ $lead->source ?: 'Belirtilmedi' }}</td>
</tr>

<tr>
<td style="color:#6b7280;">Lead ID</td>
<td>{{ $lead->public_id }}</td>
</tr>

<tr>
<td style="color:#6b7280;">Tarih</td>
<td>
{{ optional($lead->created_at)->format('d.m.Y H:i:s') }}
</td>
</tr>

</table>

@if($lead->notes)
<div style="margin-top:20px;padding:16px;background:#f9fafb;border-radius:10px;">
<strong>Notlar</strong>

<div style="margin-top:8px;white-space:pre-line;">
{{ $lead->notes }}
</div>
</div>
@endif

<div style="margin-top:24px;">

<a href="https://wa.me/{{ $phoneNormalized }}"
   style="display:inline-block;margin-right:8px;background:#16a34a;color:#ffffff;text-decoration:none;padding:12px 16px;border-radius:8px;font-weight:bold;">
WhatsApp
</a>

<a href="{{ $adminUrl }}"
   style="display:inline-block;background:#111827;color:#ffffff;text-decoration:none;padding:12px 16px;border-radius:8px;font-weight:bold;">
Lead'i Aç
</a>

</div>

</td>
</tr>

<tr>
<td style="padding:18px 28px;background:#f9fafb;color:#6b7280;font-size:11px;">
Bu bildirim WORKSITEM CRM tarafından otomatik oluşturulmuştur.
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
