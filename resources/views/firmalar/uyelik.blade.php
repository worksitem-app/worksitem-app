<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ücretsiz Firma Üyeliği: Usta, Müteahhit ve Tedarikçiler | WORKSİTEM</title>
    <meta name="description" content="Usta, tadilat firması, müteahhit, mimar ve yapı malzemesi tedarikçileri için ücretsiz Worksitem firma üyeliği. Maliyetini hesaplayan, işi yaptırmaya hazır kullanıcılara ulaşın.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/firma-uyeligi') }}">
    @include('firmalar._style')
</head>
<body>
<x-site.contech-header />

<header class="fm-hero" data-kainta-auto-link="off">
    <div class="fm-in">
        <span class="fm-chip">Firmalar için · ücretsiz</span>
        <h1>Maliyetini hesaplayan müşteri, işi yapacak firmayı arıyor. O firma siz olun.</h1>
        <p>Worksitem'de her gün banyo, mutfak, boya, seramik ve inşaat maliyeti hesaplanıyor. Firmanızı ücretsiz kaydedin; Kainta İnşaat Uzmanları onayladıktan sonra hizmet verdiğiniz il ve iş kolunda listelenin.</p>
        <a class="fm-btn" href="#kayit">Ücretsiz kaydolun →</a>
        @if ($directory)<a class="fm-btn o" style="color:#fff;margin-left:8px" href="{{ route('firms.index') }}">Firma rehberi</a>@endif
    </div>
</header>

<section class="fm"><div class="fm-in">
    <h2>Nasıl çalışır?</h2>
    <p class="lead">Kayıt 3 dakika sürer. Telefonunuzu SMS koduyla doğrularsınız; başvurunuzu 2 iş günü içinde inceleriz.</p>
    <div class="fm-grid">
        <div class="fm-card"><div class="n">1</div><h3>Firmanızı kaydedin</h3><p>İş kolu, hizmet verdiğiniz iller ve kısa tanıtım. Vergi numarası isteğe bağlıdır; doğrulanan firmalar rozet alır.</p></div>
        <div class="fm-card"><div class="n">2</div><h3>Onaylayalım</h3><p>Kainta İnşaat Uzmanları bilgileri kontrol eder. Uydurma, kopya veya yanıltıcı kayıtlar yayınlanmaz.</p></div>
        <div class="fm-card"><div class="n">3</div><h3>Müşteri size ulaşsın</h3><p>Rehberde ve ilgili hesaplayıcılarda görünürsünüz. Telefon numaranız sayfada açık yazılmaz; arama ve WhatsApp düğmesiyle ulaşılır.</p></div>
    </div>
</div></section>

<section class="fm"><div class="fm-in">
    <div class="fm-grid">
        <div class="fm-card"><h3>Ücretsiz üyelik</h3><p>Firma sayfası, il ve iş kolu filtresinde listelenme, arama/WhatsApp düğmesi, görüntülenme sayısı.</p></div>
        <div class="fm-card" style="border-color:var(--y)"><h3>Öne çıkan firma <span class="fm-badge">{{ number_format((int) config('worksitem_firms.pro_price'), 0, ',', '.') }} TL + KDV / yıl</span></h3><p>Rehberde üst sırada ve rozetli görünür, web sitenize bağlantı verilir. Onaydan sonra isterseniz kartla ödeme bağlantısı göndeririz.</p></div>
        <div class="fm-card"><h3>Marka ve üreticiler</h3><p>Hesaplayıcılarda ürün ve sponsor alanı için <a href="{{ route('sponsor.page') }}" style="color:inherit;font-weight:700">Markalar için</a> sayfasına bakın.</p></div>
    </div>
</div></section>

<section class="fm" id="kayit"><div class="fm-in" style="padding-bottom:48px">
    <h2>Firma kaydı</h2>
    @if (session('firm_ok') === 'exists')
        <div class="fm-ok" role="status">Bu telefon numarasıyla kayıtlı bir firma zaten var. Bilgilerinizi güncellemek için WhatsApp hattımıza yazın: {{ config('worksitem_inspection.phone') }}</div>
    @elseif (session('firm_ok'))
        <div class="fm-ok" role="status">Teşekkürler, başvurunuz alındı. Başvuru no: <b>{{ session('firm_ok') }}</b>. Kainta İnşaat Uzmanları en geç 2 iş günü içinde inceleyip size SMS ile dönecek.</div>
    @endif
    <p class="lead">Yıldızlı alanlar zorunludur.</p>
    <form class="fm-form" method="POST" action="{{ route('firms.register') }}#kayit" novalidate>
        @csrf
        <div><label class="f" for="fName">Firma adı *</label><input type="text" id="fName" name="name" value="{{ old('name') }}" maxlength="150" required autocomplete="organization">@error('name')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fType">Firma türü *</label>
            <select id="fType" name="type" required>
                <option value="">Seçin</option>
                @foreach ($types as $k => $l)<option value="{{ $k }}" @selected(old('type') === $k)>{{ $l }}</option>@endforeach
            </select>@error('type')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div class="full"><label class="f">Hizmet alanları * <small style="font-weight:500;color:var(--muted)">(en fazla 8)</small></label>
            <div class="fm-pills">
                @foreach ($services as $k => $s)<label><input type="checkbox" name="services[]" value="{{ $k }}" @checked(in_array($k, old('services', $preServices), true))> {{ $s[0] }}</label>@endforeach
            </div>@error('services')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div class="full"><label class="f">Hizmet verdiğiniz iller * <small style="font-weight:500;color:var(--muted)">(en fazla 10)</small></label>
            <div class="fm-cities fm-pills">
                @foreach ($cities as $c)<label><input type="checkbox" name="cities[]" value="{{ $c }}" @checked(in_array($c, old('cities', []), true))> {{ $c }}</label>@endforeach
            </div>@error('cities')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div><label class="f" for="fDistrict">İlçe / bölge</label><input type="text" id="fDistrict" name="district" value="{{ old('district') }}" maxlength="100" placeholder="ör. Kadıköy, Ataşehir"></div>
        <div><label class="f" for="fYear">Kuruluş yılı</label><input type="number" id="fYear" name="founded_year" value="{{ old('founded_year') }}" min="1900" max="{{ date('Y') }}">@error('founded_year')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div class="full"><label class="f" for="fDesc">Kısa tanıtım</label><textarea id="fDesc" name="description" rows="4" maxlength="1500" placeholder="Hangi işleri yapıyorsunuz, kaç kişilik ekip, garanti, referanslar…">{{ old('description') }}</textarea></div>

        <div><label class="f" for="fWeb">Web sitesi</label><input type="url" id="fWeb" name="website" value="{{ old('website') }}" maxlength="200" placeholder="https://">@error('website')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fTax">Vergi no / TC kimlik no <small style="font-weight:500;color:var(--muted)">(yayınlanmaz)</small></label><input type="text" id="fTax" name="tax_no" value="{{ old('tax_no') }}" inputmode="numeric" maxlength="11">@error('tax_no')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div><label class="f" for="fContact">Yetkili ad soyad *</label><input type="text" id="fContact" name="contact_name" value="{{ old('contact_name') }}" maxlength="120" required autocomplete="name">@error('contact_name')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fEmail">E-posta</label><input type="email" id="fEmail" name="email" value="{{ old('email') }}" maxlength="150" autocomplete="email">@error('email')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <div><label class="f" for="fPhone">Cep telefonu *</label>
            <div class="row"><input type="tel" id="fPhone" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX" inputmode="tel" maxlength="30" required autocomplete="tel"><button type="button" class="fm-btn o" id="fSend">Kod gönder</button></div>
            <div class="fm-msg" id="fMsg" role="status" aria-live="polite"></div>@error('phone')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fCode">SMS kodu *</label><input type="text" id="fCode" name="code" inputmode="numeric" maxlength="6" placeholder="6 haneli kod" autocomplete="one-time-code" required>@error('code')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <label class="fm-check full"><input type="checkbox" name="terms" value="1" @checked(old('terms'))> <span><a href="{{ route('legal.firma_uyelik') }}" target="_blank" rel="noopener">Firma Üyelik Koşulları</a>'nı okudum ve kabul ediyorum. Verdiğim bilgilerin doğru olduğunu beyan ederim.</span></label>
        @error('terms')<div class="fm-err full">{{ $message }}</div>@enderror
        <label class="fm-check full"><input type="checkbox" name="kvkk" value="1" @checked(old('kvkk'))> <span><a href="/legal/kvkk-aydinlatma" target="_blank" rel="noopener">KVKK Aydınlatma Metni</a>'ni okudum. Firma ve yetkili bilgilerim üyelik başvurusunun değerlendirilmesi ve onaylanırsa firma sayfasının yayınlanması için işlenir.</span></label>
        @error('kvkk')<div class="fm-err full">{{ $message }}</div>@enderror
        <div class="full"><button class="fm-btn" type="submit">Başvuruyu gönder</button></div>
    </form>
</div></section>

<script>
(function () {
    var btn = document.getElementById('fSend'), msg = document.getElementById('fMsg');
    var token = document.querySelector('meta[name="csrf-token"]').content;
    btn.addEventListener('click', function () {
        var phone = document.getElementById('fPhone').value.trim();
        msg.className = 'fm-msg';
        if (phone.replace(/\D/g, '').length < 10) { msg.className = 'fm-msg err'; msg.textContent = 'Lütfen 05XX XXX XX XX biçiminde yazın.'; return; }
        btn.disabled = true; btn.textContent = 'Gönderiliyor…';
        fetch(@json(route('firms.code')), { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ phone: phone }) })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (j) {
                msg.className = 'fm-msg ' + (j.ok ? 'ok' : 'err');
                msg.textContent = j.message || (j.ok ? 'Kod gönderildi.' : 'Kod gönderilemedi, biraz sonra tekrar deneyin.');
                if (j.ok) document.getElementById('fCode').focus();
                var wait = j.ok ? 60 : 5;
                var t = setInterval(function () { wait--; btn.textContent = wait > 0 ? 'Tekrar (' + wait + ')' : 'Kodu tekrar gönder'; if (wait <= 0) { clearInterval(t); btn.disabled = false; } }, 1000);
            }).catch(function () { msg.className = 'fm-msg err'; msg.textContent = 'Bağlantı hatası. Tekrar deneyin.'; btn.disabled = false; btn.textContent = 'Kod gönder'; });
    });
    document.querySelectorAll('.fm-pills').forEach(function (box) {
        var max = box.classList.contains('fm-cities') ? 10 : 8;
        box.addEventListener('change', function () {
            var on = box.querySelectorAll('input:checked').length;
            box.querySelectorAll('input:not(:checked)').forEach(function (i) { i.disabled = on >= max; });
        });
    });
})();
</script>
<x-site.contech-footer />
</body>
</html>
