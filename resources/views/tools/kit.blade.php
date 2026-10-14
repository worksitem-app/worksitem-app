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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style data-asset="ws-estimator.css">{!! \App\Support\Asset::inline('css/ws-estimator.css') !!}</style>
    @foreach ($schema as $s)
        <script type="application/ld+json">{!! json_encode($s, $jsonFlags) !!}</script>
    @endforeach
</head>
<body data-tool-slug="{{ $slug }}">

<x-site.contech-header />

<main class="bt-wrap">
    <nav class="bt-crumb" aria-label="Sayfa yolu">
        <a href="/">Ana sayfa</a> / <a href="/araclar">Araçlar</a> / <span>{{ $page['title'] }}</span>
    </nav>

    <header class="bt-head">
        <h1>{{ $page['h1'] }}</h1>
        <p class="bt-lede">{{ $page['lede'] }}</p>
        @if ($quick)
            <p class="bt-answer"><b>Kısa cevap:</b> {{ $quick[1] }}</p>
        @endif
        <div class="bt-facts">
            @foreach ($page['facts'] ?? [] as $f)<span>{{ $f }}</span>@endforeach
            <span>Fiyatlar: {{ $e->pricesLabel() }}</span>
        </div>
    </header>

    @if ($errors->has('kit'))
        <div class="bt-errors" role="alert" style="margin-top:16px">{{ $errors->first('kit') }}</div>
    @endif

    <div class="bt-grid">
        <form class="bt-form" id="kForm" onsubmit="return false" aria-label="Hesap bilgileri">
            @foreach ($steps as $step)
                @php $n++; @endphp
                <section class="bt-step" data-step="{{ $step['key'] ?? 'numbers'.$n }}">
                    <h2><i>{{ $n }}</i>{{ $step['title'] }}
                        @if ($step['type'] === 'checks' || ($step['type'] === 'cards' && ! ($step['required'] ?? false)))<small class="bt-opt-note">isteğe bağlı</small>@endif
                    </h2>
                    @if ($step['type'] === 'cards')
                        <div class="bt-opts c{{ $step['cols'] ?? 3 }}">
                            @foreach ($step['options'] as $k => $o)
                                <div class="bt-opt">
                                    <input type="radio" name="{{ $step['key'] }}" id="o_{{ $step['key'] }}_{{ $k }}" value="{{ $k }}" @checked(($input[$step['key']] ?? '') === (string) $k)>
                                    <label for="o_{{ $step['key'] }}_{{ $k }}"><b>{{ $o['label'] }}</b>@if (! empty($o['hint']))<small>{{ $o['hint'] }}</small>@endif</label>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($step['type'] === 'numbers')
                        <div class="bt-dims c{{ $step['cols'] ?? count($step['fields']) }}">
                            @foreach ($step['fields'] as $f)
                                <div class="bt-field">
                                    <label for="f_{{ $f['key'] }}">{{ $f['label'] }}</label>
                                    <div class="bt-input"><input type="number" id="f_{{ $f['key'] }}" name="{{ $f['key'] }}" inputmode="decimal" min="{{ $f['min'] }}" max="{{ $f['max'] }}" step="{{ $f['step'] }}" placeholder="{{ $f['placeholder'] ?? '' }}"
                                        value="{{ ! empty($input['_given_'.$f['key']]) ? $input[$f['key']] : '' }}"><span>{{ $f['unit'] }}</span></div>
                                </div>
                            @endforeach
                        </div>
                        @if (! empty($step['help']))<p class="bt-area">{{ $step['help'] }}</p>@endif
                    @elseif ($step['type'] === 'checks')
                        <div class="bt-checks">
                            @foreach ($step['options'] as $k => $label)
                                <label class="bt-check"><input type="checkbox" name="{{ $step['key'] }}[]" value="{{ $k }}" @checked(in_array($k, $input[$step['key']] ?? [], true))> {{ $label }}</label>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach

            <button type="button" class="bt-cta" id="kNext">İlerle</button>
        </form>

        <aside class="bt-result" id="resultCard" aria-live="polite" aria-label="Tahmini maliyet">
            <div class="bt-r-top">
                <div class="bt-r-label">Tahmini maliyet aralığı</div>
                <div class="bt-range {{ $ready ? '' : 'is-waiting' }}" id="kTotal">
                    @if ($ready){{ $tl($est['low']) }}<span class="sep">–</span>{{ $tl($est['high']) }}@else Seçim bekleniyor @endif
                </div>
                <div class="bt-typ" id="kTyp">@if ($ready) En olası tutar <b>{{ $tl($est['mid']) }}</b>@else {{ $page['wait'] }} @endif</div>
                <div class="bt-fx" id="kFx" hidden></div>
                <ul class="bt-r-notes" id="kNotes"></ul>
                <div class="bt-ruler" aria-hidden="true"><i></i></div>
                <div class="bt-ruler-l" aria-hidden="true"><span>Uygun usta / yerel ürün</span><span>Tasarım ürünü / büyük şehir</span></div>
            </div>
            <div class="bt-r-body">
                <div class="bt-bar" id="kBar" aria-hidden="true"></div>
                <ul class="bt-lines" id="kLines">
                    @if ($ready)
                        @foreach ($est['items'] as $it)
                            <li><i style="background:{{ $it['color'] }}"></i><span>{{ $it['label'] }}</span><b>{{ $tl($it['low']) }} – {{ $tl($it['high']) }}</b></li>
                        @endforeach
                    @else
                        <li><i></i><span>Kalemler seçimlerinize göre burada listelenir</span><b>—</b></li>
                    @endif
                </ul>
                <div class="bt-meta" id="kMeta"></div>
                <p class="bt-disc">{{ config('worksitem_legal.result_short') }} <a href="/legal/kullanim-kosullari">Koşullar</a></p>
                <button type="button" class="bt-quote" id="quoteBtn" @disabled(! $ready)>Bu aralıkla teklif iste</button>
                <a class="bt-pro" id="proBtn" href="{{ route('tools.kit.pro', ['kit' => $slug]) }}" data-kainta-auto-link="off" @if (! $ready) hidden @endif>
                    <span class="bt-pro-tag">Ücretsiz</span>
                    <strong>Pro rapor: malzeme listesi, iş programı{{ collect($steps)->firstWhere('key', 'quality') ? ', 3 kalite karşılaştırması' : '' }}</strong>
                    <small>Telefonunuza gelen kodla 1 dakikada indirin</small>
                </a>
                @if ($plan = $e->planner())
                    <a class="bt-plan" id="planBtn" href="{{ $plan['url'] }}" data-kainta-auto-link="off">
                        <svg width="34" height="34" viewBox="0 0 34 34" aria-hidden="true"><rect x="3" y="3" width="28" height="28" rx="4" fill="none" stroke="currentColor" stroke-width="2"/><rect x="6" y="6" width="10" height="10" rx="1.5" fill="#FFB800"/><path d="M6 24h8M24 20v8" stroke="currentColor" stroke-width="1.6"/></svg>
                        <span><strong>{{ $plan['label'] }}</strong><small>{{ $plan['hint'] }}</small></span>
                    </a>
                @endif
                <div class="bt-share"><button type="button" id="shareBtn">🔗 Bu hesabı paylaş</button></div>
            </div>
        </aside>
    </div>

    <div class="bt-sponsors"><x-sponsor.slot :tool="$slug" :title="$page['sponsor_title'] ?? 'Projeniz için markalar'" /></div>

    <div class="bt-lead-area" id="teklif">
        @if ($errors->any() && ! $errors->has('kit'))
            <div class="bt-errors" role="alert">
                <strong>Teklif talebiniz gönderilemedi:</strong>
                <ul>@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif
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
            <p class="bt-small" style="margin-top:-8px"><x-inspection-fee-note /></p>
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
                <div class="form-group"><label for="ld_loc">Şehir / ilçe</label><input type="text" id="ld_loc" name="location" value="{{ old('location') }}" class="form-control" required placeholder="Örn: İzmir, Karşıyaka"></div>
                <div class="form-group"><label for="ld_notes">Notunuz (isteğe bağlı)</label><textarea id="ld_notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
                <x-lead-consent-fields />
                <button type="submit" class="btn-submit">Teklif talebini gönder</button>
            </form>
        </div>
    </div>

    @if (! empty($page['extra_view']))
        @include($page['extra_view'])
    @endif

    <section class="bt-sec">
        <h2>Nasıl hesaplıyoruz?</h2>
        <ol class="bt-method">@foreach ($page['method'] as $m)<li>{{ $m }}</li>@endforeach</ol>
    </section>

    <section class="bt-sec">
        <h2>Fiyatı en çok ne etkiler?</h2>
        <ul class="bt-factors">@foreach ($page['factors'] as [$t, $d])<li><b>{{ $t }}</b><span>{{ $d }}</span></li>@endforeach</ul>
    </section>

    <section class="bt-sec bt-faq">
        <h2>Sık sorulan sorular</h2>
        @foreach ($page['faq'] as $i => [$q, $a])
            <details @if ($i === 0) open @endif><summary>{{ $q }}</summary><p>{{ $a }}</p></details>
        @endforeach
        <p class="bt-small">Fiyatlar: {{ $e->pricesLabel() }} · Hazırlayan: Worksitem / <a href="https://kainta.com" rel="noopener">Kainta İnşaat</a> teknik ekibi</p>
    </section>

    <x-sponsor.brand-cta :tool="$slug" />

    <section class="bt-sec">
        <h2>İlgili hesaplayıcılar</h2>
        <div class="bt-related">@foreach ($page['related'] as [$href, $label])<a href="{{ $href }}">{{ $label }}</a>@endforeach</div>
    </section>
</main>

<div class="bt-dock is-hidden" id="kDock" aria-hidden="true">
    <div><small>Tahmini maliyet</small><strong id="kDockRange">Seçim bekleniyor</strong></div>
    <button type="button" id="kDockBtn" tabindex="-1">Dökümü gör</button>
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
            $('kFx').hidden = true; $('kBar').innerHTML = ''; $('kMeta').innerHTML = ''; $('kNotes').innerHTML = '';
            $('kLines').innerHTML = '<li><i></i><span>Kalemler seçimlerinize göre burada listelenir</span><b>—</b></li>';
            $('quoteBtn').disabled = true; $('proBtn').hidden = true;
            $('kDockRange').textContent = 'Seçim bekleniyor';
            ['leadMin', 'leadMax', 'leadMid', 'leadCalculatedData'].forEach(function (id) { $(id).value = ''; });
            dock();
            return;
        }
        last = res;
        document.querySelectorAll('.bt-step.is-missing,.bt-input.is-missing').forEach(function (el) { el.classList.remove('is-missing'); });
        $('kTotal').classList.remove('is-waiting');
        $('kTotal').innerHTML = tl(res.low) + '<span class="sep">–</span>' + tl(res.high);
        $('kTyp').innerHTML = 'En olası tutar <b>' + tl(res.mid) + '</b>' + (res.per_unit && res.per_unit_label ? ' · ' + esc(res.per_unit_label) + ' yaklaşık <b>' + tl(res.per_unit) + '</b>' : '');
        if (res.fx && res.fx.length) {
            $('kFx').innerHTML = res.fx.map(function (f) { return '<span>≈ ' + f[0] + nf.format(f[1]) + ' – ' + f[0] + nf.format(f[2]) + '</span>'; }).join('') + '<span><em>TCMB satış kuru, ' + esc(res.fx_date || '') + '</em></span>';
            $('kFx').hidden = false;
        } else { $('kFx').hidden = true; }
        $('kNotes').innerHTML = (res.notes || []).map(function (n) { return '<li>' + esc(n) + '</li>'; }).join('');
        var total = 0, bar = '', lines = '';
        Object.keys(res.items).forEach(function (k) { total += res.items[k].mid; });
        Object.keys(res.items).forEach(function (k) {
            var it = res.items[k];
            bar += '<span style="width:' + (it.mid / total * 100).toFixed(2) + '%;background:' + it.color + '"></span>';
            lines += '<li><i style="background:' + it.color + '"></i><span>' + esc(it.label) + '</span><b>' + tl(it.low) + ' – ' + tl(it.high) + '</b></li>';
        });
        $('kBar').innerHTML = bar; $('kLines').innerHTML = lines;
        $('kMeta').innerHTML = (res.meta || []).map(function (m) { return '<div>' + esc(m[0]) + '<strong>' + esc(m[1]) + '</strong></div>'; }).join('');
        $('quoteBtn').disabled = false;
        var q = params().toString();
        $('proBtn').href = C.pro + '?' + q; $('proBtn').hidden = false;
        $('kDockRange').textContent = tl(res.low) + ' – ' + tl(res.high);
        $('leadMin').value = res.low; $('leadMax').value = res.high; $('leadMid').value = res.mid; $('leadCalculatedData').value = (res.summary || '').slice(0, 100);
        try { history.replaceState(null, '', location.pathname + (q ? '?' + q : '')); } catch (e) {}
        dock();
    }

    function calc() {
        var my = ++seq;
        $('resultCard').classList.add('bt-loading');
        fetch(C.calc + '?' + params().toString(), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) { if (my === seq) render(res); })
            .catch(function () { $('resultCard').classList.remove('bt-loading'); });
    }
    function schedule(delay) { clearTimeout(timer); timer = setTimeout(calc, delay); }

    form.addEventListener('input', function (e) { schedule(e.target.type === 'number' ? 350 : 0); });
    form.addEventListener('change', function () { schedule(0); });

    function showConfirm() { if ($('leadBox').style.display !== 'block') $('budgetConfirmBox').style.display = 'block'; }
    $('kNext').addEventListener('click', function () {
        if (!last) {
            var miss = null;
            form.querySelectorAll('.bt-step').forEach(function (st) {
                var radios = st.querySelectorAll('input[type=radio]');
                if (radios.length && !st.querySelector('input[type=radio]:checked')) { st.classList.add('is-missing'); miss = miss || st; }
            });
            form.querySelectorAll('input[type=number]').forEach(function (inp) { if (inp.value === '' && inp.closest('.bt-step') === (miss || inp.closest('.bt-step'))) {} });
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
