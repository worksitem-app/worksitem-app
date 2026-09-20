<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <title>{{ $lead->name }} - Kainta İnşaat Teklif Mektubu</title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.6; color: #000; background: #fff; margin:0; padding: 25mm 20mm; }
        
        .header { border-bottom: 3px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .logo-area { display: flex; align-items: center; gap: 10px; }
        .logo-box { background: #1e3a8a; color: #fff; padding: 8px 12px; font-family: sans-serif; font-weight: 900; font-size: 24px; letter-spacing: -1px; }
        .company-name { font-family: sans-serif; font-weight: 800; font-size: 18px; color: #1e3a8a; display: flex; flex-direction: column; }
        .company-name span { font-size: 10px; color: #64748b; font-weight: 400; text-transform: uppercase; letter-spacing: 1px; }
        
        .doc-meta { text-align: right; font-family: sans-serif; font-size: 10px; color: #64748b; }

        .title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 30px; text-transform: uppercase; color:#1e3a8a; }
        
        .customer-info { margin-bottom:30px; font-size:11pt; }
        
        .ai-content { text-align: justify; margin-bottom: 40px; }
        .ai-content ul { padding-left: 20px; }
        .ai-content li { margin-bottom: 8px; }

        .signatures { width: 100%; margin-top: 50px; border-collapse: collapse; }
        .signatures td { width: 50%; text-align: right; vertical-align: top; padding: 10px; }
        
        .footer { position: fixed; bottom: 15mm; left: 20mm; right: 20mm; border-top: 1px solid #cbd5e1; padding-top: 10px; font-family: sans-serif; font-size: 8pt; color: #94a3b8; text-align: center; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 25mm 20mm; }
        }
        .print-btn { background:#166534; color:#fff; border:none; padding:12px 24px; font-weight:bold; font-size:16px; cursor:pointer; border-radius:8px; margin: 20px auto; display: block; }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="background:#fefce8; border:1px solid #fde047; padding:15px; text-align:center; font-family:sans-serif; margin-bottom:30px; color:#854d0e;">
        <h2>🤖 Yapay Zeka Teklifi Hazırladı!</h2>
        <p>Google Gemini AI, müşterinin web sitesindeki eylemlerini ve notlarını analiz ederek aşağıdaki kurumsal niyet mektubunu yazdı.<br>Hedef olarak <b>"PDF Olarak Kaydet"</b> seçeneğini seçin ve müşterinize WhatsApp veya Mail ile iletin.</p>
        <button class="print-btn" onclick="window.print()">Teklif PDF'ini Al</button>
    </div>

    <!-- Letterhead -->
    <div class="header">
        <div class="logo-area">
            <img src="/brand/kainta-logo.jpg" alt="Kainta İnşaat" style="height:60px; border-radius:6px;">
            <div class="company-name">
                KAİNTA İNŞAAT
                <span>Mühendislik & Mimarlık A.Ş.</span>
            </div>
        </div>
        <div class="doc-meta">
            Tarih: {{ date('d.m.Y') }}<br>
            Teklif Ref: KNT-{{ $lead->id }}-{{ date('Y') }}
        </div>
    </div>

    <div class="customer-info">
        <strong>Sayın {{ $lead->name }},</strong><br>
        İletişim: {{ $lead->phone }}<br>
        Proje Konumu: {{ $lead->location }}<br>
        Konu: {{ strtoupper($lead->calculated_service ?: 'Tadilat/İnşaat') }} Ön Teklif Mektubu Hk.
    </div>

    <!-- AI TARAFINDAN ÜRETİLEN İÇERİK -->
    <div class="ai-content">
        {!! $aiProposalHtml !!}
    </div>

    <table class="signatures">
        <tr>
            <td>
                Saygılarımızla,<br><br>
                <strong>KAİNTA İNŞAAT A.Ş.</strong><br>
                Proje ve Keşif Departmanı
            </td>
        </tr>
    </table>

    <div class="footer">
        Bu ön teklif belgesi, müşteri talebi üzerine bilgilendirme amacıyla hazırlanmıştır. Nihai bağlayıcılığı uzman saha keşfi sonrasında düzenlenecek asıl sözleşme ile başlar.<br>
        Kainta İnşaat ve Mimarlık A.Ş. | kainta.com
    </div>

</body>
</html>
