<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Ödeme sonucu | Worksitem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body{margin:0;font-family:Inter,system-ui,sans-serif;background:#F5F6F8;color:#101318}
        .box{max-width:520px;margin:48px auto;padding:28px;background:#fff;border:1px solid #E3E6EA;border-radius:18px;text-align:center}
        .ic{font-size:44px}.ok{color:#047857}.no{color:#B42318}
        h1{font-size:22px;margin:10px 0 6px}p{color:#5B6472;line-height:1.6}
        .meta{display:grid;grid-template-columns:auto 1fr;gap:6px 14px;text-align:left;font-size:14px;margin:18px 0;padding:14px;border-radius:12px;background:#F9FAFB}
        .meta b{color:#101318}.btn{display:inline-block;margin-top:8px;padding:12px 18px;border-radius:12px;background:#FFB800;color:#101318;font-weight:800;text-decoration:none}
    </style>
</head>
<body>
<x-site.contech-header />
<div class="box">
    <img src="/brand/worksitem-logo.png" alt="Worksitem" height="30">
    @if ($order->isPaid())
        <div class="ic ok">✓</div>
        <h1>Ödemeniz alındı</h1>
        <p>@if ($order->kind === 'credits'){{ number_format($order->credits, 0, ',', '.') }} yapay zekâ kredisi hesabınıza yüklendi.@else Siparişiniz tamamlandı.@endif</p>
    @else
        <div class="ic no">!</div>
        <h1>Ödeme tamamlanmadı</h1>
        <p>{{ $order->error ?: 'Kart bilgilerinizi kontrol edip yeniden deneyin.' }} Kartınızdan tutar çekildiyse WhatsApp destek hattına yazın: {{ config('worksitem_inspection.phone') }}</p>
    @endif
    <div class="meta">
        <span>Sipariş no</span><b>{{ $order->public_id }}</b>
        <span>Ürün</span><b>{{ $order->title }}</b>
        <span>Tutar</span><b>{{ number_format($order->amount, 2, ',', '.') }} TL (KDV dahil)</b>
        @if ($order->paid_at)<span>Tarih</span><b>{{ $order->paid_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</b>@endif
    </div>
    <a class="btn" href="/araclar">Araçlara dön</a>
</div>
</body>
</html>
