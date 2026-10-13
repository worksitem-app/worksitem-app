@php
    $hour = (int) now('Europe/Istanbul')->format('H');
    $greet = $hour < 12 ? 'Günaydın' : ($hour < 18 ? 'İyi günler' : 'İyi akşamlar');
    $envLabel = ['production' => 'Canlı', 'staging' => 'Staging', 'local' => 'Yerel'][$env] ?? $env;
@endphp
<x-filament-widgets::widget>
    <div class="ws-cc">
        <div class="ws-cc-main">
            <div class="ws-cc-meta">
                <span class="ws-cc-env ws-cc-env--{{ $env }}">● {{ $envLabel }}</span>
                <span>{{ $host }}</span>
                <span>{{ now('Europe/Istanbul')->locale('tr')->translatedFormat('j F Y, l') }}</span>
            </div>
            <h2 class="ws-cc-title">{{ $greet }}, {{ $name }}.</h2>
            <p class="ws-cc-sub">
                {{ $live }} / {{ $tools }} araç yayında ·
                sistem: <b class="ok">{{ $health['ok'] }} tamam</b>
                @if ($health['warn']) · <b class="warn">{{ $health['warn'] }} uyarı</b>@endif
                @if ($health['fail']) · <b class="fail">{{ $health['fail'] }} hata</b>@endif
            </p>
            <div class="ws-cc-actions">
                <a href="{{ $leadsUrl }}" class="ws-cc-btn ws-cc-btn--y">Teklif talepleri</a>
                <a href="{{ $toolsUrl }}" class="ws-cc-btn">Araç yayın durumu</a>
                <a href="{{ $sponsorsUrl }}" class="ws-cc-btn">Sponsorlar</a>
                <a href="{{ $walletsUrl }}" class="ws-cc-btn">AI kredileri</a>
                <a href="{{ $pricesUrl }}" class="ws-cc-btn">Fiyat Merkezi</a>
                <a href="{{ url('/') }}" target="_blank" rel="noopener" class="ws-cc-btn">Siteyi aç ↗</a>
            </div>
        </div>
        <div class="ws-cc-alerts">
            <div class="ws-cc-alerts-h">Dikkat gerektirenler</div>
            @forelse ($alerts as $a)
                <a href="{{ $a['url'] }}" class="ws-cc-alert ws-cc-alert--{{ $a['tone'] }}"><i></i><span>{{ $a['text'] }}</span><em>→</em></a>
            @empty
                <div class="ws-cc-empty">Bekleyen iş yok. 👌</div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
