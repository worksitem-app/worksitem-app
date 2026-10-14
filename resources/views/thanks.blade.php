<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talebiniz Alındı | WORKSİTEM</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @php
        $kesifLead = session('kesif_lead') ? \App\Models\Lead::query()->where('public_id', session('kesif_lead'))->first() : null;
        $amounts = \App\Support\InspectionOffer::amounts();
        $tl = fn ($v) => \App\Support\InspectionOffer::tl($v);
        $methods = (array) config('worksitem_inspection.payment_methods');
        $consents = \App\Support\InspectionOffer::consentTexts();
        $orderErrors = $errors->hasAny(['odeme', 'fatura', 'fatura_unvan', 'vergi_dairesi', 'vergi_no', 'kesif_onay', 'email']);
        $wa = \App\Support\InspectionOffer::whatsappUrl('Merhaba, Worksitem üzerinden teklif talebi bıraktım'.($kesifLead ? ' (talep no: '.$kesifLead->displayNo().')' : '').'.');
    @endphp
    <style>
        :root { --ink:#101318; --y:#FFB800; --y-50:#FFF8E1; --muted:#5B6472; --line:#E3E6EA; --bg:#F4F5F7; --paper:#fff; --ok:#16A34A; --err:#B91C1C; }
        html[data-theme="dark"] { --bg:#0B0F17; --paper:#141A24; --line:#232C3A; --muted:#97A1AF; --y-50:#2A2410; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; }
        html[data-theme="dark"] body { color: #E6E9EE; }
        .ty { max-width: 1120px; margin: 0 auto; padding: 40px 20px 64px; }
        .ty-grid { display: grid; gap: 24px; align-items: start; }
        .ty-grid.has-offer { grid-template-columns: minmax(0, 1fr) 420px; }
        .ty-grid:not(.has-offer) { max-width: 620px; margin: 0 auto; }
        .ty-card { background: var(--paper); border: 1px solid var(--line); border-radius: 20px; padding: 32px; box-shadow: 0 12px 32px rgba(16,19,24,.06); }
        .ty-ok { width: 56px; height: 56px; border-radius: 16px; background: #DCFCE7; color: var(--ok); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 900; }
        .ty-card h1 { font-size: clamp(24px, 3vw, 32px); font-weight: 900; letter-spacing: -.02em; margin: 18px 0 8px; }
        .ty-card > p { color: var(--muted); font-size: 15.5px; line-height: 1.65; margin: 0 0 18px; }
        .ty-no { display: inline-flex; gap: 8px; align-items: center; font-size: 13px; background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: 6px 10px; margin-bottom: 22px; }
        .ty-no b { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; letter-spacing: .02em; }
        .ty-steps { list-style: none; margin: 0 0 26px; padding: 0; position: relative; }
        .ty-steps li { display: grid; grid-template-columns: 34px 1fr; gap: 12px; padding: 0 0 18px; position: relative; }
        .ty-steps li:not(:last-child):before { content: ""; position: absolute; left: 16px; top: 34px; bottom: 2px; width: 2px; background: var(--line); }
        .ty-steps .n { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; background: var(--bg); border: 2px solid var(--line); color: var(--muted); }
        .ty-steps .n.done { background: var(--ok); border-color: var(--ok); color: #fff; }
        .ty-steps .n.now { border-color: var(--y); color: var(--ink); background: var(--y-50); }
        .ty-steps b { display: block; font-size: 15px; margin: 6px 0 2px; }
        .ty-steps span { color: var(--muted); font-size: 14px; line-height: 1.5; }
        .ty-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 22px; border-radius: 12px; font: 800 15px Inter, sans-serif; text-decoration: none; border: 0; cursor: pointer; transition: .15s; }
        .btn-y { background: var(--y); color: #101318; }
        .btn-y:hover { background: #E5A500; transform: translateY(-1px); }
        .btn-o { background: transparent; color: inherit; border: 1.5px solid var(--line); }
        .btn-o:hover { border-color: var(--ink); }
        .btn-wa { background: #25D366; color: #08240F; }

        .kesif { position: sticky; top: 88px; background: #101318; color: #fff; border-radius: 20px; padding: 26px 24px; box-shadow: 0 20px 44px rgba(16,19,24,.22); overflow: hidden; }
        .kesif:after { content: ""; position: absolute; right: -60px; top: -60px; width: 180px; height: 180px; border-radius: 50%; background: radial-gradient(circle, rgba(255,184,0,.35), transparent 70%); pointer-events: none; }
        .kesif-tag { display: inline-block; font-size: 12px; font-weight: 800; color: #101318; background: var(--y); padding: 4px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: .03em; }
        .kesif h2 { font-size: 22px; margin: 14px 0 6px; letter-spacing: -.01em; }
        .kesif-price { display: flex; align-items: baseline; gap: 8px; margin: 4px 0 2px; }
        .kesif-price strong { color: var(--y); font-size: 32px; font-weight: 900; letter-spacing: -.02em; }
        .kesif-price span { color: #C9CED6; font-size: 14px; font-weight: 600; }
        .kesif-total { color: #AAB1BC; font-size: 13px; margin: 0 0 16px; }
        .kesif-list { list-style: none; margin: 0 0 20px; padding: 0; }
        .kesif-list li { display: grid; grid-template-columns: 22px 1fr; gap: 8px; color: #DFE3E8; font-size: 14px; line-height: 1.55; margin-bottom: 8px; }
        .kesif-list li:before { content: "✓"; color: var(--y); font-weight: 900; }
        .kesif a { color: var(--y); }
        .kesif .btn-y { width: 100%; font-size: 16px; padding: 16px; }
        .kesif-small { font-size: 12px; color: #AAB1BC; margin: 10px 0 0; line-height: 1.5; text-align: center; }
        .kesif-trust { display: flex; justify-content: center; gap: 14px; margin-top: 12px; font-size: 12px; color: #C9CED6; flex-wrap: wrap; }

        /* Sipariş penceresi */
        dialog.km { border: 0; padding: 0; border-radius: 20px; width: min(560px, calc(100% - 24px)); max-height: calc(100vh - 32px); background: var(--paper); color: inherit; box-shadow: 0 30px 80px rgba(0,0,0,.35); }
        dialog.km::backdrop { background: rgba(16,19,24,.6); backdrop-filter: blur(2px); }
        .km-head { position: sticky; top: 0; z-index: 2; display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); background: var(--paper); }
        .km-head h2 { margin: 0; font-size: 18px; }
        .km-head small { display: block; color: var(--muted); font-size: 12.5px; font-weight: 500; }
        .km-x { width: 38px; height: 38px; border-radius: 10px; border: 1px solid var(--line); background: transparent; color: inherit; font-size: 18px; cursor: pointer; }
        .km-body { padding: 18px 22px 8px; }
        .km-sum { width: 100%; border-collapse: collapse; font-size: 14px; margin: 0 0 6px; }
        .km-sum td { padding: 6px 0; border-bottom: 1px dashed var(--line); }
        .km-sum td:last-child { text-align: right; font-weight: 700; white-space: nowrap; }
        .km-sum tr.t td { border-bottom: 0; font-size: 16px; font-weight: 900; padding-top: 10px; }
        .km-mahsup { font-size: 12.5px; color: var(--muted); background: var(--y-50); border-radius: 10px; padding: 8px 10px; margin: 6px 0 16px; line-height: 1.5; }
        .km-lbl { display: block; font-size: 13px; font-weight: 800; margin: 14px 0 8px; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); }
        .km-opts { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .km-opt { position: relative; display: block; border: 1.5px solid var(--line); border-radius: 14px; padding: 12px 12px 12px 40px; cursor: pointer; font-size: 14px; line-height: 1.35; }
        .km-opt input { position: absolute; left: 13px; top: 14px; width: 18px; height: 18px; accent-color: #101318; margin: 0; }
        .km-opt b { display: block; }
        .km-opt small { color: var(--muted); font-size: 12px; }
        .km-opt em { position: absolute; right: 10px; top: -9px; font-style: normal; font-size: 10.5px; font-weight: 800; background: var(--y); color: #101318; padding: 2px 8px; border-radius: 999px; }
        .km-opt:has(input:checked) { border-color: #101318; box-shadow: 0 0 0 3px rgba(255,184,0,.45); }
        html[data-theme="dark"] .km-opt:has(input:checked) { border-color: var(--y); }
        .km-seg { display: inline-flex; border: 1.5px solid var(--line); border-radius: 12px; padding: 3px; gap: 3px; }
        .km-seg label { padding: 8px 16px; border-radius: 9px; font-size: 14px; font-weight: 700; cursor: pointer; }
        .km-seg input { position: absolute; opacity: 0; pointer-events: none; }
        .km-seg label:has(input:checked) { background: #101318; color: #fff; }
        html[data-theme="dark"] .km-seg label:has(input:checked) { background: var(--y); color: #101318; }
        .km-corp { display: grid; gap: 8px; margin-top: 10px; }
        .km-corp[hidden] { display: none; }
        .km-corp input { width: 100%; padding: 11px 12px; border: 1.5px solid var(--line); border-radius: 10px; font: 500 14px Inter, sans-serif; background: var(--paper); color: inherit; }
        .km-in { width: 100%; padding: 12px 12px; border: 1.5px solid var(--line); border-radius: 10px; font: 500 15px Inter, sans-serif; background: var(--paper); color: inherit; }
        .km-hint { display: block; color: var(--muted); font-size: 12px; margin-top: 5px; }
        .km-check { display: grid; grid-template-columns: 22px 1fr; gap: 10px; align-items: start; font-size: 13px; line-height: 1.55; padding: 12px; border: 1.5px solid var(--line); border-radius: 12px; margin-top: 10px; cursor: pointer; }
        .km-check input { width: 20px; height: 20px; margin: 1px 0 0; accent-color: #101318; }
        .km-check a { color: inherit; font-weight: 700; text-decoration: underline; text-decoration-color: var(--y); text-decoration-thickness: 2px; }
        .km-err { color: var(--err); background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px; padding: 7px 10px; font-size: 13px; font-weight: 600; margin: 6px 0 0; }
        .km-bad { border-color: var(--err) !important; }
        .km-foot { position: sticky; bottom: 0; padding: 14px 22px 18px; background: var(--paper); border-top: 1px solid var(--line); }
        .km-foot .btn { width: 100%; font-size: 16px; padding: 16px; }
        .km-foot p { margin: 8px 0 0; text-align: center; font-size: 12px; color: var(--muted); line-height: 1.45; }

        @media (max-width: 920px) {
            .ty { padding: 20px 16px 48px; }
            .ty-grid.has-offer { grid-template-columns: 1fr; }
            .kesif { position: relative; top: auto; }
            .ty-card { padding: 24px 20px; }
        }
        @media (max-width: 560px) {
            dialog.km { width: 100%; max-width: 100%; max-height: 92vh; margin: auto 0 0; border-radius: 20px 20px 0 0; }
            .km-opts { grid-template-columns: 1fr; }
        }
    </style>

@include('partials.thanks-ads')
@include('partials.ws-track')
@if (session('lead_public_id') && session('google_ads_conversion'))
<script>wsTrack('generate_lead', { currency: 'TRY', lead_source: 'worksitem_tool' });</script>
@endif
</head>
<body>
    <x-site.contech-header />
    <main class="ty">
    <div class="ty-grid {{ $kesifLead ? 'has-offer' : '' }}">
        <section class="ty-card" aria-labelledby="tyTitle">
            <div class="ty-ok" aria-hidden="true">✓</div>
            <h1 id="tyTitle">Talebiniz başarıyla alındı</h1>
            <p>Proje bilgileriniz Kainta İnşaat Uzmanlarına iletildi. Ön değerlendirmeden sonra sizi arayacağız.</p>
            @if ($kesifLead)
                <div class="ty-no">Talep no <b>{{ $kesifLead->displayNo() }}</b></div>
            @endif
            <ol class="ty-steps">
                <li><div class="n done">✓</div><div><b>Talebiniz alındı</b><span>Hesaplama ve iletişim bilgileriniz kaydedildi.</span></div></li>
                <li><div class="n now">2</div><div><b>Uzman ön değerlendirmesi</b><span>Genellikle 1 iş günü içinde telefonla dönüş yapılır.</span></div></li>
                <li><div class="n">3</div><div><b>Yerinde keşif ve kesin teklif</b><span>Ölçüm ve metraj yerinde yapılır, yazılı teklif hazırlanır.</span></div></li>
            </ol>
            <div class="ty-actions">
                <a href="/araclar" class="btn btn-y">Araçlara dön</a>
                <a href="{{ $wa }}" class="btn btn-o" target="_blank" rel="noopener">WhatsApp'tan yazın</a>
            </div>
        </section>

        @if ($kesifLead)
        <aside class="kesif" id="kesif" aria-labelledby="kesifTitle" data-kainta-auto-link="off">
            <span class="kesif-tag">Kesin fiyat için</span>
            <h2 id="kesifTitle">Yerinde keşif isteyin</h2>
            <div class="kesif-price"><strong>{{ \App\Support\InspectionOffer::tl(\App\Support\InspectionOffer::fee()) }}</strong><span>+ KDV</span></div>
            <p class="kesif-total">KDV dahil toplam {{ $tl($amounts['total']) }}</p>
            <ul class="kesif-list">
                <li><span>Kainta İnşaat Uzmanı yerinde ölçer; kesin metraj ve yazılı teklif hazırlar.</span></li>
                <li><span>{{ \App\Support\InspectionOffer::creditDays() }} gün içinde Kainta İnşaat ile sözleşme imzalarsanız bedel iş bedelinden <strong>düşülür</strong>.</span></li>
                <li><span>Keşif raporu her durumda size kalır.</span></li>
            </ul>
            @if ($kesifLead->inspection_status)
                <a class="btn btn-y" href="{{ route('kesif.payment') }}">Keşif siparişinizi görüntüleyin →</a>
            @else
                <button type="button" class="btn btn-y" data-km-open aria-haspopup="dialog" aria-controls="kesifModal">Keşif bedelini kabul ediyorum</button>
                <p class="kesif-small">Bir sonraki adımda ödeme yöntemini seçer ve sözleşmeyi onaylarsınız.</p>
            @endif
            <div class="kesif-trust"><span>🔒 iyzico 3D Secure</span><span>🧾 Faturalı</span><span>↩️ 14 gün cayma</span></div>
        </aside>
        @endif
    </div>
    </main>

    @if ($kesifLead && ! $kesifLead->inspection_status)
    <dialog class="km" id="kesifModal" data-kainta-auto-link="off" aria-labelledby="kmTitle" @if ($orderErrors) data-open-on-load @endif>
        <form method="POST" action="{{ route('kesif.accept') }}" novalidate data-km-form>
            @csrf
            <div class="km-head">
                <h2 id="kmTitle">Yerinde keşif siparişi <small>Talep no {{ $kesifLead->displayNo() }}</small></h2>
                <button type="button" class="km-x" data-km-close aria-label="Kapat">✕</button>
            </div>
            <div class="km-body">
                <table class="km-sum">
                    <tr><td>Yerinde keşif ve keşif raporu</td><td>{{ $tl($amounts['net']) }}</td></tr>
                    <tr><td>KDV (%{{ \App\Support\InspectionOffer::vatRate() }})</td><td>{{ $tl($amounts['vat']) }}</td></tr>
                    <tr class="t"><td>Ödenecek toplam</td><td>{{ $tl($amounts['total']) }}</td></tr>
                </table>
                <p class="km-mahsup"><x-inspection-fee-note short /></p>

                <span class="km-lbl" id="kmPay">Ödeme yöntemi</span>
                <div class="km-opts" role="radiogroup" aria-labelledby="kmPay">
                    @foreach ($methods as $key => $m)
                        <label class="km-opt">
                            <input type="radio" name="odeme" value="{{ $key }}" @checked(old('odeme', 'kart') === $key)>
                            @if (! empty($m['badge']))<em>{{ $m['badge'] }}</em>@endif
                            <b>{{ $m['label'] }}</b><small>{{ $m['hint'] }}</small>
                        </label>
                    @endforeach
                </div>
                @error('odeme')<p class="km-err" role="alert">{{ $message }}</p>@enderror

                <span class="km-lbl" id="kmInv">Fatura</span>
                <div class="km-seg" role="radiogroup" aria-labelledby="kmInv">
                    <label><input type="radio" name="fatura" value="bireysel" @checked(old('fatura', 'bireysel') === 'bireysel')>Bireysel</label>
                    <label><input type="radio" name="fatura" value="kurumsal" @checked(old('fatura') === 'kurumsal')>Kurumsal</label>
                </div>
                <div class="km-corp" data-km-corp @if (old('fatura') !== 'kurumsal') hidden @endif>
                    <input type="text" name="fatura_unvan" value="{{ old('fatura_unvan') }}" placeholder="Firma unvanı" maxlength="200" autocomplete="organization" @class(['km-bad' => $errors->has('fatura_unvan')])>
                    <input type="text" name="vergi_dairesi" value="{{ old('vergi_dairesi') }}" placeholder="Vergi dairesi" maxlength="100" @class(['km-bad' => $errors->has('vergi_dairesi')])>
                    <input type="text" name="vergi_no" value="{{ old('vergi_no') }}" placeholder="Vergi no (10 hane)" inputmode="numeric" maxlength="11" @class(['km-bad' => $errors->has('vergi_no')])>
                    @foreach (['fatura_unvan', 'vergi_dairesi', 'vergi_no'] as $k)@error($k)<p class="km-err" role="alert">{{ $message }}</p>@enderror @endforeach
                </div>

                <span class="km-lbl"><label for="kmEmail">E-posta</label></span>
                <input type="email" id="kmEmail" name="email" class="km-in @error('email') km-bad @enderror" value="{{ old('email', $kesifLead->email) }}" placeholder="ornek@eposta.com" autocomplete="email" inputmode="email" maxlength="150" required>
                <small class="km-hint">Sözleşmenizin kopyası ve faturanız bu adrese gönderilir.</small>
                @error('email')<p class="km-err" role="alert">{{ $message }}</p>@enderror

                <label class="km-check @error('kesif_onay') km-bad @enderror">
                    <input type="checkbox" name="kesif_onay" value="1" @checked(old('kesif_onay'))>
                    <span><a href="{{ route('legal.kesif_obf') }}" target="_blank" rel="noopener">Ön Bilgilendirme Formu</a>'nu ve <a href="{{ route('legal.kesif_mhs') }}" target="_blank" rel="noopener">Mesafeli Hizmet Sözleşmesi</a>'ni okudum, anladım ve kabul ediyorum. <strong>*</strong></span>
                </label>
                @error('kesif_onay')<p class="km-err" role="alert">{{ $message }}</p>@enderror

                <label class="km-check">
                    <input type="checkbox" name="kesif_erken" value="1" @checked(old('kesif_erken'))>
                    <span>Keşfin 14 günlük cayma süresi dolmadan yapılmasını talep ediyorum; keşif yapıldıktan sonra cayma hakkımı kaybedeceğimi biliyorum. <small style="color:var(--muted)">(İsteğe bağlı — işaretlemezseniz keşif 14 gün sonra yapılır.)</small></span>
                </label>
            </div>
            <div class="km-foot">
                <button type="submit" class="btn btn-y">Siparişi Onayla ve Öde · {{ $tl($amounts['total']) }}</button>
                <p>Hizmet {{ config('worksitem_inspection.provider') }} tarafından verilir ve faturalandırılır. Ödeme bir sonraki adımda: kart için güvenli iyzico bağlantısı, havale için IBAN bilgisi.</p>
            </div>
        </form>
    </dialog>
    <script>
    (function () {
        var dlg = document.getElementById('kesifModal');
        if (!dlg) return;
        var form = dlg.querySelector('[data-km-form]');
        function open() {
            if (typeof dlg.showModal === 'function') { dlg.showModal(); } else { dlg.setAttribute('open', ''); }
            document.body.style.overflow = 'hidden';
        }
        function close() { dlg.close ? dlg.close() : dlg.removeAttribute('open'); }
        dlg.addEventListener('close', function () { document.body.style.overflow = ''; });
        var item = { currency: 'TRY', value: {{ $amounts['total'] }}, items: [{ item_id: 'kesif', item_name: 'Yerinde keşif', price: {{ $amounts['total'] }}, quantity: 1 }] };
        document.querySelectorAll('[data-km-open]').forEach(function (b) { b.addEventListener('click', function () { open(); window.wsTrack && wsTrack('begin_checkout', item); }); });
        dlg.querySelector('[data-km-close]').addEventListener('click', close);
        dlg.addEventListener('click', function (e) { if (e.target === dlg) close(); });

        var corp = dlg.querySelector('[data-km-corp]');
        form.querySelectorAll('input[name="fatura"]').forEach(function (r) {
            r.addEventListener('change', function () { corp.hidden = form.fatura.value !== 'kurumsal'; });
        });

        function err(el, msg) {
            var host = el.closest('.km-check') || el;
            host.classList.add('km-bad');
            var p = document.createElement('p'); p.className = 'km-err'; p.setAttribute('role', 'alert'); p.setAttribute('data-js', '');
            p.textContent = msg; host.insertAdjacentElement('afterend', p);
            return host;
        }
        // Tüm hatalar birlikte gösterilir; ilk hatalı alana kaydırılır
        function keep(first, el) { return first || el; }
        form.addEventListener('submit', function (e) {
            form.querySelectorAll('.km-err[data-js]').forEach(function (n) { n.remove(); });
            form.querySelectorAll('.km-bad').forEach(function (n) { n.classList.remove('km-bad'); });
            var first = null;
            if (!form.querySelector('input[name="odeme"]:checked')) first = keep(first, err(form.querySelector('.km-opts'), 'Lütfen ödeme yöntemini seçin.'));
            if (form.fatura.value === 'kurumsal') {
                if (!form.fatura_unvan.value.trim()) first = keep(first, err(form.fatura_unvan, 'Firma unvanını yazın.'));
                if (!form.vergi_dairesi.value.trim()) first = keep(first, err(form.vergi_dairesi, 'Vergi dairesini yazın.'));
                if (!/^\d{10,11}$/.test(form.vergi_no.value.trim())) first = keep(first, err(form.vergi_no, 'Vergi numarası 10 haneli (TC kimlik no ise 11 haneli) olmalıdır.'));
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(form.email.value.trim())) first = keep(first, err(form.email, 'Sözleşme ve fatura için geçerli bir e-posta adresi yazın.'));
            if (!form.kesif_onay.checked) first = keep(first, err(form.kesif_onay, 'Devam etmek için ön bilgilendirme formunu ve sözleşmeyi onaylayın.'));
            if (first) { e.preventDefault(); first.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
            if (window.wsTrack) wsTrack('add_payment_info', Object.assign({ payment_type: form.odeme.value }, item));
            var b = form.querySelector('button[type="submit"]'); b.disabled = true; b.textContent = 'Sipariş kaydediliyor…';
        });

        if (dlg.hasAttribute('data-open-on-load')) open();
    })();
    </script>
    @endif

    <x-site.contech-footer />
<script src="/js/p1-product-events.js" defer></script>
</body>
</html>
