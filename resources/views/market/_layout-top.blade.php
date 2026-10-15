{{-- Pazaryeri sayfaları için ortak üst kısım. Değişkenler: $title, $robots (varsayılan noindex) --}}
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | WORKSİTEM</title>
    <meta name="robots" content="{{ $robots ?? 'noindex, nofollow' }}">
    @isset($description)<meta name="description" content="{{ $description }}">@endisset
    @include('firmalar._style')
    <style>
        .mk-wrap { max-width:960px; margin:0 auto; padding:28px 16px 56px; }
        .mk-card { background:var(--paper); border:1px solid var(--line); border-radius:18px; padding:22px; margin-bottom:16px; }
        .mk-card h2 { margin:0 0 6px; font-size:20px; }
        .mk-kv { display:grid; grid-template-columns:auto 1fr; gap:6px 16px; font-size:14.5px; }
        .mk-kv span { color:var(--muted); }
        .mk-sum { background:#101318; color:#fff; border-radius:16px; padding:18px 20px; margin-bottom:16px; }
        .mk-sum small { color:#C9CED6; } .mk-sum b { color:var(--y); font-size:24px; display:block; margin:4px 0; }
        .mk-note { background:var(--y-50); border-radius:14px; padding:14px 16px; font-size:14px; line-height:1.6; margin-bottom:16px; }
        .mk-ok { background:#DCFCE7; color:#14532D; border:1px solid #86EFAC; padding:14px 16px; border-radius:12px; font-weight:600; margin-bottom:14px; line-height:1.55; }
        .mk-err { background:#FEE2E2; color:#7F1D1D; border:1px solid #FCA5A5; padding:14px 16px; border-radius:12px; margin-bottom:14px; }
        .mk-offer { border:1.5px solid var(--line); border-radius:16px; padding:18px; margin-bottom:12px; background:var(--paper); }
        .mk-offer.on { border-color:var(--y); box-shadow:0 0 0 3px rgba(255,184,0,.18); }
        .mk-offer .amt { font-size:26px; font-weight:900; }
        .mk-offer .head { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:flex-start; }
        .mk-tag { display:inline-block; font-size:12px; font-weight:700; border-radius:999px; padding:3px 9px; background:#EEF0F3; color:#374151; margin:2px 4px 2px 0; }
        .mk-tag.g { background:#DCFCE7; color:#166534; } .mk-tag.y { background:var(--y); color:#101318; } .mk-tag.r { background:#FEE2E2; color:#991B1B; }
        details.mk-acc summary { cursor:pointer; list-style:none; } details.mk-acc summary::-webkit-details-marker { display:none; }
        .mk-table { width:100%; border-collapse:collapse; font-size:14px; }
        .mk-table td, .mk-table th { border-bottom:1px solid var(--line); padding:10px 8px; text-align:left; vertical-align:top; }
        .mk-table th { font-size:12px; text-transform:uppercase; color:var(--muted); letter-spacing:.04em; }
        .mk-row a { color:inherit; font-weight:700; }
        @media (max-width:640px) { .mk-table thead { display:none; } .mk-table td { display:block; border:0; padding:4px 0; } .mk-table tr { display:block; border-bottom:1px solid var(--line); padding:10px 0; } }
    </style>
</head>
<body>
<x-site.contech-header />
