<x-filament-panels::page>
    <p style="font-size:14px;color:#6b7280;margin-bottom:12px">Firmaların kayıt sonrası "hizmet bilgileri" adımında yazdığı fiyatlar (KDV dahil, beyan). Ortancadan 2 kattan fazla sapan cevaplar 3 ve üzeri cevap olduğunda listelenir: firmayı arayıp kontrol edin. Hesaplayıcı fiyatlarıyla karşılaştırmak ve fiyat robotunu desteklemek için kullanılır; müşterilere gösterilmez.</p>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:14px">
        <thead><tr style="text-align:left;border-bottom:1px solid #e5e7eb">
            <th style="padding:8px">Soru</th><th style="padding:8px">İş kolu</th><th style="padding:8px">Cevap</th><th style="padding:8px">Ortanca</th><th style="padding:8px">En düşük – en yüksek</th><th style="padding:8px">Kontrol et</th>
        </tr></thead>
        <tbody>
        @foreach ($rows as $r)
            <tr style="border-bottom:1px solid #f3f4f6;vertical-align:top">
                <td style="padding:8px">{{ $r['q'] }}<br><small style="color:#6b7280">{{ $r['unit'] }}</small></td>
                <td style="padding:8px">{{ config("worksitem_trades.{$r['trade']}.label", $r['trade']) }}</td>
                <td style="padding:8px">{{ $r['n'] }}</td>
                <td style="padding:8px;font-weight:600">{{ $r['median'] ? number_format($r['median'], 0, ',', '.') : '—' }}</td>
                <td style="padding:8px">{{ $r['n'] ? number_format($r['min'], 0, ',', '.').' – '.number_format($r['max'], 0, ',', '.') : '—' }}</td>
                <td style="padding:8px;color:#b45309">{{ implode(' · ', $r['outliers']) ?: '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</x-filament-panels::page>
