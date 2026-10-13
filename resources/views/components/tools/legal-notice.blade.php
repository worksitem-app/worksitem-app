{{-- Tüm araç sayfalarının altına ToolLegalNotice middleware'i ile eklenir. Metin: config/worksitem_legal.php --}}
<section id="ws-legal-notice" class="ws-legal" aria-labelledby="wsLegalTitle">
    <div class="ws-legal-in">
        <h2 id="wsLegalTitle">⚖️ {{ config('worksitem_legal.title') }}</h2>
        @foreach ((array) config('worksitem_legal.paragraphs') as $p)
            <p>{{ $p }}</p>
        @endforeach
        <p class="ws-legal-k">Yerinde keşif: {{ \App\Support\InspectionOffer::feeLabel() }}. Ayrıntılar için <a href="/legal/kullanim-kosullari">Kullanım Koşulları</a>.</p>
    </div>
</section>
<style>
.ws-legal{background:#F5F6F8;border-top:1px solid #E3E6EA;padding:28px 16px 24px}
.ws-legal-in{max-width:960px;margin:0 auto;font:13px/1.65 Inter,system-ui,-apple-system,'Segoe UI',sans-serif;color:#5B6472}
.ws-legal h2{font-size:14px;margin:0 0 8px;color:#101318;font-weight:800}
.ws-legal p{margin:0 0 8px}
.ws-legal a{color:#101318;text-decoration:underline}
html[data-theme="dark"] .ws-legal{background:#0f141d;border-color:#1e293b}
html[data-theme="dark"] .ws-legal-in{color:#94a3b8}
html[data-theme="dark"] .ws-legal h2,html[data-theme="dark"] .ws-legal a{color:#e6e8eb}
@media print{.ws-legal{background:#fff;padding:12px 0}}
</style>
