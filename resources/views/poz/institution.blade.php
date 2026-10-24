@extends('poz._layout')

@section('title', $inst.' Poz Listesi ve Birim Fiyatları '.date('Y').' | WORKSİTEM')
@section('description', $inst.' kaynaklı '.number_format($total, 0, ',', '.').' resmî inşaat pozu, '.count($groups).' poz grubunda. Poz numarası, tanım, birim ve kayıtlı birim fiyatlarını gruplar halinde inceleyin.')
@section('canonical', url('/pozlar/'.$slug))

@section('breadcrumb')
  <a href="/pozlar">Pozlar</a><span>›</span>
  <span>{{ $inst }}</span>
@endsection

@push('head')
<script type="application/ld+json">{!! json_encode([
  '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Pozlar', 'item' => url('/pozlar')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => $inst, 'item' => url('/pozlar/'.$slug)],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')
<section class="poz-detail-hero">
  <div class="poz-kicker">Resmî poz listesi</div>
  <h1 class="poz-title">{{ $inst }} pozları</h1>
  <p class="poz-lead">{{ number_format($total, 0, ',', '.') }} poz, {{ count($groups) }} grup. Bir grubu açın; her pozun tanımı, birimi ve kayıtlı birim fiyatı listelenir.</p>
</section>

<div class="poz-hub-grid">
  @foreach($groups as $g)
    <a href="/pozlar/{{ $slug }}/{{ $g['slug'] }}">
      <strong>{{ $g['group'] }}</strong>
      {{ $g['sample'] }}
      <em>{{ number_format($g['count'], 0, ',', '.') }} poz</em>
    </a>
  @endforeach
</div>

<section class="poz-card" style="margin-top:24px">
  <div class="poz-card-kicker">Diğer kurumlar</div>
  <div class="poz-hub-grid">
    @foreach($institutions as $s => $name)
      @if($s !== $slug)<a href="/pozlar/{{ $s }}"><strong>{{ $name }}</strong></a>@endif
    @endforeach
  </div>
</section>
@endsection
