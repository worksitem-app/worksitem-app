<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Proje Özeti | Kainta İnşaat</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fa;
            color: #18202a;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            width: min(900px, calc(100% - 28px));
            margin: 0 auto;
            padding: 50px 0 70px;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
        }

        h1 {
            margin: 18px 0 10px;
            font-size: clamp(30px, 5vw, 46px);
        }

        .subtitle {
            color: #64748b;
            line-height: 1.6;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 18px;
            box-shadow: 0 7px 22px rgba(15, 23, 42, .04);
        }

        .card h2 {
            margin: 0 0 18px;
            font-size: 20px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            padding: 14px 0;
            border-bottom: 1px solid #eef1f5;
        }

        .row:last-child {
            border-bottom: 0;
        }

        .label {
            color: #64748b;
        }

        .value {
            font-weight: 700;
            text-align: right;
        }

        .actions {
            margin-top: 24px;
        }

        .button {
            display: block;
            width: 100%;
            padding: 16px 20px;
            border-radius: 14px;
            background: #111827;
            color: #fff;
            text-align: center;
            font-weight: 800;
            text-decoration: none;
        }

        .note {
            margin-top: 18px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <nav class="navbar" style="background:#fff; border-bottom:1px solid #e2e8f0; padding:16px 5%; display:flex; justify-content:space-between; align-items:center;">
        <a href="https://kainta.com" target="_blank" rel="noopener" style="font-size:22px; font-weight:800; color:#0f172a; text-decoration:none;">KAINTA <span style="background:#101318; color:white; padding:4px 8px; border-radius:6px; font-size:12px;">AI</span></a>
        <div><a href="https://worksitem.com" style="color:#64748b; text-decoration:none; font-weight:500;">Ana Site</a></div>
    </nav>



<div class="container">

    <div class="header">
        <div class="badge">Kainta İnşaat AI Proje Asistanı</div>

        <h1>Projenizi Kontrol Edin</h1>

        <div class="subtitle">
            Girdiğiniz bilgileri kontrol edin. Bilgiler doğruysa
            bir sonraki adımda ön maliyet hesabını başlatacağız.
        </div>
    </div>

    <div class="card">

        <h2>{{ $project->projectType->name }} Projesi</h2>

        @foreach($project->answers as $answer)

            <div class="row">

                <div class="label">
                    {{ $answer->question->question }}
                </div>

                <div class="value">

                    @if($answer->option)
                        {{ $answer->option->label }}
                    @else
                        {{ $answer->answer_value }}
                    @endif

                </div>

            </div>

        @endforeach

    </div>

    <div class="actions">
        <a
            href="{{ route('projects.calculate', $project) }}"
            class="button"
        >
            Bilgiler Doğru — Ön Maliyeti Hesapla →
        </a>
    </div>

    <div class="note">
        Bu sonuç ön fizibilite amaçlıdır. Kesin teklif için proje,
        keşif, teknik şartname ve saha koşulları ayrıca değerlendirilir.
    </div>

</div>


    <footer style="background:#1e293b; color:#94a3b8; padding:30px 20px; text-align:center; font-size:14px; margin-top:50px;">
        &copy; 2026 Kainta İnşaat Mimarlık. Tüm hakları saklıdır.<br><span style="font-size:11px; opacity:0.6;">Yapay zeka destekli motor</span>
    </footer>
</body>
</html>
