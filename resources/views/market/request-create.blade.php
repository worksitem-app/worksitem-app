@php
    $title = 'Kayıtlı firmalardan teklif alın';
    $robots = 'noindex, follow';
    $f = fn ($v) => number_format((int) $v, 0, ',', '.');
    $defaultTitle = $toolTitle ? trim(preg_replace('/\s*(Maliyeti|Hesaplayıcı|Hesabı|Planlayıcı)\s*$/u', '', preg_replace('#^2D / 3D #', '', $toolTitle))) : '';
@endphp
@include('market._layout-top')
<header class="fm-hero" data-kainta-auto-link="off"><div class="fm-in">
    <span class="fm-chip">Ücretsiz · bağlayıcı değil</span>
    <h1>Bölgenizdeki onaylı firmalardan teklif alın</h1>
    <p>İşinizi yazın; iş kolunuza ve ilçenize uygun, vergi bilgileri doğrulanmış en fazla {{ (int) config('worksitem_market.max_invites', 5) }} firmaya iletelim. Teklifleri karşılaştırın, istediğinizi seçin. Telefonunuz yalnızca seçtiğiniz firmayla paylaşılır.</p>
</div></header>
<main class="mk-wrap">
    @if ($est['low'] && $est['high'])
        <div class="mk-sum"><small>{{ $toolTitle ?: 'Hesabınız' }}{{ $est['summary'] ? ' · '.$est['summary'] : '' }}</small><b>₺{{ $f($est['low']) }} – ₺{{ $f($est['high']) }}</b><small>Bu aralık firmalara "kullanıcının hesapladığı yaklaşık maliyet" olarak gösterilir; teklifler farklı olabilir.</small></div>
    @endif
    @if ($errors->any())<div class="mk-err" role="alert"><b>Talebiniz gönderilemedi:</b><ul style="margin:6px 0 0;padding-left:18px">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <form class="fm-form" method="POST" action="{{ route('market.request.store') }}" novalidate>
        @csrf
        <input type="hidden" name="tool" value="{{ $tool }}">
        <input type="hidden" name="subservice" value="{{ old('subservice', $subservice) }}">
        <input type="hidden" name="summary" value="{{ old('summary', $est['summary']) }}">
        <input type="hidden" name="est_low" value="{{ old('est_low', $est['low']) }}">
        <input type="hidden" name="est_high" value="{{ old('est_high', $est['high']) }}">

        <h3 class="full fm-sec" style="border:0;padding-top:0;margin-top:0">1. İş</h3>
        <div><label class="f" for="rTrade">İş kolu *</label>
            <select id="rTrade" name="trade" required><option value="">Seçin</option>@foreach ($trades as $k => $t)<option value="{{ $k }}" @selected($trade === $k)>{{ $t['label'] }}</option>@endforeach</select>@error('trade')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="rStart">Ne zaman başlansın? *</label>
            <select id="rStart" name="start_pref">@foreach ($startPrefs as $k => $l)<option value="{{ $k }}" @selected(old('start_pref', 'belirsiz') === $k)>{{ $l }}</option>@endforeach</select></div>
        <div class="full"><label class="f" for="rTitle">İşi bir cümleyle anlatın *</label><input type="text" id="rTitle" name="title" value="{{ old('title', $defaultTitle) }}" maxlength="200" required placeholder="ör. Banyo komple yenileme, yaklaşık 5 m²">@error('title')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div class="full"><label class="f" for="rDesc">Ayrıntılar <small class="o">(isteğe bağlı · telefon, e-posta ve bağlantılar firmalara gösterilmez)</small></label><textarea id="rDesc" name="description" rows="4" maxlength="2000" placeholder="Malzeme tercihi, kat / asansör durumu, mevcut durum…">{{ old('description') }}</textarea></div>

        <h3 class="full fm-sec">2. Yer</h3>
        <div><label class="f" for="rCity">İl *</label><select id="rCity" name="city" required><option value="">Seçin</option>@foreach ($cities as $c)<option value="{{ $c }}" @selected(old('city') === $c)>{{ $c }}</option>@endforeach</select>@error('city')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="rDist">İlçe *</label><input type="text" id="rDist" name="district" value="{{ old('district') }}" maxlength="60" required placeholder="ör. Kadıköy">@error('district')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <h3 class="full fm-sec">3. Siz</h3>
        <div><label class="f" for="rName">Ad soyad *</label><input type="text" id="rName" name="name" value="{{ old('name') }}" maxlength="120" required autocomplete="name">@error('name')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="rEmail">E-posta <small class="o">(isteğe bağlı)</small></label><input type="email" id="rEmail" name="email" value="{{ old('email') }}" maxlength="150" autocomplete="email">@error('email')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="rPhone">Cep telefonu *</label>
            <div class="row"><input type="tel" id="rPhone" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX" inputmode="tel" maxlength="30" required autocomplete="tel"><button type="button" class="fm-btn o" id="rSend">Kod gönder</button></div>
            <div class="fm-msg" id="rMsg" role="status" aria-live="polite"></div>@error('phone')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="rCode">SMS kodu *</label><input type="text" id="rCode" name="code" inputmode="numeric" maxlength="6" placeholder="6 haneli kod" autocomplete="one-time-code" required>@error('code')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div class="full mk-note"><b>Telefonunuz kimseye gösterilmez.</b> Firmalar yalnızca işi, ilçenizi ve hesapladığınız yaklaşık maliyeti görür. Bir teklifi kabul ettiğinizde ve o firma eşleşmeyi onayladığında adınız ve telefonunuz yalnızca o firmayla paylaşılır. Teklif almak ücretsizdir; hiçbir teklifi kabul etmek zorunda değilsiniz.</div>
        <label class="fm-check full"><input type="checkbox" name="terms" value="1" @checked(old('terms'))> <span><a href="{{ route('legal.teklif_alma') }}" target="_blank" rel="noopener">Teklif Alma Hizmeti Koşulları</a>'nı okudum. Worksitem'in yalnızca aracı olduğunu, işi yapacak firma ile sözleşmeyi kendim yapacağımı biliyorum.</span></label>
        @error('terms')<div class="fm-err full">{{ $message }}</div>@enderror
        <label class="fm-check full"><input type="checkbox" name="kvkk" value="1" @checked(old('kvkk'))> <span><a href="/legal/kvkk-aydinlatma" target="_blank" rel="noopener">KVKK Aydınlatma Metni</a>'ni okudum. İş bilgilerim ve ilçem uygun firmalara iletilir; adım ve telefonum yalnız kabul ettiğim firmayla paylaşılır.</span></label>
        @error('kvkk')<div class="fm-err full">{{ $message }}</div>@enderror
        <div class="full"><button class="fm-btn" type="submit">Teklif talebini gönder</button></div>
    </form>
    @if ($tool)
        <p class="lead" style="margin-top:18px">İşi firmalarla tek tek görüşmek yerine Kainta İnşaat Uzmanlarının üstlenmesini mi istersiniz? <a href="/araclar/{{ $tool }}#teklif" style="color:inherit;font-weight:800">Kainta İnşaat'tan keşif ve teklif isteyin →</a></p>
    @endif
</main>
@include('market._otp-script', ['url' => route('market.request.code'), 'btn' => 'rSend', 'phone' => 'rPhone', 'msg' => 'rMsg', 'code' => 'rCode'])
<x-site.contech-footer />
</body>
</html>
