<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page['title'] }} Pro Rapor | WORKSİTEM</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @php
        $tl = fn ($v) => '₺'.number_format($v, 0, ',', '.');
        $n1 = fn ($v) => number_format($v, 1, ',', '.');
        $back = '/araclar/'.$e->slug().'?'.http_build_query($query);
        $hasCompare = collect($e->steps())->firstWhere('key', 'quality') !== null;
    @endphp
    <style>
        :root { --ink:#101318; --y:#FFB800; --y-50:#FFF8E1; --muted:#5B6472; --line:#E3E6EA; --bg:#F4F5F7; --paper:#fff; --ok:#16A34A; --err:#B42318; }
        html[data-theme="dark"] { --bg:#0B0F17; --paper:#141A24; --line:#232C3A; --muted:#97A1AF; --y-50:#2A2410; }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--bg); color: var(--ink); margin: 0; -webkit-font-smoothing: antialiased; }
        html[data-theme="dark"] body { color: #E6E9EE; }
        .pr { max-width: 1080px; margin: 0 auto; padding: 32px 16px 64px; display: grid; grid-template-columns: minmax(0, 1fr) 400px; gap: 24px; align-items: start; }
        .card { background: var(--paper); border: 1px solid var(--line); border-radius: 20px; padding: 26px; box-shadow: 0 12px 32px rgba(16,19,24,.06); }
        .crumb { font-size: 13px; color: var(--muted); margin-bottom: 10px; }
        .crumb a { color: inherit; }
        .tag { display: inline-block; font-size: 12px; font-weight: 800; background: var(--y); color: #101318; padding: 4px 10px; border-radius: 999px; }
        h1 { font-size: clamp(24px, 3.2vw, 32px); letter-spacing: -.02em; margin: 12px 0 8px; font-weight: 900; }
        .sub { color: var(--muted); line-height: 1.6; margin: 0 0 18px; }
        .sum { background: #101318; color: #fff; border-radius: 16px; padding: 18px 20px; margin-bottom: 18px; }
        .sum small { color: #C9CED6; }
        .sum .rng { color: var(--y); font-size: 26px; font-weight: 900; margin: 4px 0; }
        .sum .meta { color: #C9CED6; font-size: 14px; }
        ul.inc { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
        ul.inc li { display: grid; grid-template-columns: 26px 1fr; gap: 8px; line-height: 1.5; }
        ul.inc li:before { content: "✓"; width: 22px; height: 22px; border-radius: 50%; background: var(--y-50); color: #8A6200; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: 13px; }
        ul.inc b { display: block; }
        ul.inc span { color: var(--muted); font-size: 14px; }
        form label.f { display: block; font-weight: 700; font-size: 14px; margin: 14px 0 6px; }
        input[type=text], input[type=tel] { width: 100%; padding: 12px 14px; border: 1.5px solid var(--line); border-radius: 12px; font: inherit; font-size: 16px; background: var(--paper); color: inherit; }
        input:focus { outline: none; border-color: var(--y); box-shadow: 0 0 0 3px rgba(255,184,0,.25); }
        .row { display: grid; grid-template-columns: 1fr auto; gap: 8px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 12px; padding: 13px 18px; font: inherit; font-weight: 800; cursor: pointer; text-decoration: none; }
        .btn-y { background: var(--y); color: #101318; width: 100%; font-size: 16px; margin-top: 16px; }
        .btn-y:disabled { opacity: .55; cursor: not-allowed; }
        .btn-o { background: transparent; border: 1.5px solid var(--ink); color: inherit; white-space: nowrap; }
        html[data-theme="dark"] .btn-o { border-color: #E6E9EE; }
        .chk { display: grid; grid-template-columns: 20px 1fr; gap: 10px; font-size: 13.5px; line-height: 1.5; margin-top: 12px; color: var(--muted); }
        .chk input { width: 18px; height: 18px; margin-top: 1px; accent-color: #101318; }
        .chk a { color: inherit; font-weight: 700; }
        .msg { font-size: 13.5px; margin-top: 8px; min-height: 1em; }
        .msg.ok { color: var(--ok); }
        .msg.err, .err { color: var(--err); font-size: 13.5px; margin-top: 6px; }
        .note { font-size: 12.5px; color: var(--muted); line-height: 1.55; margin-top: 14px; }
        .verified { background: #DCFCE7; color: #166534; border-radius: 12px; padding: 12px 14px; font-weight: 700; font-size: 14px; }
        @media (max-width: 880px) { .pr { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="ws-pro">
<main class="pr">
    <section class="card">
        <div class="crumb"><a href="/araclar">Araçlar</a> › <a href="{{ $back }}">{{ $page['title'] }}</a> › Pro rapor</div>
        <span class="tag">Pro rapor · şimdilik ücretsiz</span>
        <h1>{{ $page['title'] }}: detaylı Pro rapor</h1>
        <p class="sub">Hesapladığınız iş için usta ile konuşurken elinizde olacak, logolu ve çok sayfalı bir PDF. Telefonunuzu SMS koduyla doğrulamanız yeterli.</p>

        <div class="sum">
            <small>Hesabınız · {{ \Illuminate\Support\Str::limit($est['summary'], 90) }}</small>
            <div class="rng">{{ $tl($est['low']) }} – {{ $tl($est['high']) }}</div>
            <div class="meta">En olası {{ $tl($est['mid']) }}@if ($est['days']) · {{ $est['days'][0] }}–{{ $est['days'][1] }} iş günü@endif</div>
        </div>

        <ul class="inc">
            @if ($hasCompare)<li><div><b>Ekonomik / orta / premium karşılaştırması</b><span>Aynı iş üç kalite seviyesinde ne tutar?</span></div></li>@endif
            <li><div><b>Yaklaşık malzeme listesi</b><span>Kaç kutu, kaç torba, kaç kova: alışverişe hazır miktarlar.</span></div></li>
            <li><div><b>Gün gün iş programı</b><span>Hazırlıktan teslime aşamalar ve süreleri.</span></div></li>
            <li><div><b>Aşamalı ödeme planı önerisi</b><span>Tamamını peşin vermemeniz için örnek plan.</span></div></li>
            <li><div><b>Usta seçme kontrol listesi</b><span>Teklif, malzeme, garanti ve fatura için kontrol maddeleri.</span></div></li>
        </ul>
        <p class="note">Rapor yaklaşık bir hesaplamadır; teklif değildir, geçerlilik süresi yoktur. Fiyatlar şehre, markaya, ustaya ve güne göre değişir. Kesin fiyat yerinde keşifle belirlenir.</p>
    </section>

    <aside class="card">
        <form method="POST" action="{{ route('tools.kit.pro_download', ['kit' => $e->slug()]) }}" id="proForm" novalidate>
            @csrf
            @foreach ($query as $k => $v)
                @if (is_array($v))
                    @foreach ($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                @else
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            @if ($verified)
                <div class="verified">✓ Telefonunuz doğrulandı. Raporu doğrudan indirebilirsiniz.</div>
                <button type="submit" class="btn btn-y" id="proSubmit">Pro raporu indir (PDF)</button>
            @else
                <h2 style="margin:0 0 4px;font-size:20px">Raporu indirin</h2>
                <p class="sub" style="margin:0">Kod yalnızca telefon doğrulaması içindir.</p>

                <label class="f" for="pName">Ad soyad</label>
                <input type="text" id="pName" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror

                <label class="f" for="pPhone">Cep telefonu</label>
                <div class="row">
                    <input type="tel" id="pPhone" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX" autocomplete="tel" inputmode="tel" maxlength="30" required>
                    <button type="button" class="btn btn-o" id="sendCode">Kod gönder</button>
                </div>
                <div class="msg" id="codeMsg" role="status" aria-live="polite"></div>
                @error('phone')<div class="err">{{ $message }}</div>@enderror

                <label class="f" for="pCode">SMS kodu</label>
                <input type="text" id="pCode" name="code" value="" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" placeholder="6 haneli kod" required>
                @error('code')<div class="err">{{ $message }}</div>@enderror

                <label class="chk"><input type="checkbox" name="kvkk" value="1" required @checked(old('kvkk'))>
                    <span><a href="/legal/kvkk-aydinlatma" target="_blank" rel="noopener">KVKK Aydınlatma Metni</a>'ni okudum. Adım ve telefonum raporun hazırlanması ve doğrulama için işlenir.</span></label>
                @error('kvkk')<div class="err">{{ $message }}</div>@enderror

                <label class="chk"><input type="checkbox" name="call_ok" value="1" @checked(old('call_ok'))>
                    <span>İsteğe bağlı: Kainta İnşaat projem için beni arayabilir.</span></label>

                <button type="submit" class="btn btn-y" id="proSubmit">Doğrula ve Pro raporu indir</button>
                <p class="note">Arama iznini işaretlemezseniz sizi aramayız. Kod {{ (int) config('worksitem_sms.otp.ttl_minutes', 5) }} dakika geçerlidir.</p>
            @endif
        </form>
        <p class="note" style="text-align:center"><a href="{{ $back }}" style="color:inherit">← Hesaplayıcıya dön</a></p>
    </aside>
</main>

@include('partials.ws-track')
<script>
(function () {
    var btn = document.getElementById('sendCode');
    var form = document.getElementById('proForm');
    if (form) {
        form.addEventListener('submit', function () {
            if (window.wsTrack) wsTrack('generate_lead', { tool: @json($e->slug()), lead_source: @json($e->key().'_pro_rapor') });
        });
    }
    if (!btn) return;
    var msg = document.getElementById('codeMsg');
    var token = document.querySelector('meta[name="csrf-token"]').content;
    btn.addEventListener('click', function () {
        var phone = document.getElementById('pPhone').value.trim();
        msg.className = 'msg';
        if (phone.replace(/\D/g, '').length < 10) { msg.className = 'msg err'; msg.textContent = 'Lütfen 05XX XXX XX XX biçiminde yazın.'; return; }
        btn.disabled = true; btn.textContent = 'Gönderiliyor…';
        fetch(@json(route('tools.kit.pro_code', ['kit' => $e->slug()])), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ phone: phone })
        }).then(function (r) { return r.json().catch(function () { return {}; }); }).then(function (j) {
            msg.className = 'msg ' + (j.ok ? 'ok' : 'err');
            msg.textContent = j.message || (j.ok ? 'Kod gönderildi.' : 'Kod gönderilemedi, biraz sonra tekrar deneyin.');
            if (j.ok) { document.getElementById('pCode').focus(); }
            var wait = j.ok ? 60 : 5;
            var t = setInterval(function () {
                wait--; btn.textContent = wait > 0 ? 'Tekrar (' + wait + ')' : 'Kodu tekrar gönder';
                if (wait <= 0) { clearInterval(t); btn.disabled = false; }
            }, 1000);
        }).catch(function () {
            msg.className = 'msg err'; msg.textContent = 'Bağlantı hatası. Tekrar deneyin.';
            btn.disabled = false; btn.textContent = 'Kod gönder';
        });
    });
})();
</script>
</body>
</html>
