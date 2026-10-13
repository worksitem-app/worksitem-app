<x-filament-widgets::widget>
    <x-filament::section heading="Araç performansı (30 gün)" description="Görüntüleme → hesaplama → teklif tıklaması">
        @if ($rows->isEmpty())
            <p class="text-sm text-gray-500">Henüz araç verisi yok.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2 pr-3 font-medium">Araç</th>
                            <th class="py-2 px-2 font-medium text-right">Görüntüleme</th>
                            <th class="py-2 px-2 font-medium text-right">Hesaplama</th>
                            <th class="py-2 pl-2 font-medium text-right">Teklif tık.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $r)
                            @php $rate = $r->views > 0 ? min(100, round($r->calcs / $r->views * 100)) : 0; @endphp
                            <tr class="border-t border-gray-100 dark:border-white/5">
                                <td class="py-2 pr-3">
                                    <div class="font-medium">{{ \Illuminate\Support\Str::of($r->tool_slug)->replace(['-calculator', '-hesaplayici', '-'], ['', '', ' '])->title() }}</div>
                                    <div class="mt-1 h-1.5 rounded-full bg-gray-100 dark:bg-white/10" style="width:100%">
                                        <div class="h-1.5 rounded-full" style="width:{{ min(100, $rate) }}%;background:#FFB800"></div>
                                    </div>
                                </td>
                                <td class="py-2 px-2 text-right tabular-nums">{{ number_format($r->views, 0, ',', '.') }}</td>
                                <td class="py-2 px-2 text-right tabular-nums">{{ number_format($r->calcs, 0, ',', '.') }} <span class="text-xs text-gray-400">%{{ $rate }}</span></td>
                                <td class="py-2 pl-2 text-right tabular-nums">{{ number_format($r->ctas, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
