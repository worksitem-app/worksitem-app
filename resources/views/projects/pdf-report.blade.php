<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kainta İnşaat AI Proje Raporu</title>
    <style>
        @page { margin: 40px 40px; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .header { border-bottom: 2px solid #ffb700; padding-bottom: 10px; margin-bottom: 20px; }
        .text-right { text-align: right; }
        .title { font-size: 24px; font-weight: bold; color: #0f172a; margin: 0; letter-spacing: 1px; }
        .subtitle { font-size: 10px; color: #64748b; }
        .badge { background-color: #ffb700; color: #000; padding: 4px 8px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .summary { background-color: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-radius: 5px; margin-bottom: 20px; }
        .cost { font-size: 26px; font-weight: bold; color: #d97706; margin-top: 5px; display: inline-block; }
        .section-title { font-size: 15px; font-weight: bold; color: #0f172a; margin-top: 25px; margin-bottom: 10px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.data-table th { background-color: #0f172a; color: #fff; text-align: left; padding: 10px; font-size: 10px; text-transform: uppercase; }
        table.data-table td { border-bottom: 1px solid #e2e8f0; padding: 10px; font-size: 11px; }
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; height: 30px; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .highlight { color: #d97706; font-weight: bold; }
    </style>
</head>
<body>

    <div class="footer">
        Yasal Uyarı: Bu tahmin Worksitem Yapay Zeka (AI) motoru tarafından oluşturulmuştur. Gerçek maliyetler saha koşullarına bağlı olarak ±%15 oranında değişiklik gösterebilir. Resmi bir bağlayıcılığı yoktur. | © {{ date('Y') }} Kainta İnşaat
    </div>

    <table width="100%" class="header">
        <tr>
            <td width="60%">
                <h1 class="title">PROJE MALİYET RAPORU</h1>
                <span class="badge">KAİNTA AI x WORKSİTEM</span>
            </td>
            <td width="40%" class="text-right">
                @php
                    // Logoyu public dizininden güvenle çekmek (DomPDF absolute path sever)
                    $logoPath = file_exists(public_path('brand/worksitem-logo.png')) ? public_path('brand/worksitem-logo.png') : (file_exists(public_path('brand/kainta-logo.jpg')) ? public_path('brand/kainta-logo.jpg') : null);
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" style="max-height: 45px;">
                @else
                    <h2 style="color: #0f172a; margin:0;">KAİNTA İNŞAAT</h2>
                @endif
            </td>
        </tr>
    </table>

    <div class="summary">
        <table width="100%">
            <tr>
                <td width="50%">
                    <strong>Proje Adı:</strong> {{ $project->name ?? 'Özel Proje' }}<br>
                    <strong>Tarih:</strong> {{ date('d.m.Y H:i') }}<br>
                    <strong>Proje Tipi:</strong> {{ $project->project_type_id == 2 ? 'Tadilat / Renovasyon' : ($project->type->name ?? 'Yeni İnşaat') }}<br>
                    <strong>Brüt Alan:</strong> {{ $constructionArea ?? 0 }} m²
                </td>
                <td width="50%" class="text-right">
                    <span class="subtitle">GENEL TOPLAM TAHMİNİ MALİYET</span><br>
                    <span class="cost">{{ number_format($estimatedCost ?? 0, 0, ',', '.') }} TL</span><br>
                    <span class="subtitle" style="color: #0f172a; font-weight: bold;">Birim Maliyet: {{ number_format($unitPrice ?? 0, 0, ',', '.') }} TL/m²</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">MALİYET DAĞILIMI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Maliyet Kalemi</th>
                <th style="text-align: center;">Oran</th>
                <th style="text-align: right;">Tahmini Tutar</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($details) && is_array($details) && count($details) > 0)
                @foreach($details as $name => $data)
                <tr>
                    <td>{{ $name }}</td>
                    <td style="text-align: center;">%{{ $data['percent'] }}</td>
                    <td style="text-align: right;" class="highlight">{{ number_format($data['cost'], 0, ',', '.') }} TL</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="3">Maliyet kırılımı bulunamadı.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">MALZEME VE İŞÇİLİK ÖZETİ</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kalem</th>
                <th>Miktar</th>
                <th>Birim Fiyat</th>
                <th style="text-align: right;">Toplam Tutar</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($materials) && is_array($materials) && count($materials) > 0)
                @foreach($materials as $mat)
                <tr>
                    <td>{{ $mat['name'] }}</td>
                    <td>{{ $mat['qty'] }}</td>
                    <td>{{ $mat['price'] }}</td>
                    <td style="text-align: right;" class="highlight">{{ $mat['total'] }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="4">Metraj verisi bulunamadı.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">YAPAY ZEKA ANALİZİ NOTLARI</div>
    <ul style="color: #475569; padding-left: 20px; line-height: 1.6;">
        @if(isset($aiNotes) && is_array($aiNotes) && count($aiNotes) > 0)
            @foreach($aiNotes as $note)
                <li style="margin-bottom: 8px;">{{ $note }}</li>
            @endforeach
        @else
            <li>Proje değerlendirmesi yapılmıştır. Kesin maliyetler için uzman keşfi gerekmektedir.</li>
        @endif
    </ul>

</body>
</html>
