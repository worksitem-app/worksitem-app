{{-- v117 ortak kabuk: tek alt bilgi. Stiller css/wk-shell.css'te (BrandShell her sayfaya gömer). --}}
@php($wksAi = \App\Support\Tools::aiFileOn())
<footer class="wk-footer" aria-label="Site alt bilgisi">
<div class="wk-footer-in">
    @if (request()->path() !== '/')
    <section class="wk-footer-cta" aria-labelledby="wk-footer-cta-title">
        <div>
            <p class="wk-footer-eyebrow">Proje ön bütçesi</p>
            <h2 id="wk-footer-cta-title">Projenizi rakamlara dökün</h2>
            <p>İnşaat, tadilat ve kentsel dönüşüm için yaklaşık maliyetinizi hesaplayın; hesap ve metraj araçlarına tek yerden ulaşın.</p>
        </div>
        <div class="wk-footer-cta-actions">
            <a class="wk-fbtn wk-fbtn-primary" href="/#proje-hesaplayici">Ön bütçemi hesapla</a>
            <a class="wk-fbtn" href="/araclar">Tüm araçlar</a>
        </div>
    </section>
    @endif

    <div class="wk-footer-grid">
        <div class="wk-footer-brand">
            <a class="wk-logo" href="/" aria-label="Worksitem ana sayfa">
                <img src="/brand/worksitem-logo.png" alt="Worksitem" loading="lazy" width="160" height="46">
            </a>
            <p>İnşaat ve tadilat projeleri için maliyet, metraj, poz ve keşif platformu. Sonuçlar ön değerlendirme ve bütçe planlaması içindir; kesin teklif değildir.</p>
            <p class="wk-footer-contact">Destek: <a href="mailto:info@worksitem.com">info@worksitem.com</a></p>
            <p class="wk-footer-contact">Yerinde keşif ve uygulama: <a href="https://kainta.com" target="_blank" rel="noopener noreferrer">Kainta İnşaat<span class="wk-sr"> (yeni sekmede açılır)</span></a></p>
        </div>

        <nav aria-label="Platform">
            <h2>Platform</h2>
            <ul>
                <li><a href="/#proje-hesaplayici">Maliyet hesapla</a></li>
                <li><a href="/araclar">Tüm araçlar</a></li>
                <li><a href="/pozlar">Pozlar</a></li>
                <li><a href="/araclar/cati-maliyet-hesaplayici">Çatı maliyeti</a></li>
                <li><a href="/araclar/beton-hesaplayici">Beton hesaplama</a></li>
                @if ($wksAi)
                <li><a href="/araclar/otomatik-kesif-fiyatlandirma">AI ön keşif</a></li>
                @else
                <li><a href="/maliyet-hesapla">Tadilat maliyeti</a></li>
                @endif
            </ul>
        </nav>

        <nav aria-label="Worksitem">
            <h2>Worksitem</h2>
            <ul>
                <li><a href="/hakkimizda">Hakkımızda</a></li>
                <li><a href="/ekip">Ekip</a></li>
                @if (\App\Support\Pilot::firmSignupOpen())
                <li><a href="/firma-uyeligi">Firma üyeliği</a></li>
                @endif
                <li><a href="/uygulama">Uygulamayı indir</a></li>
                <li><a href="/#fiyatlama">Hesaplama yöntemi</a></li>
                <li><a href="/#sss">Sık sorulanlar</a></li>
            </ul>
        </nav>

        <nav aria-label="Yasal">
            <h2>Yasal</h2>
            <ul>
                <li><a href="/legal/kvkk-aydinlatma">KVKK aydınlatma</a></li>
                <li><a href="/legal/gizlilik-politikasi">Gizlilik politikası</a></li>
                <li><a href="/legal/cerez-politikasi">Çerez politikası</a></li>
                <li><a href="/legal/kvkk-basvuru">KVKK başvuru</a></li>
                <li><a href="/legal/kullanim-kosullari">Kullanım koşulları</a></li>
                <li><a href="/legal/cerez-politikasi" onclick="if(window.openCookieSettings){window.openCookieSettings();return false;}">Çerez tercihleri</a></li>
            </ul>
        </nav>
    </div>

    <div class="wk-footer-bottom">
        <span>© {{ date('Y') }} Worksitem</span>
        <span>İnşaat · Tadilat · Kentsel dönüşüm</span>
    </div>
</div>
</footer>
