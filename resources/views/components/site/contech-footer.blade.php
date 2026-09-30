<footer class="wks-footer" aria-label="Site alt bilgisi">

<style>
.wks-footer{
    --wf-bg:#111827;
    --wf-panel:#182231;
    --wf-panel-hover:#202c3d;
    --wf-text:#f8fafc;
    --wf-muted:#aab4c3;
    --wf-line:rgba(255,255,255,.11);
    --wf-yellow:#f5b400;
    --wf-yellow-hover:#ffc326;

    margin-top:64px!important;
    background:var(--wf-bg)!important;
    color:var(--wf-text)!important;
    border-top:0!important;
    font-family:inherit;
}

.wks-footer *{
    box-sizing:border-box;
}

.wks-foot-inner{
    width:min(calc(100% - 40px),1180px)!important;
    max-width:none!important;
    margin:0 auto;
    padding:0 0 24px!important;
}

/* CTA */

.wks-foot-cta{
    position:relative;
    top:-30px;
    display:grid;
    grid-template-columns:minmax(0,1fr) auto;
    align-items:center;
    gap:32px;
    padding:30px 32px;
    overflow:hidden;
    border:1px solid rgba(245,180,0,.28);
    border-radius:22px;
    background:
        radial-gradient(circle at 88% 20%,rgba(245,180,0,.16),transparent 32%),
        var(--wf-panel);
    box-shadow:0 20px 50px rgba(0,0,0,.18);
}

.wks-foot-cta::after{
    content:"";
    position:absolute;
    width:180px;
    height:180px;
    right:-85px;
    bottom:-110px;
    border-radius:50%;
    background:rgba(245,180,0,.08);
    pointer-events:none;
}

.wks-foot-eyebrow{
    display:block;
    margin-bottom:7px;
    color:var(--wf-yellow);
    font-size:11px;
    line-height:1.4;
    font-weight:900;
    letter-spacing:1px;
    text-transform:uppercase;
}

.wks-foot-cta h2{
    margin:0!important;
    max-width:650px;
    color:#fff!important;
    font-size:clamp(24px,3vw,34px)!important;
    line-height:1.12!important;
    letter-spacing:-1px!important;
    font-weight:900!important;
}

.wks-foot-cta p{
    max-width:690px;
    margin:10px 0 0!important;
    color:var(--wf-muted)!important;
    font-size:13px;
    line-height:1.7;
}

.wks-foot-cta-actions{
    position:relative;
    z-index:1;
    display:flex;
    flex-wrap:wrap;
    justify-content:flex-end;
    gap:9px;
}

.wks-foot-btn{
    min-height:48px;
    margin:0!important;
    padding:0 18px;
    display:inline-flex!important;
    align-items:center;
    justify-content:center;
    border:1px solid var(--wf-line);
    border-radius:11px;
    color:#fff!important;
    background:rgba(255,255,255,.04);
    font-size:12px!important;
    font-weight:850;
    white-space:nowrap;
    transition:transform .18s ease,background .18s ease,border-color .18s ease;
}

.wks-foot-btn:hover{
    transform:translateY(-2px);
    background:var(--wf-panel-hover);
    border-color:rgba(255,255,255,.22);
}

.wks-foot-btn-primary{
    border-color:var(--wf-yellow)!important;
    background:var(--wf-yellow)!important;
    color:#111827!important;
}

.wks-foot-btn-primary:hover{
    background:var(--wf-yellow-hover)!important;
}

/* Main grid */

.wks-foot-grid{
    display:grid!important;
    grid-template-columns:minmax(250px,1.55fr) repeat(3,minmax(135px,.72fr))!important;
    gap:42px!important;
    padding:14px 0 36px;
}

.wks-foot-brand{
    min-width:0;
}

.wks-foot-logo{
    display:block;
    width:auto!important;
    height:38px!important;
    padding:5px 7px!important;
    background:#fff!important;
    border-radius:7px!important;
}

.wks-foot-text{
    max-width:390px;
    margin:16px 0 0!important;
    color:var(--wf-muted)!important;
    font-size:13px!important;
    line-height:1.75!important;
}

.wks-foot-proof{
    display:flex;
    flex-wrap:wrap;
    gap:7px;
    margin-top:18px;
}

.wks-foot-proof span{
    padding:6px 9px;
    border:1px solid var(--wf-line);
    border-radius:999px;
    color:#cbd5e1;
    background:rgba(255,255,255,.025);
    font-size:10px;
    font-weight:750;
}

.wks-foot-title{
    margin:2px 0 15px!important;
    color:var(--wf-yellow)!important;
    font-size:11px!important;
    line-height:1.4;
    font-weight:900!important;
    letter-spacing:.9px!important;
    text-transform:uppercase!important;
}

.wks-foot-links{
    display:grid;
    gap:2px;
}

.wks-footer .wks-foot-links a{
    position:relative;
    display:inline-flex!important;
    width:max-content;
    max-width:100%;
    align-items:center;
    min-height:34px;
    margin:0!important;
    color:#cbd5e1!important;
    font-size:13px!important;
    line-height:1.45;
    text-decoration:none!important;
    transition:color .18s ease,transform .18s ease;
}

.wks-footer .wks-foot-links a:hover{
    color:#fff!important;
    transform:translateX(3px);
}

.wks-footer a:focus-visible{
    outline:3px solid var(--wf-yellow);
    outline-offset:3px;
    border-radius:4px;
}

/* Kainta partner */

.wks-partner{
    margin-top:4px;
    padding:17px!important;
    border:1px solid var(--wf-line)!important;
    border-radius:14px!important;
    background:rgba(255,255,255,.035)!important;
}

.wks-partner-label{
    display:block;
    margin-bottom:5px;
    color:var(--wf-yellow);
    font-size:10px;
    font-weight:900;
    letter-spacing:.7px;
    text-transform:uppercase;
}

.wks-partner strong{
    display:block;
    color:#fff;
    font-size:14px!important;
}

.wks-partner span{
    display:block;
    margin-top:6px!important;
    color:var(--wf-muted)!important;
    font-size:11px!important;
    line-height:1.6!important;
}

.wks-footer .wks-partner a{
    display:inline-flex!important;
    align-items:center;
    margin:11px 0 0!important;
    color:var(--wf-yellow)!important;
    font-size:12px!important;
    font-weight:850;
}

/* Bottom */

.wks-foot-bottom{
    margin:0!important;
    padding:20px 0 0!important;
    border-top:1px solid var(--wf-line)!important;
    display:flex!important;
    align-items:center;
    justify-content:space-between;
    gap:20px;
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
    border-radius:50%;
    background:#22c55e;
}

/* Responsive */

@media(max-width:980px){
    .wks-foot-cta{
        grid-template-columns:1fr;
        gap:20px;
    }

    .wks-foot-cta-actions{
        justify-content:flex-start;
    }

    .wks-foot-grid{
        grid-template-columns:1.35fr 1fr 1fr!important;
    }

    .wks-foot-grid > :last-child{
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
        grid-template-columns:1fr 1fr!important;
        gap:30px 24px!important;
        padding:8px 0 30px;
    }

    .wks-foot-brand{
        grid-column:1 / -1;
    }

    .wks-foot-grid > :last-child{
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
    .wks-foot-grid > :last-child{
        grid-column:auto;
    }

    .wks-foot-proof{
        display:grid;
        grid-template-columns:1fr 1fr;
    }

    .wks-foot-proof span{
        text-align:center;
    }
}

@media(prefers-reduced-motion:reduce){
    .wks-foot-btn,
    .wks-footer a{
        transition:none!important;
    }
}
</style>

<div class="wks-foot-inner">

    <section class="wks-foot-cta" aria-labelledby="wks-footer-cta-title">
        <div>
            <span class="wks-foot-eyebrow">WORKSİTEM · Proje ön bütçesi</span>

            <h2 id="wks-footer-cta-title">
                Projenizi rakamlara dökün.
            </h2>

            <p>
                İnşaat, tadilat ve kentsel dönüşüm projeleriniz için yaklaşık
                maliyetinizi hesaplayın; teknik hesaplama ve metraj araçlarına
                tek noktadan ulaşın.
            </p>
        </div>

        <div class="wks-foot-cta-actions">
            <a class="wks-foot-btn wks-foot-btn-primary"
               href="/#proje-hesaplayici">
                Maliyet Hesapla →
            </a>

            <a class="wks-foot-btn"
               href="/araclar">
                Tüm Araçları Gör
            </a>
        </div>
    </section>

    <div class="wks-foot-grid">

        <div class="wks-foot-brand">
            <a href="/" aria-label="WORKSİTEM ana sayfa">
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

            <div class="wks-foot-proof" aria-label="WORKSİTEM özellikleri">
                <span>2026 fiyat verileri</span>
                <span>Ön bütçe</span>
                <span>Metraj araçları</span>
                <span>AI ön keşif</span>
            </div>
        </div>

        <nav aria-label="Ürün bağlantıları">
            <div class="wks-foot-title">Platform</div>

            <div class="wks-foot-links">
                <a href="/#proje-hesaplayici">Maliyet Hesapla</a>
                <a href="/araclar">Tüm Araçlar</a>
                <a href="/araclar/dijital-metraj">Dijital Metraj</a>
                <a href="/araclar/otomatik-kesif-fiyatlandirma">AI Ön Keşif</a>
            </div>
        </nav>

        <nav aria-label="WORKSİTEM bağlantıları">
            <div class="wks-foot-title">WORKSİTEM</div>

            <div class="wks-foot-links">
                <a href="/hakkimizda">Hakkımızda</a>
                <a href="/ekip">Ekip</a>
                <a href="/#fiyatlama">Hesaplama Yöntemi</a>
                <a href="/#sss">Sık Sorulanlar</a>
            </div>
        </nav>

        <div>
            <nav aria-label="Yasal bağlantılar">
                <div class="wks-foot-title">Yasal</div>

                <div class="wks-foot-links">
                    <a href="/legal/kvkk-aydinlatma">KVKK Aydınlatma</a>
                    <a href="/legal/gizlilik-politikasi">Gizlilik Politikası</a>
                    <a href="/legal/cerez-politikasi">Çerez Politikası</a>
                    <a href="/legal/kvkk-basvuru">KVKK Başvuru</a>
                </div>
            </nav>

            <div class="wks-partner">
                <span class="wks-partner-label">Profesyonel uygulama</span>

                <strong>Kainta İnşaat</strong>

                <span>
                    Projeniz ön bütçeye uygunsa profesyonel keşif,
                    mühendislik ve teklif sürecine geçin.
                </span>

                <a href="https://kainta.com"
                   target="_blank"
                   rel="noopener noreferrer">
                    Kainta İnşaat →
                </a>
            </div>
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

<style>
/* WORKSITEM Footer V2 — global light/dark theme integration */

html[data-theme="light"] .wks-footer{
    --wf-bg:#f6f7f9;
    --wf-panel:#ffffff;
    --wf-panel-hover:#f1f3f6;
    --wf-text:#111827;
    --wf-muted:#667085;
    --wf-line:#e2e7ee;
}

html[data-theme="light"] .wks-footer{
    background:var(--wf-bg)!important;
    color:var(--wf-text)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer a{
    color:var(--wf-muted)!important;
}

html[data-theme="light"] .wks-footer a:hover{
    color:var(--wf-text)!important;
}

html[data-theme="light"] .wks-footer .wks-footer-cta,
html[data-theme="light"] .wks-footer .wks-partner{
    background:var(--wf-panel)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-text,
html[data-theme="light"] .wks-footer .wks-partner span,
html[data-theme="light"] .wks-footer .wks-foot-bottom{
    color:var(--wf-muted)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-title{
    color:#9a6800!important;
}

html[data-theme="dark"] .wks-footer{
    --wf-bg:#111827;
    --wf-panel:#182231;
    --wf-panel-hover:#202c3d;
    --wf-text:#f8fafc;
    --wf-muted:#aab4c3;
    --wf-line:rgba(255,255,255,.11);
}
</style>

<style id="worksitem-footer-theme-v1">
/* WORKSITEM Global Theme V1 */

html[data-theme="light"] .wks-footer{
    --wf-bg:#f6f7f9;
    --wf-panel:#ffffff;
    --wf-panel-hover:#f1f3f6;
    --wf-text:#111827;
    --wf-muted:#667085;
    --wf-line:#e2e7ee;

    background:var(--wf-bg)!important;
    color:var(--wf-text)!important;
    border-color:var(--wf-line)!important;
}

/* Gerçek CTA sınıfı */
html[data-theme="light"] .wks-footer .wks-foot-cta{
    background:
        radial-gradient(circle at 90% 20%,rgba(245,180,0,.13),transparent 34%),
        var(--wf-panel)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-cta h2{
    color:var(--wf-text)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-cta p{
    color:var(--wf-muted)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-btn:not(.wks-foot-btn-primary){
    color:var(--wf-text)!important;
    background:var(--wf-panel)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-btn:not(.wks-foot-btn-primary):hover{
    background:var(--wf-panel-hover)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-links a{
    color:var(--wf-muted)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-links a:hover{
    color:var(--wf-text)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-proof span{
    color:var(--wf-muted)!important;
    background:var(--wf-panel)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-partner{
    background:var(--wf-panel)!important;
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-partner strong{
    color:var(--wf-text)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-text,
html[data-theme="light"] .wks-footer .wks-partner span,
html[data-theme="light"] .wks-footer .wks-foot-bottom{
    color:var(--wf-muted)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-bottom{
    border-color:var(--wf-line)!important;
}

html[data-theme="light"] .wks-footer .wks-foot-title{
    color:#9a6800!important;
}

html[data-theme="light"] .wks-footer .wks-foot-logo{
    background:#fff!important;
}

/* Dark standardı */
html[data-theme="dark"] .wks-footer{
    --wf-bg:#111827;
    --wf-panel:#182231;
    --wf-panel-hover:#202c3d;
    --wf-text:#f8fafc;
    --wf-muted:#aab4c3;
    --wf-line:rgba(255,255,255,.11);
}
</style>
