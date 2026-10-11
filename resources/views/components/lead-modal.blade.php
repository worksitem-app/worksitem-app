<div id="leadModalOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15, 23, 42, 0.75); backdrop-filter:blur(5px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#ffffff; border-radius:20px; max-width:520px; width:100%; padding:32px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); position:relative; animation:modalPop 0.25s ease-out;">
        
        <button type="button" onclick="closeLeadModal()" style="position:absolute; top:20px; right:20px; background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-weight:800; color:#64748b; display:flex; align-items:center; justify-content:center;">✕</button>

        <div style="margin-bottom:24px;">
            <h2 style="font-size:1.4rem; font-weight:800; color:#0f172a; margin:0 0 8px 0; letter-spacing:-0.5px;">PROFESYONEL KEŞİF TALEBİ</h2>
            <p style="color:#64748b; font-size:0.875rem; line-height:1.5; margin:0;">Uzman ekibimizin projenizi incelemesi ve size özel çözümler sunması için iletişim bilgilerinizi bırakın.</p>
        </div>

        <form action="{{ url('/lead-kaydet') }}" method="POST" id="leadModalForm">
            @csrf
                        <input type="hidden" name="source_tool" id="modalSourceTool" value="Anasayfa Formu">

            <input type="hidden" name="consent_version" value="2026-09-v1">
            <input type="hidden" name="utm_source" data-lead-tracking="utm_source">
            <input type="hidden" name="utm_medium" data-lead-tracking="utm_medium">
            <input type="hidden" name="utm_campaign" data-lead-tracking="utm_campaign">
            <input type="hidden" name="utm_content" data-lead-tracking="utm_content">
            <input type="hidden" name="utm_term" data-lead-tracking="utm_term">
            <input type="hidden" name="landing_page" data-lead-tracking="landing_page">
            <input type="hidden" name="referrer" data-lead-tracking="referrer">

            <!-- Proje Tipi -->
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#334155; margin-bottom:8px;">Proje Tipi *</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; cursor:pointer; font-size:0.9rem; font-weight:600; color:#1e293b;">
                        <input type="radio" name="project_type" value="Yeni İnşaat (Villa, Bina, Fabrika)" checked style="accent-color:#101318;">
                        Yeni İnşaat (Villa, Bina, Fabrika)
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; cursor:pointer; font-size:0.9rem; font-weight:600; color:#1e293b;">
                        <input type="radio" name="project_type" value="Tadilat ve Renovasyon" style="accent-color:#101318;">
                        Tadilat ve Renovasyon
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:1px solid #cbd5e1; border-radius:10px; cursor:pointer; font-size:0.9rem; font-weight:600; color:#1e293b;">
                        <input type="radio" name="project_type" value="Diğer / Emin Değilim" style="accent-color:#101318;">
                        Diğer / Emin Değilim
                    </label>
                </div>
            </div>

            <!-- Ad Soyad -->
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#334155; margin-bottom:4px;">Ad Soyad *</label>
                <input type="text" name="full_name" required placeholder="Adınız ve Soyadınız" style="width:100%; padding:11px 14px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; font-size:0.95rem; background:#f8fafc; outline:none;">
            </div>

            <!-- Telefon Numaranız -->
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#334155; margin-bottom:4px;">Telefon Numaranız *</label>
                <input type="tel" name="phone" required placeholder="05XX XXX XX XX" style="width:100%; padding:11px 14px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; font-size:0.95rem; background:#f8fafc; outline:none;">
            </div>

            <!-- Ek Notlarınız -->
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#334155; margin-bottom:4px;">Ek Notlarınız (Opsiyonel)</label>
                <textarea name="notes" id="modalNotes" rows="2" placeholder="Projeniz hakkında eklemek istediğiniz detaylar..." style="width:100%; padding:11px 14px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; font-size:0.95rem; background:#f8fafc; outline:none;"></textarea>
            </div>

            <!-- KVKK ve İletişim Tercihleri -->
            <div style="margin-bottom:18px; padding:14px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc;">
                <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; color:#334155; font-size:0.78rem; line-height:1.5;">
                    <input
                        type="checkbox"
                        name="contact_consent"
                        value="1"
                        required
                        style="margin-top:3px; accent-color:#101318;"
                    >
                    <span>
                        <a
                            href="{{ route('legal.kvkk_aydinlatma') }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            style="color:#101318; font-weight:700; text-decoration:underline;"
                        >KVKK Aydınlatma Metni</a>'ni okudum.
                        Proje ve keşif talebimin değerlendirilmesi amacıyla telefon
                        veya e-posta yoluyla benimle iletişime geçilmesini kabul ediyorum.
                        <strong>*</strong>
                    </span>
                </label>

                <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; color:#64748b; font-size:0.78rem; line-height:1.5; margin-top:12px;">
                    <input
                        type="checkbox"
                        name="marketing_consent"
                        value="1"
                        style="margin-top:3px; accent-color:#059669;"
                    >
                    <span>
                        Kampanya, hizmet ve bilgilendirme içerikli ticari elektronik
                        iletileri almak istiyorum. Bu tercih isteğe bağlıdır.
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" style="width:100%; background:#059669; color:#ffffff; font-weight:800; font-size:1rem; padding:15px; border-radius:10px; border:none; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                ÜCRETSİZ KEŞİF İSTE →
            </button>
        </form>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
(function initialiseLeadTracking() {
    const keys = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term'
    ];

    const params = new URLSearchParams(window.location.search);

    keys.forEach(function (key) {
        const storageKey = 'worksitem_first_' + key;
        const queryValue = params.get(key);

        if (queryValue && !sessionStorage.getItem(storageKey)) {
            sessionStorage.setItem(storageKey, queryValue.substring(0, 150));
        }

        const value = sessionStorage.getItem(storageKey) || queryValue || '';
        const input = document.querySelector('[data-lead-tracking="' + key + '"]');

        if (input) {
            input.value = value.substring(0, 150);
        }
    });

    const landingPage = document.querySelector(
        '[data-lead-tracking="landing_page"]'
    );

    const referrer = document.querySelector(
        '[data-lead-tracking="referrer"]'
    );

    if (landingPage) {
        landingPage.value = window.location.href.substring(0, 2000);
    }

    if (referrer) {
        referrer.value = document.referrer.substring(0, 2000);
    }
})();
function openLeadModal(source = 'Anasayfa Formu', defaultNotes = '') {
    document.getElementById('modalSourceTool').value = source;
    if(defaultNotes) {
        document.getElementById('modalNotes').value = defaultNotes;
    }
    document.getElementById('leadModalOverlay').style.display = 'flex';
}

function closeLeadModal() {
    document.getElementById('leadModalOverlay').style.display = 'none';
}

document.getElementById('leadModalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeLeadModal();
});
</script>
