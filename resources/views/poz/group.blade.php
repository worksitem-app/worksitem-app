@extends('poz._layout')

@php
  $gTitle = $g['group'].' Pozları '.($year ?: date('Y')).' – '.$inst.' Birim Fiyat Listesi';
@endphp
@section('title', $gTitle.($page > 1 ? ' (Sayfa '.$page.')' : '').' | WORKSİTEM')
@section('description', $inst.' '.$g['group'].' grubundaki '.number_format($count, 0, ',', '.').' pozun tanımı, birimi ve '.($year ?: date('Y')).' kayıtlı birim fiyatı. Örnek: '.\Illuminate\Support\Str::limit($g['sample'], 70))
@section('canonical', url('/pozlar/'.$kurum.'/'.$g['slug']).($page > 1 ? '?sayfa='.$page : ''))

@section('breadcrumb')
  <a href="/pozlar">Pozlar</a><span>›</span>
  <a href="/pozlar/{{ $kurum }}">{{ $inst }}</a><span>›</span>
  <span>{{ $g['group'] }}</span>
@endsection

@push('head')
<script type="application/ld+json">{!! json_encode([
  '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Pozlar', 'item' => url('/pozlar')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => $inst, 'item' => url('/pozlar/'.$kurum)],
    ['@type' => 'ListItem', 'position' => 3, 'name' => $g['group'].' pozları', 'item' => url('/pozlar/'.$kurum.'/'.$g['slug'])],
  ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush

@section('content')
<section class="poz-detail-hero">
  <div class="poz-kicker">{{ $inst }} · resmî poz grubu</div>
  <h1 class="poz-title"><span class="poz-title-code">{{ $g['group'] }}</span> pozları ve birim fiyatları</h1>
  <p class="poz-lead">{{ number_format($count, 0, ',', '.') }} poz{{ $year ? ', '.$year.' kayıtlı fiyatlarıyla' : '' }}. Bir pozu açarak miktar girip toplam tutarı hesaplayabilirsiniz.</p>
</section>

<section class="poz-card">
  <ul class="poz-related">
    @foreach($pozs as $r)
    <li>
      <a href="{{ \App\Support\PozUrl::path($r->poz_no) }}"><code>{{ $r->poz_no }}</code> {{ \Illuminate\Support\Str::limit($r->description, 120) }}</a>
      <span>{{ number_format((float) $r->unit_price, 2, ',', '.') }} TL/{{ $r->unit }}</span>
    </li>
    @endforeach
  </ul>

  @if($pages > 1)
  <nav class="poz-pagination" aria-label="Grup sayfaları" style="margin-top:14px;display:flex;gap:10px;align-items:center">
    @if($page > 1)<a class="poz-btn" href="/pozlar/{{ $kurum }}/{{ $g['slug'] }}{{ $page > 2 ? '?sayfa='.($page - 1) : '' }}" rel="prev">← Önceki</a>@endif
    <span>Sayfa {{ $page }} / {{ $pages }}</span>
    @if($page < $pages)<a class="poz-btn" href="/pozlar/{{ $kurum }}/{{ $g['slug'] }}?sayfa={{ $page + 1 }}" rel="next">Sonraki →</a>@endif
  </nav>
  @endif
</section>

<p style="margin-top:18px"><a class="poz-btn" href="/pozlar/{{ $kurum }}">← {{ $inst }} poz grupları</a></p>
@endsection
