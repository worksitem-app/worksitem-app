<x-filament-panels::page>
    <p style="font-size:14px;color:#6b7280;margin-bottom:12px">Her ziyaretçi bir sürümü görür ve çerezle aynı sürümde kalır. Tıklama oranı = tıklama ÷ görüntülenme. Her sürüm en az 100 görüntülenmeye ulaşınca fark %95 güvenle test edilir. Kazananı sabitlemek için <code>config/worksitem_ab.php</code> dosyasında testin <code>winner</code> değerini yazın (ör. 'b'); bunu bir sonraki kurulum dosyasında birlikte yaparız.</p>
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:14px">
        <thead><tr style="text-align:left;border-bottom:1px solid #e5e7eb">
            <th style="padding:8px">Test</th><th style="padding:8px">Sürüm</th><th style="padding:8px">Metin</th><th style="padding:8px">Görüntülenme</th><th style="padding:8px">Tıklama</th><th style="padding:8px">Tıklama oranı</th><th style="padding:8px">Dönüşüm</th><th style="padding:8px">Sonuç</th>
        </tr></thead>
        <tbody>
        @foreach ($rows as $r)
            <tr style="border-bottom:1px solid #f3f4f6">
                <td style="padding:8px">{{ $r['label'] }}</td>
                <td style="padding:8px;font-weight:600">{{ strtoupper($r['variant']) }}@if ($r['winner']) ✓ @endif</td>
                <td style="padding:8px">{{ $r['text'] }}</td>
                <td style="padding:8px">{{ number_format($r['views'], 0, ',', '.') }}</td>
                <td style="padding:8px">{{ number_format($r['clicks'], 0, ',', '.') }}</td>
                <td style="padding:8px;font-weight:600">{{ $r['views'] ? number_format($r['ctr'] * 100, 1, ',', '.').'%' : '—' }}</td>
                <td style="padding:8px">{{ number_format($r['conversions'], 0, ',', '.') }}</td>
                <td style="padding:8px;color:#6b7280">{{ $r['note'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</x-filament-panels::page>
