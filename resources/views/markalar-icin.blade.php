@php($toolCfg = $toolCfg ?? null)
@php($toolSlug = $toolSlug ?? null)
@php($toolFree = $toolFree ?? (int) config('worksitem_sponsors.max_cards', 3))
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markalar İçin Sponsorluk | Tadilat Bütçesi Hesaplayan Kullanıcılara Ulaşın | WORKSİTEM</title>
    <meta name="description" content="Seramik, vitrifiye, yapı kimyasalları ve inşaat malzemesi markaları için Worksitem hesaplayıcılarında sponsorluk: bütçe hesaplayan, satın almaya hazır kullanıcılara sonuç ekranında ulaşın.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/markalar-icin') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#101318; --y:#FFB800; --y-50:#FFF8E1; --muted:#5B6472; --line:#E3E6EA; --bg:#F4F5F7; --paper:#fff; }
        html[data-theme="dark"] { --bg:#0B0F17; --paper:#141A24; --line:#232C3A; --muted:#97A1AF; --y-50:#2A2410; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:Inter,-apple-system,'Segoe UI',sans-serif; background:var(--bg); color:var(--ink); -webkit-font-smoothing:antialiased; }
        html[data-theme="dark"] body { color:#E6E9EE; }
        .mk-hero { background:var(--ink); color:#fff; }
        .mk-in { max-width:1120px; margin:0 auto; padding:0 20px; }
        .mk-hero .mk-in { padding-top:48px; padding-bottom:44px; display:grid; grid-template-columns:1.3fr 1fr; gap:32px; align-items:center; }
        .mk-chip { display:inline-block; background:var(--y); color:var(--ink); font-size:12px; font-weight:800; padding:4px 10px; border-radius:999px; text-transform:uppercase; letter-spacing:.04em; }
        .mk-hero h1 { font-size:clamp(30px,4.4vw,48px); line-height:1.1; letter-spacing:-.025em; margin:14px 0 12px; font-weight:900; }
        .mk-hero p { color:#C9CED6; font-size:17px; line-height:1.6; margin:0 0 20px; }
        .mk-btn { display:inline-flex; align-items:center; gap:8px; background:var(--y); color:var(--ink); padding:14px 22px; border-radius:12px; font-weight:800; text-decoration:none; border:0; cursor:pointer; font:800 15px Inter,sans-serif; }
        .mk-mock { background:#fff; color:var(--ink); border-radius:16px; padding:16px; box-shadow:0 30px 60px rgba(0,0,0,.35); }
        .mk-mock small { display:flex; justify-content:space-between; font-weight:700; font-size:12px; color:var(--muted); margin-bottom:10px; }
        .mk-mock .t { font-size:28px; font-weight:900; color:#8A6200; margin:0 0 10px; }
        .mk-mock .c { border:1px solid var(--line); border-radius:12px; padding:12px; background:#FAFAFB; }
        .mk-mock .c b { display:block; font-size:12px; color:#8A6200; text-transform:uppercase; margin-bottom:4px; }
        section.mk { padding:44px 0 8px; }
        section.mk h2 { font-size:clamp(22px,2.8vw,30px); letter-spacing:-.02em; margin:0 0 8px; }
        section.mk > .mk-in > p.lead { color:var(--muted); margin:0 0 22px; font-size:16px; line-height:1.6; max-width:760px; }
        .mk-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
        .mk-card { background:var(--paper); border:1px solid var(--line); border-radius:16px; padding:20px; }
        .mk-card h3 { margin:6px 0 6px; font-size:17px; }
        .mk-card p { margin:0; color:var(--muted); font-size:14px; line-height:1.6; }
        .mk-card .n { width:34px; height:34px; border-radius:10px; background:var(--y-50); color:#8A6200; display:flex; align-items:center; justify-content:center; font-weight:900; }
        .mk-card ul { margin:10px 0 0; padding-left:18px; color:var(--muted); font-size:14px; line-height:1.7; }
        .mk-rules { background:var(--y-50); border-radius:16px; padding:18px 20px; font-size:14px; line-height:1.7; }
        .mk-form { background:var(--paper); border:1px solid var(--line); border-radius:18px; padding:24px; display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .mk-form label { display:block; font-size:13px; font-weight:700; margin-bottom:6px; }
        .mk-form input, .mk-form select, .mk-form textarea { width:100%; padding:12px; border:1.5px solid var(--line); border-radius:10px; font:500 15px Inter,sans-serif; background:var(--paper); color:inherit; }
        .mk-form .full { grid-column:1/-1; }
        .mk-ok { background:#DCFCE7; color:#14532D; border:1px solid #86EFAC; padding:14px 16px; border-radius:12px; font-weight:700; margin-bottom:14px; }
        .mk-err { color:#B91C1C; font-size:13px; margin-top:4px; }
        .mk-check { display:flex; gap:10px; align-items:flex-start; font-size:13px; color:var(--muted); }
        .mk-check input { width:18px; height:18px; flex:0 0 18px; }
        @media (max-width: 860px) {
            .mk-hero .mk-in { grid-template-columns:1fr; }
            .mk-grid { grid-template-columns:1fr; }
            .mk-form { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<x-site.contech-header />

<header class="mk-hero" data-kainta-auto-link="off">
    <div class="mk-in">
        <div>
            <span class="mk-chip">Markalar için</span>
            <h1>Markanızı, bütçesini hesaplayan kullanıcının karar anında gösterin.</h1>
            <p>Worksitem hesaplayıcılarını kullananlar banyosunu, mutfağını ya da evini yenilemek üzere olan kişiler. Sponsor kartınız, hesap sonucunun hemen altında ve ilgili kalemin yanında görünür.</p>
            <a class="mk-btn" href="#basvuru">Sponsorluk teklifi isteyin →</a>
        </div>
        <div class="mk-mock" aria-hidden="true">
            <small><span>Banyo tadilat maliyeti</span><span>Örnek görünüm</span></small>
            <div class="t">₺106.000 – ₺150.000</div>
            <div class="c"><b>Seramik ve karo · Sponsorlu</b><strong>Markanız</strong><br><span style="color:#5B6472;font-size:13px">60×120 porselen koleksiyonu — en yakın bayi</span></div>
        </div>
    </div>
</header>

<section class="mk"><div class="mk-in">
    <h2>Nasıl çalışır?</h2>
    <p class="lead">Reklamınız yalnızca ilgili araçta ve ilgili kalemde gösterilir; bütçe hesaplayan kullanıcı, ürün seçimine en yakın olduğu anda markanızı görür.</p>
    <div class="mk-grid">
        <div class="mk-card"><div class="n">1</div><h3>Yerleşimi seçin</h3><p>Araç (banyo, mutfak, seramik…) ve kalem (seramik, vitrifiye, duşakabin, yapı kimyasalları) bazında.</p></div>
        <div class="mk-card"><div class="n">2</div><h3>Kartınızı hazırlayalım</h3><p>Logo, kısa başlık, açıklama ve hedef sayfanız. Bağlantılara kampanya takip parametreleri otomatik eklenir.</p></div>
        <div class="mk-card"><div class="n">3</div><h3>Sonuçları görün</h3><p>Gösterim, tıklama ve tıklama oranı aylık olarak raporlanır; dönem sonunda yenileme kararını verilerle verin.</p></div>
    </div>
</div></section>

<section class="mk"><div class="mk-in"><x-site.why audience="brands" /></div></section>

<section class="mk" id="paketler"><div class="mk-in">
    <h2>Paketler</h2>
    <p class="lead">Fiyatlandırma araca, kaleme ve süreye göre belirlenir; aylık sabit ücretlidir. Başvurunuza 2 iş günü içinde teklif iletilir.</p>
    <div class="mk-grid">
        <div class="mk-card"><h3>Kalem sponsorluğu</h3><p>Tek araçta tek kalem (örn. banyo · seramik).</p><ul><li>Sonuç kartında marka kartı</li><li>Kalem başına en fazla 3 marka</li><li>Aylık gösterim/tıklama raporu</li></ul></div>
        <div class="mk-card"><h3>Araç sponsorluğu</h3><p>Bir aracın tüm kalemlerinde öncelikli görünüm.</p><ul><li>Kart sıralamasında öncelik</li><li>Pro raporda "önerilen markalar" bölümü</li><li>Aylık rapor + çeyreklik değerlendirme</li></ul></div>
        <div class="mk-card"><h3>Kategori paketi</h3><p>İlgili tüm araçlarda (örn. ince işler: banyo, mutfak, seramik, boya).</p><ul><li>Birden fazla araçta görünüm</li><li>Sezonluk kampanya dönemleri</li><li>Özel rapor</li></ul></div>
    </div>
</div></section>

<section class="mk"><div class="mk-in">
    <div class="mk-rules">
        <strong>Şeffaflık kuralları:</strong> Her sponsor kartında "Sponsorlu" etiketi yer alır. Sponsorluk, hesaplama sonuçlarını, birim fiyatları ve sıralanan maliyetleri hiçbir şekilde etkilemez. Kullanıcı verileri markalarla paylaşılmaz; raporlar toplu ve anonimdir. Bağlantılar arama motorları için "sponsorlu" olarak işaretlenir.
    </div>
</div></section>

<section class="mk"><div class="mk-in">
    <div class="mk-rules" style="background:var(--paper);border:1px solid var(--line)"><strong>Usta, tadilat firması, müteahhit veya bayi misiniz?</strong> Sponsorluk yerine ücretsiz firma üyeliğiyle başlayın: <a href="{{ route('firms.page') }}" style="color:inherit;font-weight:800">Firma üyeliği →</a></div>
</div></section>

<section class="mk" id="basvuru"><div class="mk-in" style="padding-bottom:48px">
    <h2>Sponsorluk teklifi isteyin</h2>
    @if ($toolCfg)<p class="lead">Seçtiğiniz araç: <strong>{{ $toolCfg['label'] }}</strong>. Bu araçta {{ $toolFree }} / {{ config('worksitem_sponsors.max_cards', 3) }} sponsor alanı boş.</p>@endif
    <p class="lead">Formu doldurun; ekibimiz yerleşim önerisi ve fiyat teklifiyle size dönsün.</p>
    @if (session('sponsor_ok'))<div class="mk-ok" role="status">Teşekkürler, başvurunuz alındı. En geç 2 iş günü içinde size dönüş yapacağız.</div>@endif
    <form class="mk-form" method="POST" action="{{ route('sponsor.apply') }}#basvuru">
        @csrf
        <div><label for="mkCompany">Firma / marka</label><input id="mkCompany" name="company" value="{{ old('company') }}" required maxlength="150" autocomplete="organization">@error('company')<div class="mk-err">{{ $message }}</div>@enderror</div>
        <div><label for="mkName">Yetkili ad soyad</label><input id="mkName" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">@error('name')<div class="mk-err">{{ $message }}</div>@enderror</div>
        <div><label for="mkPhone">Telefon</label><input id="mkPhone" name="phone" value="{{ old('phone') }}" required inputmode="tel" autocomplete="tel">@error('phone')<div class="mk-err">{{ $message }}</div>@enderror</div>
        <div><label for="mkEmail">E-posta</label><input id="mkEmail" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<div class="mk-err">{{ $message }}</div>@enderror</div>
        <div class="full"><label for="mkInterest">İlgilendiğiniz yerleşim</label>
            <select id="mkInterest" name="interest">
                @if ($toolCfg)
                    @foreach ($toolCfg['categories'] as $ck => $cl)
                        <option value="{{ $toolCfg['label'] }} · {{ $cl }}" @selected(old('interest') === $toolCfg['label'].' · '.$cl)>{{ $toolCfg['label'] }} · {{ $cl }}</option>
                    @endforeach
                @endif
                <option value="Banyo · seramik">Banyo · seramik ve karo</option>
                <option value="Banyo · vitrifiye">Banyo · vitrifiye ve armatür</option>
                <option value="Banyo · duşakabin">Banyo · duşakabin, küvet</option>
                <option value="Banyo · yapı kimyasalları">Banyo · su yalıtımı ve yapı kimyasalları</option>
                <option value="Kategori paketi">Kategori paketi (birden fazla araç)</option>
                <option value="Diğer">Diğer</option>
            </select>
            @if ($toolCfg)<input type="hidden" name="tool" value="{{ $toolSlug }}">@endif
        </div>
        <div class="full"><label for="mkMsg">Mesajınız (isteğe bağlı)</label><textarea id="mkMsg" name="message" rows="3" maxlength="2000">{{ old('message') }}</textarea></div>
        <label class="mk-check full"><input type="checkbox" name="contact_consent" value="1" required> <span><a href="/legal/kvkk-aydinlatma" target="_blank" rel="noopener">KVKK Aydınlatma Metni</a>'ni okudum; sponsorluk teklifi için benimle iletişime geçilmesini kabul ediyorum.</span></label>
        @error('contact_consent')<div class="mk-err full">{{ $message }}</div>@enderror
        <div class="full"><button class="mk-btn" type="submit">Teklif iste</button></div>
    </form>
</div></section>

<x-site.contech-footer />
</body>
</html>
