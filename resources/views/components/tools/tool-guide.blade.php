@props(['slug'])
@php
    $guide = config("worksitem_tool_guides.tools.$slug");
    $reviewed = config('worksitem_tool_guides.reviewed');
    if ($guide) {
        // SSS metinlerindeki {kesif_bedeli} / {kesif_gun} yer tutucuları config'ten doldurulur
        $guide['faq'] = array_map(fn ($f) => ['q' => $f['q'], 'a' => \App\Support\InspectionOffer::fill($f['a'])], $guide['faq'] ?? []);
    }
@endphp
@if ($guide)
@php
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn ($f) => [
            '@type' => 'Question',
            'name' => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ], $guide['faq']),
    ];
    $jsConfig = [
        'slug' => $slug,
        'fields' => $guide['fields'] ?? [],
        'calc' => $guide['calc'] ?? null,
        'total' => $guide['total'] ?? null,
        'result' => $guide['result'] ?? null,
        'band' => $guide['band'] ?? null,
        'pdf' => isset($guide['pdf']) ? url($guide['pdf']) : null,
    ];
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, $jsonFlags) !!}</script>
<script type="application/json" id="ws-tool-guide-config">{!! json_encode($jsConfig, $jsonFlags) !!}</script>

<section class="ws-tg" id="hesap-yontemi" aria-labelledby="ws-tg-method-title">
    <div class="ws-tg-inner">
        <div class="ws-tg-toolbar" data-ws-toolbar hidden>
            <button type="button" class="ws-tg-btn" data-ws-share>🔗 Bu hesabı paylaş</button>
            <button type="button" class="ws-tg-btn" data-ws-print>{{ isset($guide['pdf']) ? '📄 PDF rapor indir' : '🖨️ Yazdır / PDF' }}</button>
            <span class="ws-tg-toast" data-ws-toast role="status" aria-live="polite"></span>
        </div>

        <h2 id="ws-tg-method-title">Nasıl hesaplıyoruz?</h2>
        <ol class="ws-tg-method">
            @foreach ($guide['method'] as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>

        <div class="ws-tg-note" role="note">
            <strong>Yaklaşık değerdir.</strong>
            Bu sonuç ön bütçe içindir; yerinde keşif, kesin metraj ve yazılı teklif yerine geçmez. {{ config('worksitem_legal.result_short') }}
            @if (! empty($guide['engineering']))
                Taşıyıcı sistem, statik veya ruhsat konularında karar vermeden önce yetkili bir mühendise danışın.
            @endif
        </div>

        <h2 id="sss">Sık sorulan sorular</h2>
        <div class="ws-tg-faq">
            @foreach ($guide['faq'] as $i => $f)
                <details @if ($i === 0) open @endif>
                    <summary>{{ $f['q'] }}</summary>
                    <p>{{ $f['a'] }}</p>
                </details>
            @endforeach
        </div>

        @if ($reviewed)
            <p class="ws-tg-meta">İçerik son gözden geçirme: {{ $reviewed }} · Hazırlayan: Worksitem / Kainta İnşaat teknik ekibi</p>
        @endif
    </div>
</section>

<style>
.ws-tg{font-family:var(--ws-font,Inter,system-ui,sans-serif);padding:40px 16px;background:var(--ws-page,#F5F6F8);color:var(--ws-ink,#101318)}
.ws-tg-inner{max-width:860px;margin:0 auto}
.ws-tg h2{font-size:1.35rem;font-weight:800;margin:28px 0 12px;line-height:1.3}
.ws-tg h2:first-of-type{margin-top:0}
.ws-tg-method{margin:0;padding-left:22px;line-height:1.65}
.ws-tg-method li{margin-bottom:6px}
.ws-tg-note{margin-top:18px;padding:14px 16px;border-left:4px solid var(--ws-yellow,#FFB800);background:var(--ws-yellow-50,#FFF8E1);border-radius:8px;font-size:.95rem;line-height:1.6;color:#3b3f47}
.ws-tg-faq details{background:#fff;border:1px solid var(--ws-line,#E5E7EB);border-radius:10px;margin-bottom:10px;padding:0 16px}
.ws-tg-faq summary{cursor:pointer;font-weight:700;padding:14px 0;list-style-position:outside}
.ws-tg-faq p{margin:0 0 14px;line-height:1.65;color:var(--ws-muted,#5B6472)}
.ws-tg-meta{margin-top:18px;font-size:.85rem;color:var(--ws-muted,#5B6472)}
.ws-tg-toolbar{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:16px 0 4px}
.ws-tg-btn{font:inherit;font-size:.9rem;font-weight:700;padding:10px 14px;min-height:44px;border-radius:10px;border:1px solid var(--ws-line,#E5E7EB);background:#fff;color:var(--ws-ink,#101318);cursor:pointer}
.ws-tg-btn:hover{border-color:var(--ws-yellow,#FFB800)}
.ws-tg-toast{font-size:.85rem;color:var(--ws-amber-text,#8A6200);font-weight:600}
.ws-tg-share{margin:8px 0 4px;padding:12px;border:1px solid var(--ws-line,#E5E7EB);border-radius:12px;background:#fff;color:var(--ws-ink,#101318);display:grid;gap:8px}
.ws-tg-share[hidden]{display:none}
.ws-tg-share-l{font-size:13px;font-weight:700}
.ws-tg-share-row{display:flex;flex-wrap:wrap;gap:8px}
.ws-tg-share-row input{flex:1 1 200px;min-width:0;min-height:44px;border:1.5px solid var(--ws-line,#E5E7EB);border-radius:10px;padding:0 10px;font:inherit;font-size:14px;color:inherit;background:#fff}
.ws-tg-share-row a.ws-tg-btn{display:inline-flex;align-items:center;text-decoration:none}
.ws-tg-share-ok{font-size:13px;font-weight:700;color:#047857}
.ws-tg-range{display:block;margin-top:6px;font-size:.9rem;font-weight:600;opacity:.85}
html[data-theme="dark"] .ws-tg{background:#0d1117;color:#e6e8eb}
html[data-theme="dark"] .ws-tg-faq details,html[data-theme="dark"] .ws-tg-btn{background:#161b22;border-color:#30363d;color:#e6e8eb}
html[data-theme="dark"] .ws-tg-faq p,html[data-theme="dark"] .ws-tg-meta{color:#9aa4b2}
html[data-theme="dark"] .ws-tg-note{background:#2a2410;color:#e6e8eb}
@media print{
  .wk-header,.wk-footer,#ws-cookie-banner,.ws-tg-toolbar,.ws-tg-faq,#sss,
  #leadBox,#budgetConfirmBox,#qualifiedMessage,#bConf,#leadForm,#budget,#quote,#budgetBox,#quoteBox,
  #calculateBtn,#calcBtn,#calculate{display:none!important}
  .ws-tg{background:#fff;padding:12px 0}
  body{background:#fff!important}
}
</style>
<script src="{{ \App\Support\Asset::url('js/ws-tool-extras.js') }}" defer></script>
@endif
