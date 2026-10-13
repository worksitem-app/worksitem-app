{{-- Hesaplayıcı tasarım kiti: </body> öncesine. Mobil sabit çubuk + TCMB döviz satırı --}}
@props(['dock' => true])
@php
    $wcConfig = [
        'fx' => app(\App\Services\FxRates::class)->current(),
        'symbols' => config('worksitem_bathroom.fx.currencies', ['USD' => '$', 'EUR' => '€', 'GBP' => '£']),
        'legal' => config('worksitem_legal.result_short'),
    ];
@endphp
@if ($dock)
<div class="wc-dock is-hidden" id="wcDock" aria-hidden="true">
    <div><small>Tahmini sonuç</small><strong id="wcDockText"></strong></div>
    <button type="button" id="wcDockBtn" tabindex="-1">Sonucu gör</button>
</div>
@endif
<script type="application/json" id="wc-config">{!! json_encode($wcConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
<script src="{{ \App\Support\Asset::url('js/ws-calc.js') }}" defer></script>
