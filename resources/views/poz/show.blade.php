@extends('poz._layout')

@section('title', $poz->poz_no . ' — ' . \Illuminate\Support\Str::limit($poz->description, 55) . ' | WORKSİTEM')

@section('description', $poz->meta_description ?: ($poz->poz_no . ' pozunun 2026 birim fiyatı ' . $priceFormatted . ' TL/' . $poz->unit . '. Kaynak: ' . $poz->institution . '.'))

@section('canonical', url('/poz/' . $poz->poz_no))

@section('breadcrumb')
  <a href="/pozlar">Pozlar</a><span>›</span>
  <span>{{ $poz->poz_no }}</span>
@endsection

@if($poz->meta_keywords)
<meta name="keywords" content="{{ $poz->meta_keywords }}">
@endif

<script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

@if($hasFaq)
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif

<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

<div class="hero">
  <h1>{{ $poz->poz_no }} <span>{{ $poz->description }}</span></h1>
</div>

<div class="direct-answer">
  <p><strong>{{ $poz->poz_no }} pozunun 2026 birim fiyatı: <span class="price-badge">{{ $priceFormatted }} TL/{{ $poz->unit }}</span></strong></p>
  <p style="margin-top:8px;color:#78350f;font-size:14px">Kaynak: {{ $poz->institution }}{{ $yearText }}</p>
</div>

<div class="card">
  <h2>Poz Detayları</h2>
  <table class="data-table">
    <tr><th>Poz No</th><td><code>{{ $poz->poz_no }}</code></td></tr>
    <tr><th>Açıklama</th><td>{{ $poz->description }}</td></tr>
    <tr><th>Birim</th><td>{{ $poz->unit }}</td></tr>
    <tr><th>Birim Fiyat</th><td><span class="price-badge">{{ $priceFormatted }} TL</span></td></tr>
    <tr><th>Kurum</th><td>{{ $poz->institution }}</td></tr>
  </table>
</div>

@if($poz->long_description)
<div class="card">
  <h2>Detaylı Açıklama</h2>
  <div class="long-description" style="line-height:1.8;color:var(--text)">
    {!! nl2br(e($poz->long_description)) !!}
  </div>
</div>
@endif

<div class="cta-box">
  <h2>Bu Poz ile Maliyet Hesapla</h2>
  <p>{{ $poz->description }} işini projenize ekleyerek toplam maliyeti öğrenin.</p>
  <a href="/maliyet-hesapla?poz={{ urlencode($poz->poz_no) }}" class="btn btn-primary">Ücretsiz Hesapla →</a>
</div>

<div class="trust-bar">
  <div class="trust-item"><span class="trust-icon">✓</span><span>Kainta İnşaat saha deneyimi</span></div>
  <div class="trust-item"><span class="trust-icon">✓</span><span>2026 resmî rayiç bedeli</span></div>
  <div class="trust-item"><span class="trust-icon">✓</span><span>Ücretsiz keşif imkânı</span></div>
</div>


@if(!empty($popular) && $popular->isNotEmpty())
<section style="margin-top:32px">
  <h2>Popüler Pozlar</h2>
  <ul class="related-list">
    @foreach($popular as $p)
    <li>
      <a href="{{ route('poz.show', $p->poz_no) }}">
        <span class="poz-code">{{ $p->poz_no }}</span>
        {{ \Illuminate\Support\Str::limit($p->description, 60) }}
        <strong style="color:var(--green);margin-left:auto;">
          {{ number_format((float)$p->unit_price, 2, ',', '.') }} TL/{{ $p->unit }}
        </strong>
      </a>
    </li>
    @endforeach
  </ul>
</section>
@endif
<section style="margin-top:48px">
  <h2>Sıkça Sorulan Sorular</h2>

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
      <p>2026 rayicinde <strong>{{ $priceFormatted }} TL/{{ $poz->unit }}</strong> olarak belirlenmiştir. Kaynak: {{ $poz->institution }}.</p>
    </details>

    <details class="faq-item">
      <summary>{{ $poz->poz_no }} hangi kurumun rayicinde?</summary>
      <p>{{ $poz->institution }}{{ $yearText }}.</p>
    </details>

    <details class="faq-item">
      <summary>Bu poz için kâr dahil mi?</summary>
      <p>{{ $profitText }}</p>
    </details>
  @endif
</section>
