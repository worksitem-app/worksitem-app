<!DOCTYPE html>
<html lang="tr">
<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->projectType->name ?? 'Proje' }} Bilgileri | Kainta İnşaat</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f5f7fa; color: #18202a; }
        .container { width: min(900px, calc(100% - 28px)); margin: 0 auto; padding: 48px 0 70px; }
        .header { text-align: center; margin-bottom: 35px; }
        .badge { display: inline-block; padding: 8px 13px; border-radius: 999px; background: #fff; border: 1px solid #e5e7eb; color: #64748b; font-size: 13px; font-weight: 700; }
        h1 { margin: 18px 0 10px; font-size: clamp(30px, 5vw, 46px); line-height: 1.1; }
        .subtitle { color: #64748b; font-size: 17px; line-height: 1.6; }
        .progress { height: 8px; background: #e5e7eb; border-radius: 999px; overflow: hidden; margin: 28px 0 36px; }
        .progress-bar { width: 100%; height: 100%; background: #111827; }
        .question { background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; padding: 24px; margin-bottom: 18px; box-shadow: 0 7px 22px rgba(15,23,42,.04); }
        .question-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .description { color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 16px; }
        input, select, textarea { width: 100%; border: 1px solid #d9dee7; border-radius: 12px; padding: 13px 14px; font: inherit; outline: none; background: #fff; }
        textarea { min-height: 110px; resize: vertical; }
        .options { display: grid; gap: 10px; }
        .option { display: block; }
        .option input { display: none; }
        .option span { display: block; padding: 14px 16px; border: 1px solid #d9dee7; border-radius: 12px; cursor: pointer; transition: .15s ease; }
        .option input:checked + span { border-color: #111827; background: #f1f5f9; font-weight: 700; }
        .submit { width: 100%; margin-top: 8px; padding: 16px 20px; border: 0; border-radius: 14px; background: #111827; color: white; font-size: 16px; font-weight: 800; cursor: pointer; }
        .footer-note { margin-top: 18px; text-align: center; color: #94a3b8; font-size: 13px; line-height: 1.5; }
        footer { background:#1e293b; color:#94a3b8; padding:30px 20px; text-align:center; font-size:14px; margin-top:50px; }
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
            <h1>{{ $project->projectType->name ?? 'Proje' }} Planınızı Oluşturalım</h1>
            <div class="subtitle">Size yaklaşık maliyet hesabı hazırlayabilmemiz için yalnızca gerekli bilgileri topluyoruz.</div>
        </div>

        <div class="progress">
            <div class="progress-bar"></div>
        </div>

        <form method="POST" action="{{ route('projects.answers.save', $project) }}">
            @csrf
            @foreach($questions as $question)
                <div class="question" data-slug="{{ $question->slug }}">
                    <div class="question-title">
                        {{ $question->question }}
                        @if($question->is_required) <span style="color:#dc2626">*</span> @endif
                    </div>

                    @if($question->description)
                        <div class="description">{{ $question->description }}</div>
                    @endif

                    @if($question->type === 'number')
                        <input type="number" name="answers[{{ $question->id }}]" min="0" step="0.01" {{ $question->is_required ? 'required' : '' }}>
                    @elseif($question->type === 'textarea')
                        <textarea name="answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}></textarea>
                    @elseif($question->type === 'select')
                        <select name="answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}>
                            <option value="">Seçiniz</option>
                            @foreach($question->options as $option)
                                <option value="{{ $option->value ?? $option->slug ?? $option->id ?? '' }}">
                                    {{ $option->label ?? $option->name ?? $option->text ?? $option->option_text ?? 'Seçenek' }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($question->type === 'radio')
                        <div class="options">
                            @foreach($question->options as $option)
                                <label class="option">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->value ?? $option->slug ?? $option->id ?? '' }}" {{ $question->is_required ? 'required' : '' }}>
                                    <span>{{ $option->label ?? $option->name ?? $option->text ?? $option->option_text ?? 'Seçenek' }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <input type="text" name="answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}>
                    @endif
                </div>
            @endforeach

            <button class="submit" type="submit">Cevapları Kaydet ve Devam Et →</button>
        </form>

        <div class="footer-note">Ön fizibilite sonuçları yaklaşık değerlerdir. Kesin teklif için proje ve saha koşulları ayrıca değerlendirilir.</div>
    </div>

    <footer>
        &copy; 2026 Kainta İnşaat Mimarlık. Tüm hakları saklıdır.<br>
        <span style="font-size:11px; opacity:0.6;">Yapay zeka destekli motor</span>
    </footer>
<script id="kainta-logic">
document.addEventListener('DOMContentLoaded', function () {
    // Mantık Ağacımız: Hangi soru, hangi cevapla, neleri açacak?
    const logicTree = [
        { 
            parentSlug: 'villa-havuz-istek', 
            triggerValue: 'evet', 
            childSlugs: ['villa-havuz-tipi', 'villa-havuz-alani', 'villa-havuz-ekstra'] 
        },
        { 
            parentSlug: 'villa-peyzaj-istek', 
            triggerValue: 'evet', 
            childSlugs: ['villa-peyzaj-alani'] 
        },
        { 
            parentSlug: 'fabrika-vinc', 
            triggerValue: 'evet', 
            childSlugs: ['fabrika-vinc-kapasite'] 
        }
    ];

    function applyLogic() {
        logicTree.forEach(rule => {
            const parentCard = document.querySelector('div[data-slug="' + rule.parentSlug + '"]');
            if (!parentCard) return;

            let selectedValue = null;
            const select = parentCard.querySelector('select');
            
            if (select) {
                selectedValue = select.value;
            } else {
                const checkedRadio = parentCard.querySelector('input[type="radio"]:checked');
                if (checkedRadio) selectedValue = checkedRadio.value;
            }

            const show = (selectedValue === rule.triggerValue);

            rule.childSlugs.forEach(childSlug => {
                const childCard = document.querySelector('div[data-slug="' + childSlug + '"]');
                if (childCard) {
                    childCard.style.display = show ? '' : 'none';
                    
                    // Gizlenen alanların zorunluluğunu (required) kaldır ve içini temizle
                    const inputs = childCard.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        input.disabled = !show; // Form gönderilirken arka plana gitmesini engeller
                        if (!show) {
                            if (input.type === 'radio' || input.type === 'checkbox') {
                                input.checked = false;
                            } else {
                                input.value = '';
                            }
                        }
                    });
                }
            });
        });
    }

    // Formdaki tüm açılır menülere ve seçim butonlarına dinleyici ekle
    const allInputs = document.querySelectorAll('.question select, .question input[type="radio"]');
    allInputs.forEach(input => {
        input.addEventListener('change', applyLogic);
    });

    // Sayfa ilk yüklendiğinde kuralları çalıştır (Gizlenmesi gerekenleri gizle)
    applyLogic();
});
</script>
</body>
</html>
