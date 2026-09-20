<header class="wks-header">
<style>
.wks-header{position:relative;z-index:1000;background:#fff;border-bottom:1px solid #e5e7eb;font-family:inherit}
.wks-header *{box-sizing:border-box}
.wks-head-inner{max-width:1180px;min-height:72px;margin:auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;gap:24px}
.wks-brand{display:flex;align-items:center;flex-shrink:0}
.wks-brand img{display:block;height:38px;width:auto}
.wks-nav{display:flex;align-items:center;gap:6px;margin-left:auto}
.wks-nav a{padding:10px 12px;border-radius:9px;text-decoration:none;color:#374151;font-size:13px;font-weight:750}
.wks-nav a:hover{background:#f3f4f6;color:#111827}
.wks-offer{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 16px;background:#f5b400;color:#111827!important;border-radius:10px!important;font-weight:850!important;white-space:nowrap}
.wks-offer:hover{background:#dfa500!important}
.wks-menu{display:none;width:42px;height:42px;border:1px solid #e5e7eb;background:#fff;border-radius:10px;font-size:20px;cursor:pointer}
@media(max-width:820px){
 .wks-head-inner{min-height:64px;padding:0 14px}
 .wks-brand img{height:34px}
 .wks-menu{display:block}
 .wks-nav{display:none;position:absolute;top:64px;left:12px;right:12px;padding:10px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 18px 45px rgba(0,0,0,.12);flex-direction:column;align-items:stretch}
 .wks-nav.wks-open{display:flex}
 .wks-nav a{padding:13px}
}
</style>

<div class="wks-head-inner">
    <a href="/" class="wks-brand" aria-label="WORKSİTEM ana sayfa">
        <img src="/brand/worksitem-logo.png" alt="WORKSİTEM">
    </a>

    <button class="wks-menu" type="button"
            onclick="this.nextElementSibling.classList.toggle('wks-open')"
            aria-label="Menüyü aç">☰</button>

    <nav class="wks-nav" aria-label="Ana navigasyon">
        <a href="/araclar">Araçlar</a>
        <a href="/araclar/dijital-metraj">Dijital Metraj</a>
        <a href="/maliyet-hesapla" class="wks-offer">Proje Bütçesi</a>
    </nav>
</div>
</header>
<x-site.kainta-linker />
