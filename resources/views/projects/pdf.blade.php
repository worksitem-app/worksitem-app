<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>Resmi Keşif Raporu | Kainta İnşaat x Worksitem</title>
    <style>
        @page { margin: 30px 40px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            color: #1e293b; 
            background: #ffffff; 
            font-size: 11px; 
            line-height: 1.5; 
        }
        
        /* ANTETLİ KAĞIT HEADER (ÜST BİLGİ) */
        .pdf-header { 
            width: 100%; 
            border-bottom: 3px solid #0f172a; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
            position: relative;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo-kainta { height: 45px; max-width: 180px; object-fit: contain; }
        .logo-worksitem { height: 35px; max-width: 150px; object-fit: contain; }
        
        .doc-title-box { text-align: center; }
        .doc-title { font-size: 14px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
        .doc-subtitle { font-size: 10px; color: #64748b; font-weight: bold; margin-top: 3px; }
        .badge-ai { background: #fffbeb; border: 1px solid #fde68a; color: #d97706; padding: 3px 8px; border-radius: 4px; font-size: 9px; display: inline-block; margin-top: 5px; font-weight: bold; }

        /* PROJE VE MÜŞTERİ KÜNYESİ */
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .info-grid td { padding: 8px; border: 1px solid #e2e8f0; font-size: 10px; }
        .info-grid .label { background: #f8fafc; font-weight: bold; color: #475569; width: 20%; text-transform: uppercase; }
        .info-grid .val { color: #0f172a; width: 30%; font-weight: bold; }

        /* TOPLAM MALİYET GÖSTERGESİ (DEV) */
        .grand-total-box { 
            background: #f8fafc; 
            border-left: 5px solid #ffb700; 
            padding: 15px 20px; 
            margin-bottom: 25px; 
            border-radius: 0 8px 8px 0;
            display: table;
            width: 100%;
            box-sizing: border-box;
        }
        .gt-left { display: table-cell; vertical-align: middle; width: 60%; }
        .gt-right { display: table-cell; vertical-align: middle; width: 40%; text-align: right; }
        .gt-title { font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .gt-amount { font-size: 28px; font-weight: bold; color: #0f172a; }
        .gt-currency { font-size: 14px; font-weight: normal; color: #475569; }
        
        .currency-rates { font-size: 9px; color: #64748b; margin-top: 6px; }
        .currency-rates strong { color: #0f172a; }

        /* METRAJ / BİRİM FİYAT TABLOSU */
        .section-title { font-size: 12px; font-weight: bold; color: #0f172a; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; }
        
        .cost-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .cost-table th { background: #0f172a; color: #ffffff; text-align: left; padding: 10px 12px; font-size: 10px; text-transform: uppercase; border: 1px solid #0f172a; }
        .cost-table td { padding: 9px 12px; border: 1px solid #e2e8f0; font-size: 10px; color: #334155; }
        .cost-table tr:nth-child(even) { background: #f8fafc; }
        .cost-table tr:last-child td { border-bottom: 2px solid #0f172a; }
        
        .progress-bar { width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: 4px; }
        .progress-fill { height: 100%; background: #ffb700; }

        /* ALTYAZI VE İMZA BLOĞU */
        .signature-section { width: 100%; display: table; margin-top: 40px; page-break-inside: avoid; }
        .sig-left { display: table-cell; width: 60%; vertical-align: top; font-size: 9px; color: #64748b; text-align: justify; padding-right: 20px; }
        .sig-right { display: table-cell; width: 40%; vertical-align: top; text-align: center; }
        
        .stamp-area { 
            border: 1px dashed #cbd5e1; 
            padding: 30px 10px 10px 10px; 
            border-radius: 8px; 
            background: #f8fafc;
            color: #0f172a;
        }
        .stamp-title { font-weight: bold; font-size: 11px; margin-bottom: 30px; text-transform: uppercase; }
        .stamp-line { border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 9px; }

        .footer-strip { position: fixed; bottom: -10px; left: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>

    {{-- 1. HEADER: ANTETLİ KAĞIT FORMATI --}}
    <div class="pdf-header">
        <table class="header-table">
            <tr>
                <td style="width: 30%; text-align: left;">
                    {{-- KAİNTA LOGO --}}
                    <img src="{{ public_path('brand/kainta-logo.jpg') }}" class="logo-kainta" alt="Kainta İnşaat">
                </td>
                <td style="width: 40%;" class="doc-title-box">
                    <div class="doc-title">KEŞİF VE METRAJ ÖN RAPORU</div>
                    <div class="doc-subtitle">2026 ÇŞB Rayiç Bedelleri Baz Alınmıştır</div>
                    <div class="badge-ai">Ön fizibilite · yaklaşık değerler</div>
                </td>
                <td style="width: 30%; text-align: right;">
                    {{-- WORKSITEM LOGO --}}
                    <img src="{{ public_path('brand/worksitem-logo.png') }}" class="logo-worksitem" alt="Worksitem">
                </td>
            </tr>
        </table>
    </div>

    {{-- 2. PROJE KÜNYESİ --}}
    <table class="info-grid">
        <tr>
            <td class="label">Proje Adı</td>
            <td class="val" colspan="3">{{ $project->name ?? 'İsimsiz Proje' }}</td>
        </tr>
        <tr>
            <td class="label">Lokasyon</td>
            <td class="val">{{ ucfirst($project->city ?? 'Belirtilmedi') }} / {{ ucfirst($project->district ?? 'Genel') }}</td>
            <td class="label">Rapor Tarihi</td>
            <td class="val">{{ date('d.m.Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">İnşaat Alanı</td>
            <td class="val">{{ number_format($project->construction_area ?? 0, 0, ',', '.') }} m²</td>
            <td class="label">Referans No</td>
            <td class="val" style="font-family: monospace;">WKS-{{ $project->id ?? rand(1000,9999) }}-{{ date('Y') }}</td>
        </tr>
        <tr>
            <td class="label">Kalite Sınıfı</td>
            <td class="val">{{ ucfirst($project->quality ?? 'Standart') }} Segman</td>
            <td class="label">Geçerlilik</td>
            <td class="val">15 Gün (Piyasa Oynaklığı)</td>
        </tr>
    </table>

    {{-- 3. TOPLAM MALİYET ÖZETİ (GÖSTERGE) --}}
    <div class="grand-total-box">
        <div class="gt-left">
            <div class="gt-title">ANAHTAR TESLİM YAKLAŞIK MALİYET (KDV HARİÇ)</div>
            <div class="gt-amount">
                {{ number_format($breakdown['total'] ?? $project->estimated_cost ?? 0, 0, ',', '.') }} 
                <span class="gt-currency">TL</span>
            </div>
        </div>
        <div class="gt-right">
            <div class="gt-title">GLOBAL PARİTE (BİLGİ AMAÇLI)</div>
            <div class="currency-rates">
                <strong>USD:</strong> ${{ number_format(($breakdown['total'] ?? 0) * 0.024, 0, ',', '.') }} <br>
                <strong>EUR:</strong> €{{ number_format(($breakdown['total'] ?? 0) * 0.022, 0, ',', '.') }} <br>
                <strong>GBP:</strong> £{{ number_format(($breakdown['total'] ?? 0) * 0.019, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- 4. METRAJ VE KIRILIM TABLOSU --}}
    <div class="section-title">DETAYLI METRAJ VE MALİYET KIRILIMI</div>
    <table class="cost-table">
        <thead>
            <tr>
                <th style="width: 45%;">İmalat Açıklaması</th>
                <th style="width: 25%;">Bütçe Oranı</th>
                <th style="width: 30%; text-align: right;">Maliyet (TL)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($breakdown['details']) && is_array($breakdown['details']))
                @foreach($breakdown['details'] as $title => $data)
                <tr>
                    <td><strong>{{ $title }}</strong></td>
                    <td>
                        <span style="font-weight: bold; color: #0f172a;">%{{ $data['percent'] }}</span>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $data['percent'] * 1.5 }}%;"></div>
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: #0f172a;">
                        {{ number_format($data['cost'], 0, ',', '.') }} TL
                    </td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="3" style="text-align: center; padding: 20px;">Maliyet kırılım detayları hesaplanıyor...</td></tr>
            @endif
        </tbody>
    </table>

    {{-- 5. YASAL UYARI VE İMZA BLOĞU --}}
    <div class="signature-section">
        <div class="sig-left">
            <strong>YASAL UYARI VE BİLGİLENDİRME:</strong><br>
            İşbu belge, 2026 yılı Çevre, Şehircilik ve İklim Değişikliği Bakanlığı Resmi Gazete tebliğleri ile demir, çimento ve beton borsasındaki anlık veriler kullanılarak <strong>Worksitem Yapay Zeka (AI) Motoru</strong> tarafından otomatik olarak üretilmiş bir "Fizibilite / Yaklaşık Maliyet" raporudur.<br><br>
            Saha koşulları, zemin etüdü (jeolojik durum), mimari detaylar ve işveren ek isteklerine göre maliyetler değişkenlik gösterebilir. Kesin ve bağlayıcı "Anahtar Teslim Taahhüt Teklifi" için projenin rölöve ve uygulama projeleri ile birlikte <strong>Kainta İnşaat</strong> uzman mühendis kadrosu tarafından yerinde keşif yapılması zorunludur.
        </div>
        <div class="sig-right">
            <div class="stamp-area">
                <div class="stamp-title">ONAY VE KABUL</div>
                <br><br><br>
                <div class="stamp-line">KAİNTA İNŞAAT VE MİMARLİK A.Ş.<br>Yetkili İmza / Kaşe</div>
            </div>
        </div>
    </div>

    {{-- SAYFA ALTI BİLGİSİ --}}
    <div class="footer-strip">
        Bu belge elektronik olarak üretilmiştir. Barkod No: WKS-{{ date('Ymd-His') }} | İletişim: info@worksitem.com - www.worksitem.com
    </div>

</body>
</html>
