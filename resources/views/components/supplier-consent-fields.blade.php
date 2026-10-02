<div style="margin:10px 0; padding:10px; border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; text-align:left;">
    <label style="display:flex; align-items:flex-start; gap:7px; cursor:pointer; color:#334155; font-size:0.72rem; line-height:1.35;">
        <input
            type="checkbox"
            name="contact_consent"
            value="1"
            required
            style="width:16px !important; min-width:16px; height:16px; margin:1px 0 0; padding:0; accent-color:#2563eb; flex:0 0 16px;"
        >
        <span>
            <a
                href="{{ route('legal.kvkk_aydinlatma') }}"
                target="_blank"
                rel="noopener noreferrer"
                style="color:#2563eb; font-weight:700; text-decoration:underline;"
            >KVKK Aydınlatma Metni</a>'ni okudum.
            Tedarikçi / iş ortaklığı başvurum hakkında benimle iletişime
            geçilmesini kabul ediyorum.
        </span>
    </label>

    <label style="display:flex; align-items:flex-start; gap:7px; cursor:pointer; color:#64748b; font-size:0.72rem; line-height:1.35; margin-top:7px;">
        <input
            type="checkbox"
            name="marketing_consent"
            value="1"
            style="width:16px !important; min-width:16px; height:16px; margin:1px 0 0; padding:0; accent-color:#059669; flex:0 0 16px;"
        >
        <span>
            Kampanya ve hizmetlere ilişkin ticari elektronik iletileri
            almak istiyorum. Bu tercih isteğe bağlıdır.
        </span>
    </label>
</div>