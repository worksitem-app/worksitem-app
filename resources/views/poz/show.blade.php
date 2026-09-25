@extends('poz._layout')

@section('title', $poz->poz_no . ' — ' . \Illuminate\Support\Str::limit($poz->description, 55) . ' | WORKSİTEM')
@section('description', $poz->poz_no . ' pozunun 2026 birim fiyatı ' . $priceFormatted . ' TL/' . $poz->unit . '. Kaynak: ' . $poz->institution . '.')
@section('canonical', url('/poz/' . $poz->poz_no))

@section('breadcrumb')
  <a href="/pozlar">Pozlar</a><span>›</span>
  <span>{{ $poz->poz_no }}</span>
@endsection

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

<section style="margin-top:48px">
  <h2>Sıkça Sorulan Sorular</h2>

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
</section>
