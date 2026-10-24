@php
    $page['seo_title'] = \App\Support\ToolSeo::title($page['seo_title']);
    $page['seo_desc'] = \App\Support\ToolSeo::desc($page['seo_desc']);
    $page['faq'] = \App\Support\ToolSeo::faq($page['faq']);
    $quick = \App\Support\ToolSeo::quickAnswer($page['faq']);
    $year = now()->year;
    $url = 'https://worksitem.com/araclar/'.$slug;
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP;
    $tl = fn ($v) => '₺'.number_format((float) $v, 0, ',', '.');
    $schema = [
        ['@context' => 'https://schema.org', '@type' => 'WebApplication', 'name' => $page['title'], 'url' => $url,
            'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web', 'inLanguage' => 'tr-TR',
            'description' => $page['seo_desc'], 'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'TRY'],
            'publisher' => ['@type' => 'Organization', 'name' => 'Worksitem', 'url' => 'https://worksitem.com']],
        ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana sayfa', 'item' => 'https://worksitem.com/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Araçlar', 'item' => 'https://worksitem.com/araclar'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $page['title'], 'item' => $url]]],
        ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => collect($page['faq'])->map(fn ($f) => [
            '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]])->all()],
    ];
    $n = 0;
    $ready = $est['ready'] ?? false;
    $fmtNum = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', ''), '0'), ',');
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page['seo_title'] }}</title>
    <meta name="description" content="{{ $page['seo_desc'] }}">
    <link rel="canonical" href="{{ $url }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $page['seo_title'] }}">
    <meta property="og:description" content="{{ $page['seo_desc'] }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:image" content="https://worksitem.com/brand/og-image.png">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- v119: araç sayfası tasarım dilinde (wk-). Bileşenler wk-ui.css'te (BrandShell ekler); araç düzeni wk-tool.css'te --}}
    <style data-asset="wk-tool.css">{!! \App\Support\Asset::inline('css/wk-tool.css') !!}</style>
    @foreach ($schema as $s)
        <script type="application/ld+json">{!! json_encode($s, $jsonFlags) !!}</script>
    @endforeach
</head>
<body class="wk-root" data-tool-slug="{{ $slug }}">

<x-site.contech-header />

<main class="wk-container kit" id="ana-icerik">
    <div class="wk-pagehead">
        <nav aria-label="Gezinti yolu"><ol class="wk-crumbs"><li><a href="/">Ana sayfa</a></li><li><a href="/araclar">Araçlar</a></li><li aria-current="page">{{ $page['title'] }}</li></ol></nav>
        <h1 class="wk-h1">{{ $page['h1'] }}</h1>
        <p class="wk-lead">{{ $page['lede'] }}</p>
        @if ($quick)
            <p class="kit-answer"><b>Kısa cevap:</b> {{ $quick[1] }}</p>
        @endif
        <div class="wk-chips">
            @foreach ($page['facts'] ?? [] as $f)<span class="wk-chip">{{ $f }}</span>@endforeach
            <span class="wk-chip">Fiyatlar: <b>{{ $e->pricesLabel() }}</b></span>
        </div>
    </div>

    @if ($errors->has('kit'))
        <div class="wk-note wk-note-danger" role="alert" style="margin-bottom:var(--wk-space-4)"><span>{{ $errors->first('kit') }}</span></div>
    @endif

    <div class="wk-tool kit-tool">
        <form class="wk-card kit-form" id="kForm" onsubmit="return false" aria-label="Hesap bilgileri">
            <ol class="wk-steps">
            @foreach ($steps as $step)
                @php $n++; @endphp
                @php $vis = $e->visible($step, $input ?? []); @endphp
                <li class="wk-step bt-step" data-step="{{ $step['key'] ?? 'numbers'.$n }}" @if (! empty($step['show'])) data-show='@json($step['show'])' @endif @if (! empty($step['hide'])) data-hide='@json($step['hide'])' @endif @if (! $vis) hidden @endif>
                    <span class="wk-step-n" aria-hidden="true">{{ $n }}</span>
                    <div class="wk-step-body">
                    @if ($step['type'] === 'cards' || $step['type'] === 'checks')
                        @php $opt = $step['type'] === 'checks' || ! ($step['required'] ?? false); @endphp
                        <fieldset class="kit-fs">
                            <legend class="wk-legend-h">{{ $step['title'] }}@if ($opt) <small class="kit-opt-note">isteğe bağlı</small>@endif</legend>
                            <div class="wk-options kit-opts c{{ $step['type'] === 'checks' ? 2 : ($step['cols'] ?? 3) }}">
                            @if ($step['type'] === 'cards')
                                @foreach ($step['options'] as $k => $o)
                                    <label class="wk-option" for="o_{{ $n }}_{{ $step['key'] }}_{{ $k }}"><input type="radio" name="{{ $step['key'] }}" id="o_{{ $n }}_{{ $step['key'] }}_{{ $k }}" value="{{ $k }}" @checked(($input[$step['key']] ?? '') === (string) $k) @disabled(! $vis)><b>{{ $o['label'] }}</b>@if (! empty($o['hint']))<span>{{ $o['hint'] }}</span>@endif</label>
                                @endforeach
                            @else
                                @foreach ($step['options'] as $k => $label)
                                    <label class="wk-option"><input type="checkbox" name="{{ $step['key'] }}[]" value="{{ $k }}" @checked(in_array($k, $input[$step['key']] ?? [], true)) @disabled(! $vis)><b>{{ $label }}</b></label>
                                @endforeach
                            @endif
                            </div>
                        </fieldset>
                    @elseif ($step['type'] === 'numbers')
                        <h2 class="wk-legend-h kit-h">{{ $step['title'] }}</h2>
                        <div class="kit-dims c{{ $step['cols'] ?? count($step['fields']) }}">
                            @foreach ($step['fields'] as $f)
                                <div class="wk-field">
                                    <label class="wk-label" for="f_{{ $f['key'] }}">{{ $f['label'] }}</label>
                                    <div class="wk-input-unit"><input type="text" class="wk-input" id="f_{{ $f['key'] }}" name="{{ $f['key'] }}" inputmode="decimal" autocomplete="off" pattern="[0-9]*[.,]?[0-9]*" data-min="{{ $f['min'] }}" data-max="{{ $f['max'] }}" placeholder="{{ $f['placeholder'] ?? '' }}" aria-describedby="u_{{ $f['key'] }}" @disabled(! $vis)
                                        value="{{ ! empty($input['_given_'.$f['key']]) ? str_replace('.', ',', (string) $input[$f['key']]) : '' }}"><span class="wk-unit" id="u_{{ $f['key'] }}">{{ $f['unit'] }}</span></div>
                                </div>
                            @endforeach
                        </div>
                        @if (! empty($step['help']))<p class="wk-hint">{{ $step['help'] }}</p>@endif
                    @endif
                    </div>
                </li>
            @endforeach
            </ol>

            <button type="button" class="wk-btn wk-btn-primary wk-btn-block kit-next" id="kNext">İlerle</button>
        </form>

        <aside class="wk-tool-result" aria-label="Tahmini maliyet">
            <section class="wk-result" id="resultCard" aria-live="polite" aria-labelledby="kResT">
                <div class="wk-result-top">
                    <p class="wk-eyebrow" id="kResT">Tahmini maliyet aralığı</p>
                    <p class="wk-figure {{ $ready ? '' : 'is-waiting' }}" id="kTotal">
                        @if ($ready)<span><span class="wk-cur">₺</span>{{ number_format($est['low'], 0, ',', '.') }}</span><span class="wk-sep">–</span><span><span class="wk-cur">₺</span>{{ number_format($est['high'], 0, ',', '.') }}</span>@else Seçim bekleniyor @endif
                    </p>
                    <p class="wk-small kit-typ" id="kTyp">@if ($ready) En olası tutar <b>{{ $tl($est['mid']) }}</b>@else {{ $page['wait'] }} @endif</p>
                    <div class="kit-fx" id="kFx" hidden></div>
                    <ul class="kit-notes" id="kNotes"></ul>
                </div>
                <div class="wk-result-body">
                    <ul class="wk-lines" id="kLines">
                        @if ($ready)
                            @php $kSum = max(1, array_sum(array_column($est['items'], 'mid'))); @endphp
                            @foreach ($est['items'] as $ik => $it)
                                <li><span>{{ $it['label'] }}@if (! empty($sponsorLines[$ik]))<a class="bt-spl" href="{{ $sponsorLines[$ik]['url'] }}" target="_blank" rel="sponsored nofollow noopener" data-sponsor="{{ $sponsorLines[$ik]['id'] }}">Sponsorlu · {{ $sponsorLines[$ik]['brand'] }}</a>@endif</span><span>{{ $tl($it['mid']) }}</span><span class="wk-share"><i style="width:{{ round($it['mid'] / $kSum * 100, 1) }}%"></i></span></li>
                            @endforeach
                        @else
                            <li class="kit-empty"><span>Kalemler seçimlerinize göre burada listelenir</span><span>—</span></li>
                        @endif
                    </ul>
                    <dl class="kit-meta" id="kMeta"></dl>
                    <p class="wk-result-meta"><span>Fiyatlar: {{ $e->pricesLabel() }}</span><span aria-hidden="true">·</span><span id="kVat">{{ $e instanceof \App\Estimators\PozEstimator ? 'KDV ayrı kalemde' : 'KDV dahil' }}</span><span aria-hidden="true">·</span><a href="#yontem">Yöntem</a></p>
                    <p class="wk-result-note">{{ config('worksitem_legal.result_short') }} <a href="/legal/kullanim-kosullari">Koşullar</a></p>
                    <div class="wk-result-actions">
                        <button type="button" class="wk-btn wk-btn-primary wk-btn-block" id="quoteBtn" @disabled(! $ready)>Bu aralıkla teklif iste</button>
                        <a class="kit-link kit-pro" id="proBtn" href="{{ route('tools.kit.pro', ['kit' => $slug]) }}" data-kainta-auto-link="off" @if (! $ready) hidden @endif>
                            <span class="wk-icon"><svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/></svg></span>
                            <span><b>Pro rapor: malzeme listesi, iş programı{{ collect($steps)->firstWhere('key', 'quality') ? ', 3 kalite karşılaştırması' : '' }}</b><span class="wk-small">Ücretsiz · telefonunuza gelen kodla 1 dakikada indirin</span></span>
                        </a>
                        @if ($plan = $e->planner())
                            <a class="kit-link" id="planBtn" href="{{ $plan['url'] }}" data-kainta-auto-link="off">
                                <span class="wk-icon"><svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h8v12M15 3v6h6"/></svg></span>
                                <span><b>{{ $plan['label'] }}</b><span class="wk-small">{{ $plan['hint'] }}</span></span>
                            </a>
                        @endif
                        @foreach ($e->visuals() as $vis)
                            <a class="kit-link bt-vis" href="{{ $vis['url'] }}" data-kainta-auto-link="off" data-visual="{{ parse_url($vis['url'], PHP_URL_PATH) }}">
                                <span class="wk-icon"><svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></span>
                                <span><b>{{ $vis['label'] }}</b><span class="wk-small">{{ $vis['hint'] }}</span></span>
                            </a>
                        @endforeach
                        <div class="bt-share"><button type="button" class="wk-btn wk-btn-ghost wk-btn-block" id="shareBtn"><svg class="wk-i wk-i-sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg> Bu hesabı paylaş</button></div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <div class="kit-sponsors"><x-sponsor.slot :tool="$slug" :title="$page['sponsor_title'] ?? 'Projeniz için markalar'" /></div>

    <div class="bt-lead-area kit-lead" id="teklif">
        @if ($errors->any() && ! $errors->has('kit'))
            <div class="wk-note wk-note-danger" role="alert">
                <span><strong>Teklif talebiniz gönderilemedi:</strong></span>
                <ul>@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif
        <x-market.choice :tool="$slug" />
        <div class="budget-confirm-box" id="budgetConfirmBox">
            <div class="budget-confirm-title">Bu bütçe aralığı projeniz için gerçekçi mi?</div>
            <div class="budget-confirm-text">Kainta İnşaat'a başvurmadan önce bütçenizin bu seviyede olduğunu onaylayın. Böylece yalnızca gerçekten uygulanabilir projeleri değerlendirmeye alabiliriz.</div>
            <div class="budget-confirm-actions">
                <button type="button" class="budget-btn budget-yes" id="btnYes">Evet, bu bütçeyi karşılayabilirim</button>
                <button type="button" class="budget-btn budget-no" id="btnNo">Henüz bütçemi netleştirmedim</button>
            </div>
        </div>
        <div class="qualified-message" id="qualifiedMessage">
            <strong>Projeniz Kainta İnşaat değerlendirmesine uygun görünüyor.</strong><br>
            Formu doldurduğunuzda keşif ve teklif süreci için sizinle iletişime geçilecek.
        </div>
        <div class="lead-box" id="leadBox" @if ($errors->any() && ! $errors->has('kit')) style="display:block" @endif>
            <h3>{{ $page['lead_title'] ?? $page['service'].' için teklif alın' }}</h3>
            <p>{{ $page['lead_text'] ?? 'Hesapladığınız bilgiler forma eklenir. Kainta İnşaat uzmanları yerinde ölçü alıp yazılı teklif hazırlar.' }}</p>
            <p class="wk-small"><x-inspection-fee-note /></p>
            <form action="{{ route('lead.store') }}" method="POST">
                @csrf
                <input type="hidden" name="calculated_service" value="{{ $page['service'] }}">
                @if (! empty($page['lead_source']))<input type="hidden" name="source" value="{{ $page['lead_source'] }}"><input type="hidden" name="source_tool" value="{{ $slug }}">@endif
                <input type="hidden" name="calculated_area" id="leadCalculatedData" value="{{ $ready ? $est['summary'] : '' }}">
                <input type="hidden" name="estimated_min" id="leadMin" value="{{ $ready ? $est['low'] : '' }}">
                <input type="hidden" name="estimated_max" id="leadMax" value="{{ $ready ? $est['high'] : '' }}">
                <input type="hidden" name="estimated_cost" id="leadMid" value="{{ $ready ? $est['mid'] : '' }}">
                <input type="hidden" name="budget_confirmed" value="1">
                <div class="form-group"><label for="ld_name">Adınız soyadınız</label><input type="text" id="ld_name" name="name" value="{{ old('name') }}" class="form-control" required autocomplete="name"></div>
                <div class="form-group"><label for="ld_phone">Telefon</label><input type="tel" id="ld_phone" name="phone" value="{{ old('phone') }}" class="form-control" required autocomplete="tel" placeholder="05XX XXX XX XX"></div>
                <div class="form-group"><label for="ld_loc">Şehir / ilçe</label><input type="text" id="ld_loc" name="location" value="{{ old('location') }}" class="form-control" required placeholder="Örn: İstanbul, Kadıköy"></div>
                <div class="form-group"><label for="ld_notes">Notunuz (isteğe bağlı)</label><textarea id="ld_notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
                <x-lead-consent-fields />
                <button type="submit" class="btn-submit" {!! \App\Support\Ab::attr('tool_lead_submit') !!}>{{ \App\Support\Ab::text('tool_lead_submit') }}</button>
                <x-site.ab-script />
            </form>
        </div>
    </div>

    @if (! empty($page['extra_view']))
        @include($page['extra_view'])
    @endif
</main>

<section class="wk-band-surface" id="yontem">
    <div class="wk-container wk-section wk-split">
        <div class="wk-stack" style="gap:var(--wk-space-3)">
            <p class="wk-eyebrow">Yöntem</p>
            <h2 class="wk-h2">Nasıl hesaplıyoruz?</h2>
            <ol class="kit-method">@foreach ($page['method'] as $m)<li>{{ $m }}</li>@endforeach</ol>
        </div>
        <div class="wk-stack" style="gap:var(--wk-space-3)">
            <h2 class="wk-h3">Fiyatı en çok ne etkiler?</h2>
            <ul class="kit-factors">@foreach ($page['factors'] as [$t, $d])<li><b>{{ $t }}</b><span>{{ $d }}</span></li>@endforeach</ul>
        </div>
    </div>
</section>

<section class="wk-container wk-section wk-split" id="sss">
    <div class="wk-stack" style="gap:var(--wk-space-3)">
        <p class="wk-eyebrow">Sık sorulanlar</p>
        <h2 class="wk-h2">Sık sorulan sorular</h2>
        <p class="wk-small">Fiyatlar: {{ $e->pricesLabel() }} · Hazırlayan: Worksitem / <a href="https://kainta.com" rel="noopener">Kainta İnşaat</a> teknik ekibi</p>
    </div>
    <div class="wk-faq">
        @foreach ($page['faq'] as $i => [$q, $a])
            <details @if ($i === 0) open @endif><summary>{{ $q }}</summary><div>{{ $a }}</div></details>
        @endforeach
    </div>
</section>

<div class="wk-container"><x-sponsor.brand-cta :tool="$slug" /></div>

<section class="wk-container kit-related" aria-labelledby="kRelT">
    <h2 class="wk-h3" id="kRelT">İlgili hesaplayıcılar</h2>
    <ul class="wk-toollist kit-rel">@foreach ($page['related'] as [$href, $label])<li><a href="{{ $href }}"><span><b>{{ $label }}</b></span><svg class="wk-i" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg></a></li>@endforeach</ul>
</section>

<div class="kit-dock is-hidden" id="kDock" aria-hidden="true">
    <div><small>Tahmini maliyet</small><strong id="kDockRange">Seçim bekleniyor</strong></div>
    <button type="button" class="wk-btn wk-btn-primary" id="kDockBtn" tabindex="-1">Dökümü gör</button>
</div>

<script type="application/json" id="kCfg">{!! json_encode([
    'calc' => route('tools.kit.calc', ['kit' => $slug]),
    'pro' => route('tools.kit.pro', ['kit' => $slug]),
    'wait' => $page['wait'],
    'perUnit' => $page['per_unit_label'] ?? null,
    'slug' => $slug,
    'planner' => ($pl = $e->planner()) ? ['map' => $pl['map'], 'checks' => $pl['checks'] ?? []] : null,
], $jsonFlags) !!}</script>
<script>
(function () {
    'use strict';
    var C = JSON.parse(document.getElementById('kCfg').textContent);
    var $ = function (id) { return document.getElementById(id); };
    var form = $('kForm');
    var nf = new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 });
    var tl = function (v) { return '₺' + nf.format(v); };
    var esc = function (s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
    var last = null, timer = null, seq = 0;

    function params() {
        var q = new URLSearchParams();
        new FormData(form).forEach(function (v, k) { if (String(v).trim() !== '') q.append(k, v); });
        return q;
    }

    function render(res) {
        var card = $('resultCard');
        card.classList.remove('bt-loading');
        if (!res.ready) {
            last = null;
            $('kTotal').textContent = 'Seçim bekleniyor'; $('kTotal').classList.add('is-waiting');
            $('kTyp').textContent = res.blocked ? res.blocked : (res.missing && res.missing.length ? 'Sonuç için eksik: ' + res.missing.join(', ') + '. Seçtikçe maliyet burada anında hesaplanır.' : C.wait);
            if (res.blocked) { $('kTotal').textContent = 'Fiyat yok'; }
            $('kFx').hidden = true; $('kMeta').innerHTML = ''; $('kNotes').innerHTML = '';
            $('kLines').innerHTML = '<li class="kit-empty"><span>Kalemler seçimlerinize göre burada listelenir</span><span>—</span></li>';
            $('quoteBtn').disabled = true; $('proBtn').hidden = true;
            $('kDockRange').textContent = 'Seçim bekleniyor';
            ['leadMin', 'leadMax', 'leadMid', 'leadCalculatedData'].forEach(function (id) { $(id).value = ''; });
            dock();
            return;
        }
        last = res;
        document.querySelectorAll('.bt-step.is-missing,.bt-input.is-missing').forEach(function (el) { el.classList.remove('is-missing'); });
        $('kTotal').classList.remove('is-waiting');
        $('kTotal').innerHTML = '<span><span class="wk-cur">₺</span>' + nf.format(res.low) + '</span><span class="wk-sep">–</span><span><span class="wk-cur">₺</span>' + nf.format(res.high) + '</span>';
        $('kTyp').innerHTML = 'En olası tutar <b>' + tl(res.mid) + '</b>' + (res.per_unit && res.per_unit_label ? ' · ' + esc(res.per_unit_label) + ' yaklaşık <b>' + tl(res.per_unit) + '</b>' : '');
        if (res.fx && res.fx.length) {
            $('kFx').innerHTML = res.fx.map(function (f) { return '<span>≈ ' + f[0] + nf.format(f[1]) + ' – ' + f[0] + nf.format(f[2]) + '</span>'; }).join('') + '<span><em>TCMB satış kuru, ' + esc(res.fx_date || '') + '</em></span>';
            $('kFx').hidden = false;
        } else { $('kFx').hidden = true; }
        $('kNotes').innerHTML = (res.notes || []).map(function (n) { return '<li>' + esc(n) + '</li>'; }).join('');
        var total = 0, lines = '';
        Object.keys(res.items).forEach(function (k) { total += res.items[k].mid; });
        Object.keys(res.items).forEach(function (k) {
            var it = res.items[k];
            var sp = res.sponsor_lines && res.sponsor_lines[k];
            lines += '<li><span>' + esc(it.label)
                + (sp ? '<a class="bt-spl" href="' + esc(sp.url) + '" target="_blank" rel="sponsored nofollow noopener" data-sponsor="' + sp.id + '">Sponsorlu · ' + esc(sp.brand) + '</a>' : '')
                + '</span><span>' + tl(it.mid) + '</span><span class="wk-share"><i style="width:' + (total ? (it.mid / total * 100).toFixed(1) : 0) + '%"></i></span></li>';
        });
        $('kLines').innerHTML = lines;
        $('kMeta').innerHTML = (res.meta || []).map(function (m) { return '<div><dt>' + esc(m[0]) + '</dt><dd>' + esc(m[1]) + '</dd></div>'; }).join('');
        $('quoteBtn').disabled = false;
        var q = params().toString();
        $('proBtn').href = C.pro + '?' + q; $('proBtn').hidden = false;
        $('kDockRange').textContent = tl(res.low) + ' – ' + tl(res.high);
        $('leadMin').value = res.low; $('leadMax').value = res.high; $('leadMid').value = res.mid; $('leadCalculatedData').value = (res.summary || '').slice(0, 100);
        try { history.replaceState(null, '', location.pathname + (q ? '?' + q : '')); } catch (e) {}
        dock();
    }

    // Koşullu adımlar: seçime göre göster/gizle, gizli adımın alanları gönderilmez, adımlar yeniden numaralanır
    function applyShow() {
        var cur = {}, keep = {};
        form.querySelectorAll('input[type=radio]:checked').forEach(function (r) { keep[r.name] = r.value; if (!r.disabled) cur[r.name] = r.value; });
        form.querySelectorAll('.bt-step[data-show],.bt-step[data-hide]').forEach(function (st) {
            var show = {}, hide = {};
            try { show = JSON.parse(st.getAttribute('data-show') || '{}'); hide = JSON.parse(st.getAttribute('data-hide') || '{}'); } catch (e) {}
            var ok = Object.keys(show).every(function (k) { return show[k].map(String).indexOf(String(cur[k] || '')) !== -1; })
                && !Object.keys(hide).some(function (k) { return hide[k].map(String).indexOf(String(cur[k] || '')) !== -1; });
            st.hidden = !ok;
            st.querySelectorAll('input').forEach(function (inp) { inp.disabled = !ok; });
        });
        // Aynı anahtarlı iki adım arasında geçişte seçim korunur (ör. iç/dış cephe boya kalitesi)
        form.querySelectorAll('.bt-step:not([hidden])').forEach(function (st) {
            var r = st.querySelector('input[type=radio]');
            if (r && !st.querySelector('input[type=radio]:checked') && keep[r.name]) {
                var m = st.querySelector('input[type=radio][value="' + keep[r.name] + '"]'); if (m) m.checked = true;
            }
        });
        var i = 0;
        form.querySelectorAll('.bt-step').forEach(function (st) { if (!st.hidden) { var n = st.querySelector('.wk-step-n'); if (n) n.textContent = ++i; } });
    }
    applyShow();

    function calc() {
        applyShow();
        var my = ++seq;
        $('resultCard').classList.add('bt-loading');
        fetch(C.calc + '?' + params().toString(), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) { if (my === seq) render(res); })
            .catch(function () { $('resultCard').classList.remove('bt-loading'); });
    }
    function schedule(delay) { clearTimeout(timer); timer = setTimeout(calc, delay); }

    // Sayı alanları metin + ondalık klavye: "2,5" virgülle yazılır, sunucu virgülü noktaya çevirir (tarayıcı virgülü silmez)
    form.addEventListener('input', function (e) { schedule(e.target.inputMode === 'decimal' ? 350 : 0); });
    form.addEventListener('change', function () { schedule(0); });

    function showConfirm() { if ($('leadBox').style.display !== 'block') $('budgetConfirmBox').style.display = 'block'; }
    $('kNext').addEventListener('click', function () {
        if (!last) {
            var miss = null;
            form.querySelectorAll('.bt-step:not([hidden])').forEach(function (st) {
                var radios = st.querySelectorAll('input[type=radio]');
                if (radios.length && !st.querySelector('input[type=radio]:checked')) { st.classList.add('is-missing'); miss = miss || st; }
            });
            (miss || form).scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        showConfirm();
        $('resultCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    // Planlayıcı bağlantısı: güncel girdiler plana taşınır; plandan gelindiyse plan bağlantısı teklif notuna eklenir
    if (C.planner && $('planBtn')) {
        $('planBtn').addEventListener('click', function () {
            var fd = new FormData(form), q = new URLSearchParams();
            Object.keys(C.planner.map).forEach(function (k) { var v = fd.get(k); if (v !== null && String(v).trim() !== '') q.set(C.planner.map[k], v); });
            Object.keys(C.planner.checks).forEach(function (g) {
                var on = fd.getAll(g + '[]');
                C.planner.checks[g].forEach(function (v) { q.set(v, on.indexOf(v) >= 0 ? '1' : '0'); });
            });
            $('planBtn').href = $('planBtn').href.split('?')[0] + '?' + q.toString();
        });
    }
    (function () {
        var plan = null, notes = $('ld_notes');
        try { plan = sessionStorage.getItem('ws_bath_plan'); } catch (e) {}
        if (!plan || !notes || notes.value || !/^https?:\/\/[^\s]+\/araclar\/[a-z-]+-planlayici/.test(plan)) return;
        notes.value = 'Planım: ' + plan;
        try { sessionStorage.removeItem('ws_bath_plan'); } catch (e) {}
        if (location.hash === '#teklif') setTimeout(showConfirm, 50);
    })();
    $('quoteBtn').addEventListener('click', function () { showConfirm(); $('budgetConfirmBox').scrollIntoView({ behavior: 'smooth', block: 'center' }); });
    $('btnYes').addEventListener('click', function () {
        $('budgetConfirmBox').style.display = 'none'; $('qualifiedMessage').style.display = 'block'; $('leadBox').style.display = 'block';
        setTimeout(function () { $('qualifiedMessage').scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 100);
    });
    $('btnNo').addEventListener('click', function () {
        $('budgetConfirmBox').innerHTML = '<div class="budget-confirm-title">Sorun değil.</div><div class="budget-confirm-text" style="margin-bottom:0">Bu aşamada iletişim bilgilerinizi almıyoruz. Bütçeniz netleştiğinde tekrar teklif isteyebilirsiniz.</div>';
        $('leadBox').style.display = 'none'; $('qualifiedMessage').style.display = 'none';
    });
    // Paylaş: telefonda sistem paylaşım menüsü; bilgisayarda (Windows paylaşım penceresi çoğu zaman açılmaz)
    // bağlantı kopyalanır ve altında Kopyala / WhatsApp / E-posta paneli açılır.
    function copyLink(t) {
        if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(t);
        return new Promise(function (ok, no) {
            var ta = document.createElement('textarea'); ta.value = t; ta.setAttribute('readonly', ''); ta.style.cssText = 'position:fixed;left:-9999px';
            document.body.appendChild(ta); ta.select();
            try { document.execCommand('copy') ? ok() : no(); } catch (e) { no(e); } finally { ta.remove(); }
        });
    }
    function sharePanel(link) {
        var box = $('kShare');
        if (!box) {
            box = document.createElement('div'); box.id = 'kShare'; box.className = 'bt-sharepanel';
            box.innerHTML = '<div class="bt-sharerow"><input type="text" readonly aria-label="Paylaşım bağlantısı" id="kShareLink"><button type="button" id="kShareCopy">Kopyala</button></div>'
                + '<div class="bt-sharerow"><a id="kShareWa" target="_blank" rel="noopener">WhatsApp ile gönder</a><a id="kShareMail">E-posta ile gönder</a></div>'
                + '<small id="kShareMsg" role="status" aria-live="polite"></small>';
            $('shareBtn').parentNode.appendChild(box);
            $('kShareCopy').addEventListener('click', function () {
                copyLink($('kShareLink').value).then(function () { $('kShareMsg').textContent = '✓ Bağlantı kopyalandı'; },
                    function () { $('kShareLink').select(); $('kShareMsg').textContent = 'Bağlantı seçildi; Ctrl+C ile kopyalayın.'; });
            });
        }
        $('kShareLink').value = link;
        $('kShareWa').href = 'https://wa.me/?text=' + encodeURIComponent(document.title + ' ' + link);
        $('kShareMail').href = 'mailto:?subject=' + encodeURIComponent(document.title) + '&body=' + encodeURIComponent(link);
        box.hidden = false;
        copyLink(link).then(function () { $('kShareMsg').textContent = '✓ Bağlantı kopyalandı; aynı seçimlerle açılır.'; }, function () { $('kShareMsg').textContent = ''; });
    }
    $('shareBtn').addEventListener('click', function () {
        var link = location.href;
        if (window.wsTrack) wsTrack('tool_share', { tool: document.body.getAttribute('data-tool-slug') });
        if (navigator.share && /Mobi|Android|iPhone|iPad/i.test(navigator.userAgent)) {
            navigator.share({ title: document.title, url: link }).catch(function (e) { if (!e || e.name !== 'AbortError') sharePanel(link); });
            return;
        }
        sharePanel(link);
    });
    $('kDockBtn').addEventListener('click', function () { $('resultCard').scrollIntoView({ behavior: 'smooth', block: 'start' }); });

    var seen = new Set(), dockEl = $('kDock');
    function dock() { dockEl.classList.toggle('is-hidden', seen.size > 0 || !last); }
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) { e.isIntersecting ? seen.add(e.target) : seen.delete(e.target); });
            dock();
        }, { threshold: 0.15 });
        [$('resultCard'), document.querySelector('.bt-lead-area')].forEach(function (el) { io.observe(el); });
    }

    calc();
})();
</script>

<x-site.contech-footer />
<script src="/js/tool-events.js" defer></script>
<script src="/js/p1-product-events.js" defer></script>
</body>
</html>
