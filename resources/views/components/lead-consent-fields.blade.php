<div style="margin:14px 0; padding:13px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; text-align:left;">
    <label style="display:flex; align-items:flex-start; gap:9px; cursor:pointer; color:#334155; font-size:0.78rem; line-height:1.5;">
        <input
            type="checkbox"
            name="contact_consent"
            value="1"
            required
            style="margin-top:3px; accent-color:#2563eb;"
        >
        <span>
            <a
                href="{{ route('legal.kvkk_aydinlatma') }}"
                target="_blank"
                rel="noopener noreferrer"
                style="color:#2563eb; font-weight:700; text-decoration:underline;"
            >KVKK Aydınlatma Metni</a>'ni okudum.
            Proje ve keşif talebim hakkında benimle iletişime
            geçilmesini kabul ediyorum. <strong>*</strong>
        </span>
    </label>

    <label style="display:flex; align-items:flex-start; gap:9px; cursor:pointer; color:#64748b; font-size:0.78rem; line-height:1.5; margin-top:10px;">
        <input
            type="checkbox"
            name="marketing_consent"
            value="1"
            style="margin-top:3px; accent-color:#059669;"
        >
        <span>
            Kampanya ve hizmetlere ilişkin ticari elektronik iletileri
            almak istiyorum. Bu tercih isteğe bağlıdır.
        </span>
    </label>
</div>