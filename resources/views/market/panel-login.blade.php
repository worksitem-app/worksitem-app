@php($title = 'Firma paneli girişi')
@include('market._layout-top')
<main class="mk-wrap" style="max-width:760px">
    <div class="mk-card">
        <h1 style="font-size:24px;margin:0 0 6px">Firma paneli</h1>
        <p style="color:var(--muted);margin:0 0 16px;line-height:1.6">Onaylı firma üyeliğinizdeki cep telefonunuzla giriş yapın; şifre yok, SMS kodu yeterli.</p>
        @if ($errors->any())<div class="mk-err">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('market.panel.verify') }}" class="fm-form" style="grid-template-columns:1fr;border:0;padding:0">
            @csrf
            <div><label class="f" for="pPhone">Cep telefonu</label>
                <div class="row"><input type="tel" id="pPhone" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX" inputmode="tel" maxlength="30" required autocomplete="tel"><button type="button" class="fm-btn o" id="pSend">Kod gönder</button></div>
                <div class="fm-msg" id="pMsg" role="status" aria-live="polite"></div></div>
            <div><label class="f" for="pCode">SMS kodu</label><input type="text" id="pCode" name="code" inputmode="numeric" maxlength="6" placeholder="6 haneli kod" autocomplete="one-time-code" required></div>
            <button class="fm-btn" type="submit">Giriş yap</button>
        </form>
        <p style="margin:16px 0 0;font-size:14px;color:var(--muted)">Üye değil misiniz? <a href="{{ route('firms.page') }}" style="color:inherit;font-weight:700">Ücretsiz firma üyeliği</a></p>
    </div>
    <div style="margin-top:20px"><x-site.why audience="firms" /></div>
</main>
@include('market._otp-script', ['url' => route('market.panel.code'), 'btn' => 'pSend', 'phone' => 'pPhone', 'msg' => 'pMsg', 'code' => 'pCode'])
<x-site.contech-footer />
</body>
</html>
