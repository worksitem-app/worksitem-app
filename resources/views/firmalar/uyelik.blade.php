<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Usta ve Firmalara İş: Ücretsiz Firma Kaydı | Worksitem</title>
    <meta name="description" content="Bölgenizde maliyetini hesaplamış müşteriler firma arıyor. Ücretsiz kayıt, ücretsiz teklif; kurucu firma programında 31.03.2027'ye kadar komisyon yok.">
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
        <p>Worksitem'de her gün banyo, mutfak, boya, seramik ve inşaat maliyeti hesaplanıyor. Firmanızı ücretsiz kaydedin; bölgenizdeki ve iş kolunuzdaki teklif talepleri size SMS ile gelsin.@if (\App\Services\Market\Marketplace::isFree()) Kurucu firma programında {{ \App\Services\Market\Marketplace::freeUntil()->format('d.m.Y') }} tarihine kadar komisyon yok.@else Yalnızca kazandığınız iş için ödeme yaparsınız.@endif</p>
        <a class="fm-btn" href="#kayit" {!! \App\Support\Ab::attr('firm_signup_cta') !!}>{{ \App\Support\Ab::text('firm_signup_cta') }}</a>
        @if ($directory)<a class="fm-btn o" style="color:#fff;margin-left:8px" href="{{ route('firms.index') }}">Firma rehberi</a>@endif
    </div>
</header>

<section class="fm"><div class="fm-in">
    <h2>Nasıl çalışır?</h2>
    <p class="lead">Üyelik ücretsizdir. Kayıt 5 dakika sürer; vergi bilgileriniz ve vergi levhanız Worksitem ekibi tarafından kontrol edilir. Böylece müşteriler yalnızca gerçek ve vergi kaydı olan firmalarla buluşur.</p>
    <div class="fm-grid">
        <div class="fm-card"><div class="n">1</div><h3>İş size gelsin</h3><p>Hizmet verdiğiniz il / ilçe ve iş kolunda bir kullanıcı firmalardan teklif istediğinde size SMS ve e-posta gelir: "Kadıköy'de yaklaşık 106–150 bin TL'lik banyo tadilatı var, teklif vermek ister misiniz?"</p></div>
        <div class="fm-card"><div class="n">2</div><h3>Teklif verin</h3><p>Firma panelinden tutar, süre ve notunuzu yazarsınız. Bir işe en fazla {{ (int) config('worksitem_market.max_invites', 5) }} firma davet edilir; müşteri adı ve telefonu bu aşamada kimseyle paylaşılmaz.</p></div>
        @if (\App\Services\Market\Marketplace::isFree())
        <div class="fm-card"><div class="n">3</div><h3>Kazanın, onaylayın</h3><p>Müşteri teklifinizi kabul ederse panelden onaylarsınız; müşterinin adı ve telefonu açılır, Worksitem aradan çekilir. <b>Kurucu firma programı: {{ \App\Services\Market\Marketplace::freeUntil()->format('d.m.Y') }} tarihine kadar komisyon yok.</b></p></div>
        @else
        <div class="fm-card"><div class="n">3</div><h3>Kazanırsanız ödeyin</h3><p>Müşteri teklifinizi kabul ederse eşleşme bedelini kartla ödersiniz: teklif tutarının %{{ rtrim(rtrim(number_format(config('worksitem_market.fee_rate') * 100, 1, ',', ''), '0'), ',') }}'i (en az {{ number_format(config('worksitem_market.fee_min'), 0, ',', '.') }} TL, en çok {{ number_format(config('worksitem_market.fee_max'), 0, ',', '.') }} TL + KDV). Ödeme sonrası müşterinin adı ve telefonu açılır; Worksitem aradan çekilir.</p></div>
        @endif
    </div>
</div></section>

<section class="fm"><div class="fm-in">
    <div class="fm-grid">
        @if (\App\Services\Market\Marketplace::isFree())
        <div class="fm-card"><h3>Kurucu firma programı</h3><p>{{ \App\Services\Market\Marketplace::freeUntil()->format('d.m.Y') }} tarihine kadar eşleşme bedeli alınmaz. Sonrasında yalnızca kazandığınız iş için ödersiniz: teklifin %{{ rtrim(rtrim(number_format(config('worksitem_market.fee_rate') * 100, 1, ',', ''), '0'), ',') }}'i, en az {{ number_format(config('worksitem_market.fee_min'), 0, ',', '.') }}, en çok {{ number_format(config('worksitem_market.fee_max'), 0, ',', '.') }} TL + KDV. Teklif vermek her zaman ücretsiz.</p></div>
        @else
        <div class="fm-card"><h3>Kaybettiğiniz işe ücret yok</h3><p>Teklif vermek ücretsizdir. Müşteri sizi seçmezse hiçbir şey ödemezsiniz. Müşteriye 72 saat içinde ulaşamazsanız eşleşme bedeli iade edilir.</p></div>
        @endif
        <div class="fm-card" style="border-color:var(--y)"><h3>Öne çıkan firma <span class="fm-badge">{{ number_format((int) config('worksitem_firms.pro_price'), 0, ',', '.') }} TL + KDV / yıl</span></h3><p>Firma rehberinde üst sırada ve rozetli görünürsünüz. Teklif davetlerinde de önceliklisiniz.</p></div>
        <div class="fm-card"><h3>Marka ve üreticiler</h3><p>Hesaplayıcılarda ürün ve sponsor alanı için <a href="{{ route('sponsor.page') }}" style="color:inherit;font-weight:700">Markalar için</a> sayfasına bakın.</p></div>
    </div>
</div></section>

<section class="fm"><div class="fm-in"><x-site.why audience="firms" /></div></section>

<section class="fm" id="kayit"><div class="fm-in" style="padding-bottom:48px">
    <h2>Firma kaydı</h2>
    @if (session('firm_ok') === 'exists')
        <div class="fm-ok" role="status">Bu telefon veya vergi numarasıyla kayıtlı bir firma zaten var. Bilgilerinizi güncellemek için WhatsApp hattımıza yazın: {{ config('worksitem_inspection.phone') }}</div>
    @elseif (session('firm_ok'))
        <div class="fm-ok" role="status">Teşekkürler, başvurunuz alındı. Başvuru no: <b>{{ session('firm_ok') }}</b>. Worksitem ekibi vergi bilgilerinizi en geç 2 iş günü içinde kontrol edip size SMS ile dönecek. Onaydan sonra <a href="{{ route('market.panel.login') }}" style="color:inherit">Firma Paneli</a>'ne telefonunuzla girebilirsiniz.</div>
    @endif
    <p class="lead">Yıldızlı alanlar zorunludur. Vergi bilgileri ve vergi levhası yayınlanmaz; yalnız doğrulama ve fatura için kullanılır.</p>
    <form class="fm-form" method="POST" action="{{ route('firms.register') }}#kayit" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="ref" value="{{ old('ref', $ref) }}">
        <h3 class="full fm-sec">1. Firma ve vergi bilgileri <small>(fatura için)</small></h3>
        <div><label class="f" for="fName">Firma / marka adı *</label><input type="text" id="fName" name="name" value="{{ old('name') }}" maxlength="150" required autocomplete="organization">@error('name')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fType">Firma türü *</label>
            <select id="fType" name="type" required>
                <option value="">Seçin</option>
                @foreach ($types as $k => $l)<option value="{{ $k }}" @selected(old('type') === $k)>{{ $l }}</option>@endforeach
            </select>@error('type')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fLegalType">Vergi türü *</label>
            <select id="fLegalType" name="legal_type" required>
                <option value="sirket" @selected(old('legal_type', 'sirket') === 'sirket')>Şirket (Ltd., A.Ş. …)</option>
                <option value="sahis" @selected(old('legal_type') === 'sahis')>Şahıs işletmesi</option>
            </select></div>
        <div><label class="f" for="fLegal">Unvan (vergi levhasındaki gibi) *</label><input type="text" id="fLegal" name="legal_name" value="{{ old('legal_name') }}" maxlength="200" required>@error('legal_name')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fTax">Vergi kimlik no / TC kimlik no *</label><input type="text" id="fTax" name="tax_no" value="{{ old('tax_no') }}" inputmode="numeric" maxlength="11" required>@error('tax_no')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fTaxOffice">Vergi dairesi *</label><input type="text" id="fTaxOffice" name="tax_office" value="{{ old('tax_office') }}" maxlength="80" required>@error('tax_office')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fEfat">e-Fatura mükellefi misiniz? *</label>
            <select id="fEfat" name="efatura" required>
                <option value="yes" @selected(old('efatura') === 'yes')>Evet, e-Fatura kullanıyorum</option>
                <option value="no" @selected(old('efatura') === 'no')>Hayır (e-Arşiv fatura kesilir)</option>
                <option value="unknown" @selected(old('efatura', 'unknown') === 'unknown')>Bilmiyorum, siz kontrol edin</option>
            </select></div>
        <div><label class="f" for="fMersis">MERSİS no <small class="o">(şirketler için)</small></label><input type="text" id="fMersis" name="mersis_no" value="{{ old('mersis_no') }}" inputmode="numeric" maxlength="16">@error('mersis_no')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fInvCity">Fatura adresi: il *</label>
            <select id="fInvCity" name="invoice_city" required><option value="">Seçin</option>@foreach ($cities as $c)<option value="{{ $c }}" @selected(old('invoice_city') === $c)>{{ $c }}</option>@endforeach</select>@error('invoice_city')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fInvDist">Fatura adresi: ilçe *</label><input type="text" id="fInvDist" name="invoice_district" value="{{ old('invoice_district') }}" maxlength="60" required>@error('invoice_district')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div class="full"><label class="f" for="fInvAddr">Açık adres *</label><input type="text" id="fInvAddr" name="invoice_address" value="{{ old('invoice_address') }}" maxlength="300" required placeholder="Mahalle, cadde/sokak, no">@error('invoice_address')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fKep">KEP adresi</label><input type="email" id="fKep" name="kep" value="{{ old('kep') }}" maxlength="150">@error('kep')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fDoc">Vergi levhası * <small class="o">(PDF / JPG / PNG, en çok 5 MB · yayınlanmaz)</small></label><input type="file" id="fDoc" name="tax_doc" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" required>@error('tax_doc')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <h3 class="full fm-sec">2. İş kolları <small>(en fazla {{ (int) config('worksitem_firms.max_trades') }}; alt hizmet seçimi isteğe bağlı)</small></h3>
        <div class="full fm-trades">
            @foreach ($trades as $k => $t)
                @php($on = in_array($k, old('trades', $preTrades), true))
                <details class="fm-trade" @if ($on) open @endif>
                    <summary><label><input type="checkbox" name="trades[]" value="{{ $k }}" @checked($on)> {{ $t['label'] }}</label> <small>{{ count($t['services']) }} hizmet</small></summary>
                    @if ($on)
                    <div class="fm-subs">@foreach ($t['services'] as $sv)<label><input type="checkbox" name="subservices[]" value="{{ $sv }}" @checked(in_array($sv, old('subservices', []), true))> {{ $sv }}</label>@endforeach</div>
                    @else
                    <div class="fm-subs"></div><template>@foreach ($t['services'] as $sv)<label><input type="checkbox" name="subservices[]" value="{{ $sv }}"> {{ $sv }}</label>@endforeach</template>
                    @endif
                </details>
            @endforeach
            @error('trades')<div class="fm-err">{{ $message }}</div>@enderror
        </div>

        <h3 class="full fm-sec">3. Hizmet bölgesi</h3>
        <div class="full"><label class="f">Hizmet verdiğiniz iller * <small class="o">(en fazla {{ (int) config('worksitem_firms.max_cities') }})</small></label>
            <div class="fm-cities fm-pills">
                @foreach ($cities as $c)<label><input type="checkbox" name="cities[]" value="{{ $c }}" @checked(in_array($c, old('cities', []), true))> {{ $c }}</label>@endforeach
            </div>@error('cities')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div class="full"><label class="f" for="fDists">Yalnız belirli ilçelere mi gidiyorsunuz? <small class="o">(virgülle ayırın; boş bırakırsanız seçtiğiniz illerin tamamı)</small></label><input type="text" id="fDists" name="districts" value="{{ old('districts') }}" maxlength="500" placeholder="ör. Kadıköy, Ataşehir, Üsküdar"></div>

        <h3 class="full fm-sec">4. Tanıtım</h3>
        <div><label class="f" for="fYear">Kuruluş yılı</label><input type="number" id="fYear" name="founded_year" value="{{ old('founded_year') }}" min="1900" max="{{ date('Y') }}">@error('founded_year')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fWeb">Web sitesi</label><input type="url" id="fWeb" name="website" value="{{ old('website') }}" maxlength="200" placeholder="https://">@error('website')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div class="full"><label class="f" for="fDesc">Kısa tanıtım</label><textarea id="fDesc" name="description" rows="4" maxlength="1500" placeholder="Hangi işleri yapıyorsunuz, kaç kişilik ekip, garanti, referanslar…">{{ old('description') }}</textarea></div>

        <h3 class="full fm-sec">5. Yetkili</h3>
        <div><label class="f" for="fContact">Yetkili ad soyad *</label><input type="text" id="fContact" name="contact_name" value="{{ old('contact_name') }}" maxlength="120" required autocomplete="name">@error('contact_name')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fEmail">E-posta * <small class="o">(fatura ve iş bildirimleri)</small></label><input type="email" id="fEmail" name="email" value="{{ old('email') }}" maxlength="150" required autocomplete="email">@error('email')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fPhone">Cep telefonu *</label>
            <div class="row"><input type="tel" id="fPhone" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX" inputmode="tel" maxlength="30" required autocomplete="tel"><button type="button" class="fm-btn o" id="fSend">Kod gönder</button></div>
            <div class="fm-msg" id="fMsg" role="status" aria-live="polite"></div>@error('phone')<div class="fm-err">{{ $message }}</div>@enderror</div>
        <div><label class="f" for="fCode">SMS kodu *</label><input type="text" id="fCode" name="code" inputmode="numeric" maxlength="6" placeholder="6 haneli kod" autocomplete="one-time-code" required>@error('code')<div class="fm-err">{{ $message }}</div>@enderror</div>

        <label class="fm-check full"><input type="checkbox" name="terms" value="1" @checked(old('terms'))> <span><a href="{{ route('legal.firma_uyelik') }}" target="_blank" rel="noopener">Firma Üyelik ve Aracılık Hizmet Sözleşmesi</a>'ni okudum, kabul ediyorum. Kurucu firma programı sonrasında, kabul edilen tekliflerim için eşleşme bedelini ödemeyi kabul ederim.</span></label>
        @error('terms')<div class="fm-err full">{{ $message }}</div>@enderror
        <label class="fm-check full"><input type="checkbox" name="declare" value="1" @checked(old('declare'))> <span>Bilgilerin ve vergi levhasının doğru ve güncel olduğunu; üstlendiğim işlerde müşteriye kendi adıma fatura düzenleyeceğimi, işin kusursuz yapılmasından, iş güvenliği ve yasal yükümlülüklerden yalnız firmamın sorumlu olduğunu beyan ederim.</span></label>
        @error('declare')<div class="fm-err full">{{ $message }}</div>@enderror
        <label class="fm-check full"><input type="checkbox" name="kvkk" value="1" @checked(old('kvkk'))> <span><a href="/legal/kvkk-aydinlatma" target="_blank" rel="noopener">KVKK Aydınlatma Metni</a>'ni okudum. Firma ve yetkili bilgilerim üyelik, fatura ve teklif süreçleri için işlenir.</span></label>
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
    var trades = document.querySelectorAll('input[name="trades[]"]');
    function capTrades() {
        var on = document.querySelectorAll('input[name="trades[]"]:checked').length;
        trades.forEach(function (i) { if (!i.checked) i.disabled = on >= {{ (int) config('worksitem_firms.max_trades') }}; });
    }
    trades.forEach(function (i) {
        i.addEventListener('change', function () { var d = i.closest('details'); if (i.checked) d.open = true; else d.querySelectorAll('.fm-subs input').forEach(function (x) { x.checked = false; }); capTrades(); });
        i.addEventListener('click', function (e) { e.stopPropagation(); });
    });
    // Alt hizmetler (1.300+ kutu) ancak iş kolu açılınca sayfaya eklenir: mobilde hızlı açılış
    document.querySelectorAll('details.fm-trade').forEach(function (d) {
        d.addEventListener('toggle', function () {
            var box = d.querySelector('.fm-subs'), tpl = d.querySelector('template');
            if (d.open && tpl && !box.children.length) { box.appendChild(tpl.content.cloneNode(true)); }
        });
    });
    document.addEventListener('change', function (e) {
        var x = e.target;
        if (!x.matches || !x.matches('.fm-subs input') || !x.checked) return;
        var t = x.closest('details').querySelector('input[name="trades[]"]');
        if (!t.checked && !t.disabled) { t.checked = true; capTrades(); }
    });
    capTrades();
    document.querySelectorAll('.fm-cities').forEach(function (box) {
        var max = {{ (int) config('worksitem_firms.max_cities') }};
        box.addEventListener('change', function () {
            var on = box.querySelectorAll('input:checked').length;
            box.querySelectorAll('input:not(:checked)').forEach(function (i) { i.disabled = on >= max; });
        });
    });
})();
</script>
<x-site.ab-script />
<x-site.contech-footer />
</body>
</html>
