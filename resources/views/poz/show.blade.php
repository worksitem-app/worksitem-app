@extends('poz._layout')

@if (config('worksitem_seo.poz_index_only_enriched', false) && ! $poz->seo_enriched)
@section('robots', 'noindex,follow')
@endif
@php
  // v113 başlık: aranan ifade başta ("<poz no> Birim Fiyatı <yıl>"), tanım kısaltılır, kurum sonda
  $pozHead = $poz->poz_no.' Birim Fiyatı'.($poz->year ? ' '.$poz->year : '');
  $pozInst = \App\Support\PozCatalog::shortInstitution($poz->institution);
  $pozRoom = max(18, 68 - mb_strlen($pozHead) - mb_strlen($pozInst) - 6);
  $pozTitle = $pozHead.' – '.\Illuminate\Support\Str::limit(\Illuminate\Support\Str::ucfirst(trim((string) $poz->description)), $pozRoom, '…').($pozInst !== '' ? ' | '.$pozInst : '');
  $pozDesc = $poz->meta_description ?: ($poz->poz_no.' pozunun '.($poz->year ? $poz->year.' ' : '').'birim fiyatı '.$priceFormatted.' TL/'.$poz->unit.' ('.$poz->institution.'). Miktarı girin, toplam tutarı KDV dahil hemen görün; ilgili pozlar ve maliyet hesabı.');
@endphp
@section('title', $pozTitle)
@section('description', $pozDesc)
@section('canonical', \App\Support\PozUrl::absolute($poz->poz_no))

@section('breadcrumb')
  <a href="/pozlar">Pozlar</a><span>›</span>
  <a href="{{ $institutionUrl }}">{{ $poz->institution }}</a><span>›</span>
  <a href="{{ $groupUrl }}">{{ $group }}</a><span>›</span>
  <span>{{ $poz->poz_no }}</span>
@endsection

@if($poz->meta_keywords)
@section('keywords', $poz->meta_keywords)
@endif

@push('head')
<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@if($hasFaq)
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endif
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')

<section class="poz-detail-hero">
  <div class="poz-kicker">Resmî poz · birim fiyat referansı</div>

  <h1 class="poz-title">
    <span class="poz-title-code">{{ $poz->poz_no }}</span>
    — {{ $poz->description }}
  </h1>

  <p class="poz-lead">
    {{ $poz->institution }} kaynağındaki bu pozun tanımını, birimini, güncel kayıtlı birim fiyatını ve WORKSİTEM üzerindeki ilgili pozları tek ekranda inceleyin.
  </p>

  <div class="poz-badges" aria-label="Poz özet bilgileri">
    <span class="poz-badge">Poz <strong>{{ $poz->poz_no }}</strong></span>
    <span class="poz-badge">Birim <strong>{{ $poz->unit }}</strong></span>
    <span class="poz-badge">Kurum <strong>{{ $poz->institution }}</strong></span>
    @if($poz->year)
      <span class="poz-badge">Yıl <strong>{{ $poz->year }}</strong></span>
    @endif
  </div>

  @if($otherInstitutions->isNotEmpty())
  <p class="poz-variants">
    Bu poz numarası başka kurumların listesinde de var:
    @foreach($otherInstitutions as $v)
      <a href="{{ \App\Support\PozUrl::path($v->poz_no) }}?kurum={{ \App\Support\PozCatalog::institutionSlug($v->institution) }}" rel="nofollow">{{ $v->institution }} ({{ number_format((float) $v->unit_price, 2, ',', '.') }} TL/{{ $v->unit }})</a>@if(! $loop->last), @endif
    @endforeach
  </p>
  @endif

  <div class="poz-actions">
    <a class="poz-btn poz-btn-primary" href="/maliyet-hesapla?poz={{ urlencode($poz->poz_no) }}" data-poz-cta="hesapla">Bu poz ile maliyet hesapla →</a>
    <a class="poz-btn" href="{{ $groupUrl }}">{{ $group }} grubundaki pozlar</a>
  </div>
</section>

<div class="poz-grid">
  <div>
    <section class="poz-card" aria-labelledby="pozDetailsTitle">
      <div class="poz-card-head">
        <div>
          <div class="poz-card-kicker">Poz bilgileri</div>
          <h2 id="pozDetailsTitle">Teknik özet</h2>
        </div>
      </div>

      <div class="poz-details">
        <div class="poz-details-row">
          <div class="poz-details-label">Poz numarası</div>
          <div class="poz-details-value"><code>{{ $poz->poz_no }}</code></div>
        </div>

        <div class="poz-details-row">
          <div class="poz-details-label">Tanım</div>
          <div class="poz-details-value">{{ $poz->description }}</div>
        </div>

        <div class="poz-details-row">
          <div class="poz-details-label">Birim</div>
          <div class="poz-details-value">{{ $poz->unit }}</div>
        </div>

        <div class="poz-details-row">
          <div class="poz-details-label">Kaynak kurum</div>
          <div class="poz-details-value">{{ $poz->institution }}</div>
        </div>

        @if($poz->year)
        <div class="poz-details-row">
          <div class="poz-details-label">Kaynak yılı</div>
          <div class="poz-details-value">{{ $poz->year }}</div>
        </div>
        @endif

        <div class="poz-details-row">
          <div class="poz-details-label">Yüklenici kârı</div>
          <div class="poz-details-value">{{ $profitText }}</div>
        </div>
      </div>
    </section>

    @if($poz->long_description)
    <section class="poz-card" aria-labelledby="pozLongDescriptionTitle">
      <div class="poz-card-head">
        <div>
          <div class="poz-card-kicker">Tarif / yapım şartları</div>
          <h2 id="pozLongDescriptionTitle">{{ $poz->poz_no }} detaylı açıklaması</h2>
        </div>
      </div>

      <div class="poz-prose">
        {!! nl2br(e($poz->long_description)) !!}
      </div>
    </section>
    @endif

    @if($related->isNotEmpty())
    <section class="poz-card" aria-labelledby="pozRelatedTitle">
      <div class="poz-card-head">
        <div>
          <div class="poz-card-kicker">{{ $poz->institution }} · {{ $group }} grubu</div>
          <h2 id="pozRelatedTitle">İlgili pozlar</h2>
        </div>
      </div>
      <ul class="poz-related">
        @foreach($related as $r)
        <li>
          <a href="{{ \App\Support\PozUrl::path($r->poz_no) }}"><code>{{ $r->poz_no }}</code> {{ \Illuminate\Support\Str::limit($r->description, 95) }}</a>
          <span>{{ number_format((float) $r->unit_price, 2, ',', '.') }} TL/{{ $r->unit }}</span>
        </li>
        @endforeach
      </ul>
      <a class="poz-more" href="{{ $groupUrl }}">{{ $group }} grubundaki tüm pozlar →</a>
    </section>
    @endif
  </div>

  <aside class="poz-summary" aria-label="Poz fiyat özeti">
    <section class="poz-card">
      <div class="poz-price-label">Kayıtlı birim fiyat</div>
      <div class="poz-price-value">{{ $priceFormatted }} TL</div>
      <div class="poz-price-unit">1 {{ $poz->unit }} için kayıtlı referans değer</div>

      <form class="poz-calc" onsubmit="return false" aria-label="Tutar hesapla" data-price="{{ (float) $poz->unit_price }}">
        <label for="pozQty">Miktar ({{ $poz->unit }})</label>
        <input id="pozQty" type="number" inputmode="decimal" min="0" step="any" placeholder="Örn. 120">
        <div class="poz-calc-out" aria-live="polite">
          <div><span>Tutar (KDV hariç)</span><strong data-calc="net">—</strong></div>
          <div><span>KDV %20 dahil</span><strong data-calc="gross">—</strong></div>
        </div>
      </form>

      <div class="poz-summary-list">
        <div class="poz-summary-row">
          <span>Poz no</span>
          <strong>{{ $poz->poz_no }}</strong>
        </div>

        <div class="poz-summary-row">
          <span>Kurum</span>
          <strong>{{ $poz->institution }}</strong>
        </div>

        @if($poz->year)
        <div class="poz-summary-row">
          <span>Yıl</span>
          <strong>{{ $poz->year }}</strong>
        </div>
        @endif

        <div class="poz-summary-row">
          <span>Birim</span>
          <strong>{{ $poz->unit }}</strong>
        </div>
      </div>

      <div class="poz-note">
        Bu değer WORKSİTEM veritabanındaki poz kaydının referans birim fiyatıdır. Projeye özel piyasa teklifi değildir; miktar, malzeme, saha ve uygulama koşulları ayrıca değerlendirilmelidir.
      </div>

      <a class="poz-btn poz-btn-primary" style="width:100%;margin-top:16px" href="/maliyet-hesapla?poz={{ urlencode($poz->poz_no) }}" data-poz-cta="hesapla">
        Maliyet hesabına geç →
      </a>
    </section>
  </aside>
</div>

@php
  $toolStatusConfig = config('worksitem_tools.tool_status', []);
  $toolStatusDefault = $toolStatusConfig['default'] ?? 'review';
  $toolStatusOverrides = $toolStatusConfig['overrides'] ?? [];

  $autoBoqStatus =
      $toolStatusOverrides['otomatik-kesif-fiyatlandirma']
      ?? $toolStatusDefault;

  /*
   * Staging'de CTA QA için görünür.
   * Production'da yalnız araç active ise kullanıcıya gösterilir.
   */
  $showAutoBoqCta =
      request()->getHost() === 'app.worksitem.com'
      || ($autoBoqStatus === 'active' && \App\Support\Tools::aiFileOn());
@endphp

<section class="poz-cta">
  <div>
    <h2>Bu pozu proje bütçenizde değerlendirin.</h2>

    @if($showAutoBoqCta)
      <p>Tek bir poz için maliyet hesabına geçin. Birden fazla iş kaleminiz veya keşif listeniz varsa dosyanızı AI ön keşif aracında analiz edin.</p>
    @else
      <p>Poz referansını kullanarak yaklaşık proje maliyeti hesabına geçin.</p>
    @endif
  </div>

  <div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-end">
    <a
      class="poz-btn poz-btn-primary"
      href="/maliyet-hesapla?poz={{ urlencode($poz->poz_no) }}" data-poz-cta="hesapla"
    >
      Maliyet hesapla →
    </a>

    @if($showAutoBoqCta)
      <a
        class="poz-btn"
        href="/araclar/otomatik-kesif-fiyatlandirma"
      >
        Keşif dosyanızı yükleyin →
      </a>
    @endif
  </div>
</section>



@if(! empty($sponsorCategories))
  <x-sponsor.slot tool="poz-sayfalari" :categories="$sponsorCategories" title="Bu imalat için ürünler" />
@endif

<section class="poz-card" style="margin-top:18px" aria-labelledby="pozFaqTitle">
  <div class="poz-card-head">
    <div>
      <div class="poz-card-kicker">Sık sorulan sorular</div>
      <h2 id="pozFaqTitle">{{ $poz->poz_no }} hakkında</h2>
    </div>
  </div>

  @if($hasFaq)
    @foreach($faqItems as $faq)
    <details class="faq-item">
      <summary>{{ $faq['q'] ?? '' }}</summary>
      <p>{{ $faq['a'] ?? '' }}</p>
    </details>
    @endforeach
  @else
    <details class="faq-item">
      <summary>{{ $poz->poz_no }} pozu nedir?</summary>
      <p>{{ $poz->description }}</p>
    </details>

    <details class="faq-item">
      <summary>{{ $poz->poz_no }} birim fiyatı ne kadar?</summary>
      <p>Kayıtlı birim fiyat <strong>{{ $priceFormatted }} TL/{{ $poz->unit }}</strong>. Kaynak: {{ $poz->institution }}{{ $yearText }}.</p>
    </details>

    <details class="faq-item">
      <summary>{{ $poz->poz_no }} hangi kurumun kaydında?</summary>
      <p>{{ $poz->institution }}{{ $yearText }}.</p>
    </details>

    <details class="faq-item">
      <summary>Bu poz için yüklenici kârı dahil mi?</summary>
      <p>{{ $profitText }}</p>
    </details>
  @endif
</section>

@include('partials.ws-track')
<script>
(function () {
  var f = document.querySelector('.poz-calc'); if (!f) return;
  var price = parseFloat(f.getAttribute('data-price')) || 0, q = f.querySelector('#pozQty'), sent = false;
  var fmt = function (v) { return v.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' TL'; };
  q.addEventListener('input', function () {
    var n = parseFloat(String(q.value).replace(',', '.'));
    var ok = isFinite(n) && n > 0;
    f.querySelector('[data-calc=net]').textContent = ok ? fmt(n * price) : '—';
    f.querySelector('[data-calc=gross]').textContent = ok ? fmt(n * price * 1.2) : '—';
    if (ok && !sent && window.wsTrack) { sent = true; wsTrack('poz_calc', { poz: @json($poz->poz_no) }); }
  });
})();
document.addEventListener('click', function (e) {
  var a = e.target.closest('[data-poz-cta]'); if (!a || !window.wsTrack) return;
  wsTrack('poz_cta_click', { poz: @json($poz->poz_no), cta: a.getAttribute('data-poz-cta') });
});
</script>

@endsection
