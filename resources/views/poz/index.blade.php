@extends('poz._layout')

@section('title', 'İnşaat Pozları — 43.290+ Resmî Rayiç ve Birim Fiyat | WORKSİTEM')
@section('description', 'ÇŞB, DSİ, Karayolları ve 12 kurumdan toplam 43.290+ inşaat pozu. 2026 güncel birim fiyatları, detaylı açıklamalar, SSS.')
@section('canonical', url('/pozlar'))
@section('keywords', 'inşaat pozları, rayiç bedeli, birim fiyat, ÇŞB poz, YFK poz, 2026 inşaat maliyeti')

@section('breadcrumb')
  <span>Pozlar</span>
@endsection

<div class="hero">
  <h1>İnşaat Pozları <span>43.290+</span></h1>
  <p style="font-size:18px;color:var(--muted);max-width:700px">
    ÇŞB, DSİ, Karayolları ve diğer kurumların 2026 güncel rayiçleri ve birim fiyatları
  </p>
</div>

<div class="stats">
  <div class="stat">
    <div class="stat-value">43.290</div>
    <div class="stat-label">Toplam Poz</div>
  </div>
  <div class="stat">
    <div class="stat-value">12</div>
    <div class="stat-label">Kurum</div>
  </div>
  <div class="stat">
    <div class="stat-value">2026</div>
    <div class="stat-label">Güncel Yıl</div>
  </div>
</div>

<form class="search-box" method="GET">
  <input type="text" name="q" placeholder="Poz no veya açıklama ara..." value="{{ $search }}">
  <select name="kurum">
    <option value="all">Tüm Kurumlar</option>
    @foreach($institutions as $inst)
    <option value="{{ $inst }}" {{ $institution === $inst ? 'selected' : '' }}>{{ $inst }}</option>
    @endforeach
  </select>
  <button type="submit">Ara</button>
</form>

<div class="poz-list">
  @forelse($pozs as $poz)
  <div class="poz-item">
    <a href="{{ route('poz.show', $poz->poz_no) }}">
      <span class="poz-code">{{ $poz->poz_no }}</span>
      <span>{{ \Illuminate\Support\Str::limit($poz->description, 80) }}</span>
    </a>
    <span class="poz-price">{{ number_format((float)$poz->unit_price, 2, ',', '.') }} TL/{{ $poz->unit }}</span>
  </div>
  @empty
  <div style="padding:40px;text-align:center;color:var(--muted)">
    Sonuç bulunamadı.
  </div>
  @endforelse
</div>

<div style="margin-top:32px">
  {{ $pozs->links() }}

@if(!empty($popular) && $popular->isNotEmpty())
<section style="margin-top:48px">
  <h2>Popüler Pozlar</h2>
  <div class="poz-list">
    @foreach($popular as $p)
    <div class="poz-item">
      <a href="{{ route('poz.show', $p->poz_no) }}">
        <span class="poz-code">{{ $p->poz_no }}</span>
        <span>{{ \Illuminate\Support\Str::limit($p->description, 80) }}</span>
      </a>
      <span class="poz-price">
        {{ number_format((float)$p->unit_price, 2, ',', '.') }} TL/{{ $p->unit }}
      </span>
    </div>
    @endforeach
  </div>
</section>
@endif
</div>
