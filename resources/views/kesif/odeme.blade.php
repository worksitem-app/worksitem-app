<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keşif Siparişiniz | WORKSİTEM</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @php
        $tl = fn ($v) => \App\Support\InspectionOffer::tl($v);
        $havale = $method === 'havale';
        $iban = $company['iban'] ?? null;
        $early = (bool) ($lead->inspection_data['consents']['early_performance'] ?? false);
        $paid = $lead->inspection_status === 'odendi';
        $online = $iyzico && ! $havale && ! $paid;
    @endphp
    <style>
        :root { --ink:#101318; --y:#FFB800; --y-50:#FFF8E1; --muted:#5B6472; --line:#E3E6EA; --bg:#F4F5F7; --paper:#fff; --ok:#16A34A; }
        html[data-theme="dark"] { --bg:#0B0F17; --paper:#141A24; --line:#232C3A; --muted:#97A1AF; --y-50:#2A2410; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; }
        html[data-theme="dark"] body { color: #E6E9EE; }
        .od { max-width: 1120px; margin: 0 auto; padding: 36px 20px 64px; display: grid; grid-template-columns: minmax(0, 1fr) 380px; gap: 24px; align-items: start; }
        .card { background: var(--paper); border: 1px solid var(--line); border-radius: 20px; padding: 28px; box-shadow: 0 12px 32px rgba(16,19,24,.06); }
        .chip { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; background: #DCFCE7; color: #166534; padding: 5px 10px; border-radius: 999px; }
        h1 { font-size: clamp(24px, 3vw, 30px); letter-spacing: -.02em; margin: 14px 0 6px; font-weight: 900; }
        .sub { color: var(--muted); margin: 0 0 20px; line-height: 1.6; }
        ol.steps { list-style: none; padding: 0; margin: 0 0 22px; }
        ol.steps li { display: grid; grid-template-columns: 34px 1fr; gap: 12px; padding: 0 0 18px; position: relative; }
        ol.steps li:not(:last-child):before { content: ""; position: absolute; left: 16px; top: 34px; bottom: 2px; width: 2px; background: var(--line); }
        .n { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; background: var(--bg); border: 2px solid var(--line); color: var(--muted); }
        .n.ok { background: var(--ok); border-color: var(--ok); color: #fff; }
        .n.now { border-color: var(--y); background: var(--y-50); color: var(--ink); }
        ol.steps b { display: block; margin: 6px 0 2px; }
        ol.steps span { color: var(--muted); font-size: 14px; line-height: 1.55; }
        .iban { border: 1.5px dashed var(--line); border-radius: 14px; padding: 14px 16px; margin: 0 0 16px; }
        .iban dl { margin: 0; display: grid; grid-template-columns: 110px 1fr auto; gap: 8px 10px; align-items: center; font-size: 14px; }
        .iban dt { color: var(--muted); font-weight: 600; }
        .iban dd { margin: 0; font-weight: 700; word-break: break-all; }
        .iban dd.mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .cp { border: 1px solid var(--line); background: transparent; color: inherit; border-radius: 8px; padding: 5px 9px; font: 700 12px Inter, sans-serif; cursor: pointer; }
        .btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 15px 18px; border-radius: 12px; font: 800 15px Inter, sans-serif; text-decoration: none; margin-top: 10px; border: 0; cursor: pointer; }
        .wa { background: #25D366; color: #08240f; }
        .tel { background: #101318; color: #fff; }
        html[data-theme="dark"] .tel { background: #fff; color: #101318; }
        .ghost { background: transparent; color: inherit; border: 1.5px solid var(--line); }
        .note { margin-top: 18px; font-size: 13px; color: var(--muted); line-height: 1.6; background: var(--y-50); border-radius: 12px; padding: 12px 14px; }
        .note a { color: inherit; font-weight: 700; }
        .sum { position: sticky; top: 88px; }
        .sum h2 { font-size: 13px; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin: 0 0 10px; }
        .sum table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .sum td { padding: 8px 0; border-bottom: 1px dashed var(--line); vertical-align: top; }
        .sum td:last-child { text-align: right; font-weight: 700; }
        .sum tr.t td { border-bottom: 0; font-size: 17px; font-weight: 900; }
        .docs { margin-top: 16px; display: grid; gap: 8px; }
        .docs a { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 10px; color: inherit; text-decoration: none; font-size: 14px; font-weight: 600; }
        .docs a:hover { border-color: var(--ink); }
        .msg { border-radius: 12px; padding: 12px 14px; margin: 0 0 16px; font-size: 14px; font-weight: 600; line-height: 1.5; }
        .msg.ok { background: #DCFCE7; color: #14532D; border: 1px solid #86EFAC; }
        .msg.err { background: #FEF2F2; color: #7F1D1D; border: 1px solid #FCA5A5; }
        .pay { background: #FFB800; color: #101318; font-size: 16px; }
        .pay:hover { background: #E5A500; }
        .toast { position: fixed; left: 50%; bottom: 24px; transform: translateX(-50%); background: #101318; color: #fff; padding: 10px 16px; border-radius: 10px; font-size: 14px; opacity: 0; transition: .2s; pointer-events: none; }
        .toast.on { opacity: 1; }
        @media (max-width: 920px) { .od { grid-template-columns: 1fr; padding: 20px 16px 48px; } .sum { position: static; } .card { padding: 22px 18px; } .iban dl { grid-template-columns: 90px 1fr auto; } }
    </style>
@include('partials.ws-track')
@if (session('iyzico_ok'))
<script>wsTrack('purchase', { transaction_id: @json($lead->public_id), currency: 'TRY', value: {{ $amounts['total'] }}, items: [{ item_id: 'kesif', item_name: 'Yerinde keşif', price: {{ $amounts['total'] }}, quantity: 1 }] });</script>
@endif
</head>
<body>
    <x-site.contech-header />
    <main class="od" data-kainta-auto-link="off">
    <section class="card" aria-labelledby="odTitle">
        <span class="chip">✓ {{ $paid ? 'Ödeme alındı' : 'Sipariş alındı' }} · {{ $lead->displayNo() }}</span>
        <h1 id="odTitle">{{ $paid ? 'Keşif ödemeniz alındı' : ($havale ? 'Havale / EFT ile ödeme' : 'Kartla güvenli ödeme') }}</h1>
        @if (session('iyzico_ok') || $paid)
            <p class="msg ok" role="status">Teşekkürler, {{ $tl($amounts['total']) }} ödemeniz alındı. Kainta İnşaat mühendisi randevu için sizi arayacak; SMS ile de bilgilendirildiniz.</p>
        @elseif (session('iyzico_error'))
            <p class="msg err" role="alert">{{ session('iyzico_error') }}</p>
        @endif
        <p class="sub">
            @if ($paid)
                Faturanız {{ config('worksitem_inspection.provider') }} tarafından düzenlenip e-posta adresinize gönderilecek.
            @elseif ($online)
                Ödeme, iyzico'nun güvenli sayfasında 3D Secure ile yapılır; kart bilgileriniz bize ulaşmaz.
            @elseif ($havale)
                Aşağıdaki hesaba <strong>{{ $tl($amounts['total']) }}</strong> gönderin ve açıklamaya talep numaranızı yazın. Dekontu WhatsApp'tan iletmeniz ödemenin hızlı eşleşmesini sağlar.
            @else
                Güvenli kart ödeme bağlantınız (iyzico, 3D Secure) {{ config('worksitem_inspection.call_center_label') }} tarafından WhatsApp ile gönderilir. Beklemek istemezseniz hemen yazın.
            @endif
        </p>

        @if ($havale && ! $paid)
            <div class="iban">
                @if ($iban)
                <dl>
                    <dt>Alıcı</dt><dd>{{ $company['iban_holder'] ?: ($company['legal_name'] ?? '') }}</dd><button type="button" class="cp" data-copy="{{ $company['iban_holder'] ?: ($company['legal_name'] ?? '') }}">Kopyala</button>
                    @if (! empty($company['bank_name']))<dt>Banka</dt><dd>{{ $company['bank_name'] }}</dd><span></span>@endif
                    <dt>IBAN</dt><dd class="mono">{{ $iban }}</dd><button type="button" class="cp" data-copy="{{ preg_replace('/\s+/', '', $iban) }}">Kopyala</button>
                    <dt>Tutar</dt><dd>{{ $tl($amounts['total']) }}</dd><span></span>
                    <dt>Açıklama</dt><dd class="mono">{{ $lead->displayNo() }}</dd><button type="button" class="cp" data-copy="{{ $lead->displayNo() }}">Kopyala</button>
                </dl>
                @else
                <p style="margin:0;font-size:14px">IBAN bilgisi WhatsApp'tan iletilir. Açıklamaya talep numaranızı (<strong>{{ $lead->displayNo() }}</strong>) yazmayı unutmayın.</p>
                @endif
            </div>
        @endif

        <ol class="steps">
            <li><div class="n ok">✓</div><div><b>Sözleşme onaylandı</b><span>{{ optional($lead->inspection_accepted_at)->format('d.m.Y H:i') }} · {{ config("worksitem_inspection.payment_methods.$method.label") }}</span></div></li>
            @if ($paid)
            <li><div class="n ok">✓</div><div><b>Ödeme alındı</b><span>{{ optional($lead->inspection_paid_at)->format('d.m.Y H:i') }}</span></div></li>
            @else
            <li><div class="n now">2</div><div><b>{{ $havale ? 'Havale / EFT ve dekont' : ($online ? 'Kartla ödeme (iyzico)' : 'Kart ödeme bağlantısı') }}</b><span>{{ $havale ? 'Ödemeniz hesabımıza geçince size WhatsApp ile onay gönderilir.' : ($online ? 'Aşağıdaki düğmeyle güvenli ödeme sayfasına geçin.' : 'Bağlantı çağrı merkezi çalışma saatlerinde en kısa sürede gönderilir.') }}</span></div></li>
            @endif
            <li><div class="n {{ $paid ? 'now' : '' }}">3</div><div><b>Mühendis sizi arar</b><span>Ödeme onaylanınca Kainta İnşaat mühendisi arayıp keşif randevusunu birlikte belirler{{ $early ? '' : ' (cayma süresi içinde keşif talep etmediğiniz için keşif, 14 günlük süre dolduktan sonra yapılır)' }}.</span></div></li>
        </ol>

        @if ($online)
            <form method="POST" action="{{ route('kesif.iyzico.start') }}">@csrf<button type="submit" class="btn pay">🔒 Kartla güvenli öde · {{ $tl($amounts['total']) }}</button></form>
        @elseif (! $paid)
            <a class="btn wa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener" data-event="kesif_whatsapp">{{ $havale ? 'Dekontu WhatsApp\'tan gönderin' : 'Ödeme bağlantısını WhatsApp\'tan isteyin' }}</a>
        @endif
        <a class="btn tel" href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" data-event="kesif_call">Çağrı merkezi: {{ $phone }}</a>

        <p class="note">
            Hizmet {{ config('worksitem_inspection.provider') }} tarafından verilir ve faturalandırılır. {{ \App\Support\InspectionOffer::creditDays() }} gün içinde sözleşme imzalarsanız keşif bedeli iş bedelinden düşülür.
            Keşif yapılmadan önce 14 gün içinde cayma hakkınızı kullanabilirsiniz: <a href="mailto:{{ config('worksitem_inspection.cancel_email') }}">{{ config('worksitem_inspection.cancel_email') }}</a>.
        </p>
    </section>

    <aside class="card sum" aria-label="Sipariş özeti">
        <h2>Sipariş özeti</h2>
        <table>
            <tr><td>Yerinde keşif ve keşif raporu</td><td>{{ $tl($amounts['net']) }}</td></tr>
            <tr><td>KDV (%{{ \App\Support\InspectionOffer::vatRate() }})</td><td>{{ $tl($amounts['vat']) }}</td></tr>
            <tr class="t"><td>Toplam</td><td>{{ $tl($amounts['total']) }}</td></tr>
        </table>
        <div class="docs">
            <a href="{{ route('kesif.contract_pdf') }}">📄 Sözleşmenizi indirin (PDF) <span>↓</span></a>
            <a href="{{ route('legal.kesif_obf') }}" target="_blank" rel="noopener">Ön Bilgilendirme Formu <span>↗</span></a>
            <a href="{{ route('legal.kesif_mhs') }}" target="_blank" rel="noopener">Mesafeli Hizmet Sözleşmesi <span>↗</span></a>
        </div>
    </aside>
    </main>
    <div class="toast" role="status" aria-live="polite" data-toast></div>
    <x-site.contech-footer />
    <script>
    document.querySelectorAll('[data-copy]').forEach(function (b) {
        b.addEventListener('click', function () {
            var t = document.querySelector('[data-toast]'), v = b.getAttribute('data-copy');
            function say(m) { t.textContent = m; t.classList.add('on'); setTimeout(function () { t.classList.remove('on'); }, 2000); }
            (navigator.clipboard && window.isSecureContext ? navigator.clipboard.writeText(v) : Promise.reject()).then(function () { say('✓ Kopyalandı'); }, function () { window.prompt('Kopyalayın:', v); });
        });
    });
    </script>
</body>
</html>
