<footer class="wks-footer {{ request()->path() === '/' ? 'wks-footer-home' : '' }}" aria-label="Site alt bilgisi">

<style>
/* =========================================================
   WORKSITEM FOOTER V2
   Component design tokens + light/dark integration
   ========================================================= */

.wks-footer{
    --wf-bg:#f6f7f9;
    --wf-panel:#ffffff;
    --wf-panel-soft:#f0f2f5;
    --wf-text:#111827;
    --wf-muted:#5f6b7a;
    --wf-line:#dce2e9;

    --wf-yellow:#f5b400;
    --wf-yellow-hover:#e6a900;
    --wf-yellow-soft:#fff8df;

    --wf-success:#16a34a;

    --wf-shadow:
        0 16px 40px rgba(15,23,42,.09);

    margin-top:64px!important;
    border-top:1px solid var(--wf-line)!important;
    background:var(--wf-bg)!important;
    color:var(--wf-text)!important;
    font-family:inherit;
}

html[data-theme="dark"] .wks-footer{
    --wf-bg:#111827;
    --wf-panel:#182231;
    --wf-panel-soft:#202c3d;
    --wf-text:#f8fafc;
    --wf-muted:#aeb8c6;
    --wf-line:rgba(255,255,255,.12);

    --wf-yellow:#f5b400;
    --wf-yellow-hover:#ffc326;
    --wf-yellow-soft:rgba(245,180,0,.10);

    --wf-success:#22c55e;

    --wf-shadow:
        0 20px 50px rgba(0,0,0,.22);
}

.wks-footer *,
.wks-footer *::before,
.wks-footer *::after{
    box-sizing:border-box;
}

.wks-foot-inner{
    width:min(calc(100% - 40px),1180px)!important;
    margin:0 auto;
    padding:0 0 24px!important;
}

/* =========================================================
   CTA
   ========================================================= */

.wks-foot-cta{
    position:relative;
    top:-30px;

    display:grid;
    grid-template-columns:minmax(0,1fr) auto;
    align-items:center;
    gap:30px;

    padding:29px 32px;

    overflow:hidden;

    border:1px solid rgba(245,180,0,.28);
    border-radius:22px;

    background:
        radial-gradient(
            circle at 90% 15%,
            rgba(245,180,0,.15),
            transparent 32%
        ),
        var(--wf-panel);

    box-shadow:var(--wf-shadow);
}

.wks-foot-cta::after{
    content:"";

    position:absolute;
    right:-84px;
    bottom:-112px;

    width:180px;
    height:180px;

    border-radius:50%;
    background:rgba(245,180,0,.08);

    pointer-events:none;
}

.wks-foot-cta-copy,
.wks-foot-cta-actions{
    position:relative;
    z-index:1;
}

.wks-foot-eyebrow{
    display:block;

    margin-bottom:7px;

    color:var(--wf-yellow);

    font-size:10px;
    line-height:1.4;
    font-weight:900;

    letter-spacing:1px;
    text-transform:uppercase;
}

.wks-foot-cta h2{
    max-width:650px;

    margin:0!important;

    color:var(--wf-text)!important;

    font-size:clamp(25px,3vw,35px)!important;
    line-height:1.1!important;
    letter-spacing:-1.2px!important;

    font-weight:950!important;
}

.wks-foot-cta p{
    max-width:680px;

    margin:10px 0 0!important;

    color:var(--wf-muted)!important;

    font-size:13px!important;
    line-height:1.65!important;
}

.wks-foot-cta-actions{
    display:flex;
    flex-wrap:wrap;
    justify-content:flex-end;
    gap:9px;
}

.wks-foot-btn{
    min-height:48px;

    display:inline-flex!important;
    align-items:center;
    justify-content:center;

    margin:0!important;
    padding:0 18px;

    border:1px solid var(--wf-line)!important;
    border-radius:11px!important;

    background:var(--wf-panel)!important;
    color:var(--wf-text)!important;

    font-size:12px!important;
    font-weight:900!important;

    white-space:nowrap;
    text-decoration:none!important;

    transition:
        transform .18s ease,
        border-color .18s ease,
        background .18s ease;
}

.wks-foot-btn:hover{
    transform:translateY(-2px);

    border-color:rgba(245,180,0,.45)!important;
    background:var(--wf-panel-soft)!important;
}

.wks-foot-btn-primary{
    border-color:var(--wf-yellow)!important;
    background:var(--wf-yellow)!important;
    color:#111827!important;
}

.wks-foot-btn-primary:hover{
    background:var(--wf-yellow-hover)!important;
}

/* =========================================================
   GRID
   ========================================================= */

.wks-foot-grid{
    display:grid!important;

    grid-template-columns:
        minmax(280px,1.6fr)
        minmax(125px,.72fr)
        minmax(125px,.72fr)
        minmax(135px,.78fr)
        minmax(155px,.9fr)!important;

    gap:26px!important;

    padding:8px 0 34px;
}

.wks-foot-brand{
    min-width:0;
    padding-right:16px;
}

.wks-foot-logo{
    display:block;

    width:auto!important;
    height:38px!important;

    padding:5px 7px!important;

    border:1px solid var(--wf-line);
    border-radius:7px!important;

    background:#fff!important;
}

.wks-foot-text{
    max-width:390px;

    margin:16px 0 0!important;

    color:var(--wf-muted)!important;

    font-size:13px!important;
    line-height:1.7!important;
}

/* =========================================================
   Proof list — no false button affordance
   ========================================================= */

.wks-foot-proof{
    display:grid;

    grid-template-columns:
        repeat(2,minmax(0,1fr));

    gap:7px 14px;

    margin:17px 0 0;
    padding:0;

    list-style:none;
}

.wks-foot-proof li{
    position:relative;

    padding-left:19px;

    color:var(--wf-muted);

    font-size:11px;
    line-height:1.5;
    font-weight:750;
}

.wks-foot-proof li::before{
    content:"✓";

    position:absolute;
    left:0;
    top:0;

    color:var(--wf-yellow);

    font-weight:950;
}

/* =========================================================
   Contact
   ========================================================= */

.wks-foot-contact{
    margin-top:18px;
    padding-top:14px;

    border-top:1px solid var(--wf-line);
}

.wks-foot-contact-label{
    display:block;

    margin-bottom:6px;

    color:var(--wf-muted);

    font-size:10px;
    font-weight:850;

    letter-spacing:.65px;
    text-transform:uppercase;
}

.wks-footer .wks-foot-contact a{
    display:inline-flex!important;
    align-items:center;
    gap:7px;

    color:var(--wf-text)!important;

    font-size:12px!important;
    font-weight:900!important;

    text-decoration:none!important;
}

.wks-footer .wks-foot-contact a::before{
    content:"@";

    color:var(--wf-yellow);
    font-weight:950;
}

/* =========================================================
   Navigation
   ========================================================= */

.wks-foot-title{
    margin:2px 0 13px!important;

    color:var(--wf-yellow)!important;

    font-size:10px!important;
    line-height:1.4;

    font-weight:950!important;

    letter-spacing:.9px!important;
    text-transform:uppercase!important;
}

.wks-foot-links{
    display:grid;
    gap:1px;
}

.wks-footer .wks-foot-links a{
    display:inline-flex!important;
    align-items:center;

    width:max-content;
    max-width:100%;

    min-height:31px;

    margin:0!important;

    color:var(--wf-muted)!important;

    font-size:12px!important;
    line-height:1.45!important;
    font-weight:650!important;

    text-decoration:none!important;

    transition:
        color .18s ease,
        transform .18s ease;
}

.wks-footer .wks-foot-links a:hover{
    color:var(--wf-text)!important;
    transform:translateX(2px);
}

/* =========================================================
   Professional application — no competing card
   ========================================================= */

.wks-foot-pro{
    min-width:0;

    padding-left:16px;

    border-left:2px solid rgba(245,180,0,.58);
}

.wks-foot-pro strong{
    display:block;

    margin:0 0 8px;

    color:var(--wf-text);

    font-size:13px;
    line-height:1.4;
}

.wks-foot-pro p{
    margin:0!important;

    color:var(--wf-muted)!important;

    font-size:11px!important;
    line-height:1.65!important;
}

.wks-footer .wks-foot-pro-link{
    display:inline-flex!important;
    align-items:center;
    gap:5px;

    margin:11px 0 0!important;

    color:var(--wf-text)!important;

    font-size:11px!important;
    font-weight:900!important;

    text-decoration:none!important;
}

.wks-foot-pro-link-arrow{
    color:var(--wf-yellow);
}

/* =========================================================
   Accessibility
   ========================================================= */

.wks-footer a:focus-visible{
    outline:3px solid var(--wf-yellow);
    outline-offset:3px;
    border-radius:4px;
}

/* =========================================================
   Bottom
   ========================================================= */

.wks-foot-bottom{
    display:flex!important;
    align-items:center;
    justify-content:space-between;

    gap:20px;

    margin:0!important;
    padding:19px 0 0!important;

    border-top:1px solid var(--wf-line)!important;

    color:var(--wf-muted)!important;

    font-size:11px!important;
}

.wks-foot-bottom-left{
    display:flex;
    flex-wrap:wrap;
    align-items:center;

    gap:8px 16px;
}

.wks-foot-bottom-right{
    text-align:right;
}

.wks-foot-status{
    display:inline-flex;
    align-items:center;

    gap:6px;
}

.wks-foot-status::before{
    content:"";

    width:6px;
    height:6px;

    flex:0 0 6px;

    border-radius:50%;

    background:var(--wf-success);
}

/* =========================================================
   Responsive
   ========================================================= */

@media(max-width:1080px){

    .wks-foot-grid{
        grid-template-columns:
            minmax(270px,1.4fr)
            repeat(2,minmax(150px,1fr))!important;

        gap:30px!important;
    }

    .wks-foot-pro{
        grid-column:2 / 4;
    }
}

@media(max-width:760px){

    .wks-footer{
        margin-top:48px!important;
    }

    .wks-foot-inner{
        width:min(calc(100% - 28px),1180px)!important;
        padding-bottom:20px!important;
    }

    .wks-foot-cta{
        top:-22px;

        grid-template-columns:1fr;

        gap:20px;

        padding:24px 20px;

        border-radius:18px;
    }

    .wks-foot-cta-actions{
        display:grid;
        grid-template-columns:1fr;
    }

    .wks-foot-btn{
        width:100%;
    }

    .wks-foot-grid{
        grid-template-columns:
            repeat(2,minmax(0,1fr))!important;

        gap:28px 22px!important;

        padding:8px 0 29px;
    }

    .wks-foot-brand{
        grid-column:1 / -1;

        padding-right:0;
    }

    .wks-foot-pro{
        grid-column:1 / -1;
    }

    .wks-foot-bottom{
        flex-direction:column!important;
        align-items:flex-start!important;
    }

    .wks-foot-bottom-right{
        text-align:left;
    }
}

@media(max-width:480px){

    .wks-foot-grid{
        grid-template-columns:1fr!important;
    }

    .wks-foot-brand,
    .wks-foot-pro{
        grid-column:auto;
    }

    .wks-foot-proof{
        grid-template-columns:1fr;
    }
}

@media(prefers-reduced-motion:reduce){

    .wks-footer a,
    .wks-foot-btn{
        transition:none!important;
    }
}

/* Homepage: üst CTA zaten sayfa içinde mevcut */
.wks-footer-home{
    margin-top:38px!important;
}

.wks-footer-home .wks-foot-inner{
    padding-top:30px!important;
}

@media(max-width:760px){
    .wks-footer-home{
        margin-top:30px!important;
    }

    .wks-footer-home .wks-foot-inner{
        padding-top:24px!important;
    }
}

/* WORKSITEM FOOTER TYPOGRAPHY V2 */
.wks-footer .wks-foot-title{
    font-weight:800!important;
    letter-spacing:1px!important;
    line-height:1.4!important;
}

.wks-footer .wks-foot-pro strong{
    font-weight:800!important;
    letter-spacing:.05px!important;
    line-height:1.4!important;
}

.wks-footer a{
    line-height:1.55!important;
}

</style>

<div class="wks-foot-inner">

    @if (request()->path() !== '/')
<section
        class="wks-foot-cta"
        aria-labelledby="wks-footer-cta-title"
    >

        <div class="wks-foot-cta-copy">

            <span class="wks-foot-eyebrow">
                WORKSİTEM · Proje ön bütçesi
            </span>

            <h2 id="wks-footer-cta-title">
                Projenizi rakamlara dökün.
            </h2>

            <p>
                İnşaat, tadilat ve kentsel dönüşüm projeleriniz için
                yaklaşık maliyetinizi hesaplayın; teknik hesaplama ve
                metraj araçlarına tek noktadan ulaşın.
            </p>

        </div>

        <div class="wks-foot-cta-actions">

            <a
                class="wks-foot-btn wks-foot-btn-primary"
                href="/#proje-hesaplayici"
            >
                Ön Bütçemi Hesapla →
            </a>

            <a
                class="wks-foot-btn"
                href="/araclar"
            >
                Tüm Araçları Gör
            </a>

        </div>

    </section>
@endif

    <div class="wks-foot-grid">

        <div class="wks-foot-brand">

            <a
                href="/"
                aria-label="WORKSİTEM ana sayfa"
            >
                <img
                    src="/brand/worksitem-logo.png"
                    class="wks-foot-logo"
                    alt="WORKSİTEM"
                    loading="lazy"
                    width="160"
                    height="38"
                >
            </a>

            <p class="wks-foot-text">
                İnşaat ve tadilat projeleri için veri odaklı maliyet,
                metraj, ön keşif ve teknik hesaplama platformu.
                Sonuçlar ön değerlendirme ve bütçe planlaması amacıyla sunulur.
            </p>

            <ul
                class="wks-foot-proof"
                aria-label="WORKSİTEM özellikleri"
            >
                <li>2026 fiyat verileri</li>
                <li>Ön bütçe</li>
                <li>Teknik hesaplama</li>
                <li>AI ön keşif</li>
            </ul>

            <div class="wks-foot-contact">

                <span class="wks-foot-contact-label">
                    Destek / İletişim
                </span>

                <a href="mailto:info@worksitem.com">
                    info@worksitem.com
                </a>

            </div>

        </div>

        <nav aria-label="Platform bağlantıları">

            <div class="wks-foot-title">
                Platform
            </div>

            <div class="wks-foot-links">
                <a href="/#proje-hesaplayici">
                    Maliyet Hesapla
                </a>

                <a href="/araclar">
                    Tüm Araçlar
                </a>

                <a href="/pozlar">
                    Poz Ara
                </a>

                <a href="/araclar/cati-maliyet-hesaplayici">
                    Çatı Maliyeti
                </a>

                <a href="/araclar/beton-hesaplayici">
                    Beton Hesaplayıcı
                </a>

                <a href="/araclar/otomatik-kesif-fiyatlandirma">
                    AI Ön Keşif
                </a>
            </div>

        </nav>

        <nav aria-label="WORKSİTEM bağlantıları">

            <div class="wks-foot-title">
                WORKSİTEM
            </div>

            <div class="wks-foot-links">

                <a href="/hakkimizda">
                    Hakkımızda
                </a>

                <a href="/ekip">
                    Ekip
                </a>

                <a href="/#fiyatlama">
                    Hesaplama Yöntemi
                </a>

                <a href="/#sss">
                    Sık Sorulanlar
                </a>

            </div>

        </nav>

        <nav aria-label="Yasal bağlantılar">

            <div class="wks-foot-title">
                Yasal
            </div>

            <div class="wks-foot-links">

                <a href="/legal/kvkk-aydinlatma">
                    KVKK Aydınlatma
                </a>

                <a href="/legal/gizlilik-politikasi">
                    Gizlilik Politikası
                </a>

                <a href="/legal/cerez-politikasi">
                    Çerez Politikası
                </a>

                <a href="/legal/kvkk-basvuru">
                    KVKK Başvuru
                </a>

                <a href="/legal/kullanim-kosullari">
                    Kullanım Koşulları
                </a>

                <a href="/legal/cerez-politikasi" onclick="if(window.openCookieSettings){window.openCookieSettings();return false;}">
                    Çerez Tercihleri
                </a>

            </div>

        </nav>

        <div class="wks-foot-pro">

            <div class="wks-foot-title">
                Profesyonel Uygulama
            </div>

            <strong>
                Kainta İnşaat
            </strong>

            <p>
                Ön bütçesi uygun projelerde profesyonel keşif,
                mühendislik ve teklif sürecine geçin.
            </p>

            <a
                class="wks-foot-pro-link"
                href="https://kainta.com"
                target="_blank"
                rel="noopener noreferrer"
            >
                Kainta İnşaat
                <span class="wks-foot-pro-link-arrow">→</span>
            </a>

        </div>

    </div>

    <div class="wks-foot-bottom">

        <div class="wks-foot-bottom-left">
            <span>© {{ date('Y') }} WORKSİTEM</span>
            <span>Türkiye için ConTech karar destek platformu</span>
        </div>

        <div class="wks-foot-bottom-right">

            <span class="wks-foot-status">
                İnşaat · Tadilat · Kentsel Dönüşüm
            </span>

        </div>

    </div>

</div>

</footer>
