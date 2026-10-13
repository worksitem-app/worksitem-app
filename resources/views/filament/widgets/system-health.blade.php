<x-filament-widgets::widget id="ws-health">
    <x-filament::section heading="Sistem sağlığı" description="Entegrasyonlar ve veri tazeliği · {{ $sum['ok'] }} tamam, {{ $sum['warn'] }} uyarı, {{ $sum['fail'] }} hata">
        <div class="ws-health">
            @foreach ($groups as $group => $rows)
                <div class="ws-health-g">{{ $group }}</div>
                @foreach ($rows as $r)
                    <div class="ws-health-row">
                        <span class="ws-dot ws-dot--{{ $r['state'] }}" title="{{ ['ok' => 'Tamam', 'warn' => 'Uyarı', 'fail' => 'Hata', 'off' => 'Kapalı'][$r['state']] }}"></span>
                        <div><b>{{ $r['label'] }}</b><small>{{ $r['detail'] }}</small></div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
