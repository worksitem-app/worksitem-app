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
            Proje ve keşif talebim hakkında benimle iletişime
            geçilmesini kabul ediyorum. <strong>*</strong>
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
@once
{{-- Teklif formu ön kontrolü: hatayı alanın hemen altında gösterir, form gönderilmeden düzeltilir --}}
<script type="application/json" id="ws-lead-guard-words">{!! json_encode(['words' => config('worksitem_lead_guard.words', []), 'stems' => config('worksitem_lead_guard.stems', []), 'fake' => config('worksitem_lead_guard.fake_words', []), 'rows' => config('worksitem_lead_guard.keyboard_rows', [])], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<datalist id="ws-tr-iller">@foreach (config('worksitem_lead_guard.cities', []) as $il)<option value="{{ $il }}">@endforeach</datalist>
<style>
.ws-lead-err{display:block;margin:6px 0 0;padding:8px 10px;border-radius:8px;background:#FEF2F2;border:1px solid #FCA5A5;color:#991B1B;font-size:13px;font-weight:600;line-height:1.4;text-align:left}
.ws-lead-invalid{border-color:#DC2626!important;box-shadow:0 0 0 3px rgba(220,38,38,.12)!important}
</style>
<script>
(function () {
    var W = { words: [], stems: [] };
    try { W = JSON.parse(document.getElementById('ws-lead-guard-words').textContent); } catch (e) {}
    function norm(s) { return String(s || '').replace(/İ/g, 'i').replace(/I/g, 'ı').toLocaleLowerCase('tr-TR').trim(); }
    function offensive(text) {
        var t = norm(text); if (!t) return false;
        if (W.stems.some(function (s) { return t.indexOf(norm(s)) !== -1; })) return true;
        var parts = t.split(/[^0-9a-zçğıöşüâîû]+/i);
        return W.words.some(function (w) { return parts.indexOf(norm(w)) !== -1; });
    }
    // Sahte / anlamsız metin (sunucudaki App\Support\LeadTextGuard::looksFake ile aynı kurallar)
    var V = 'aeıioöuüâîû';
    function words(v) { return norm(v).split(/[^a-zçğıöşüâîûéäëïü]+/i).filter(Boolean); }
    function fake(v) {
        var fw = (W.fake || []).map(norm), rows = (W.rows || []).map(norm);
        return words(v).some(function (w) {
            var len = w.length;
            if (fw.indexOf(w) !== -1) return true;
            if (len < 3) return false;
            if (!new RegExp('[' + V + ']').test(w)) return true;
            if (new RegExp('[^' + V + ']{5,}').test(w)) return true;
            if (/(.)\1\1/.test(w)) return true;
            if (len >= 6 && /^(.{2,3})\1+$/.test(w)) return true;
            if (len >= 8 && (w.match(new RegExp('[' + V + ']', 'g')) || []).length / len < 0.15) return true;
            return rows.some(function (r) { for (var i = 0; i + 4 <= r.length; i++) { if (w.indexOf(r.substr(i, 4)) !== -1) return true; } return false; });
        });
    }
    var MSG = {
        name: 'Lütfen gerçek adınızı ve soyadınızı yazın (rakam olmadan). Örnek: Ayşe Yılmaz',
        phone: 'Telefon numaranızı 05XX XXX XX XX biçiminde, 11 haneli yazın. Örnek: 0532 123 45 67',
        place: 'Lütfen geçerli bir şehir ve ilçe yazın. Örnek: İzmir, Karşıyaka'
    };
    function letters(v) { return String(v || '').replace(/[^A-Za-zÇĞİÖŞÜçğıöşüÂÎÛâîû]/g, '').length; }
    function nameOk(v) { return !/\d/.test(v) && words(v).filter(function (w) { return w.length >= 2; }).length >= 2 && !fake(v) && !offensive(v); }
    function placeOk(v) { return letters(v) >= 3 && !/^\d+$/.test(String(v).trim()) && !fake(v) && !offensive(v); }
    // Şehir alanlarına 81 il önerisi
    function attachCities() {
        document.querySelectorAll('form input[name="city"], form input[name="location"]').forEach(function (el) {
            if (!el.getAttribute('list') && el.form && el.form.querySelector('[name="contact_consent"]')) el.setAttribute('list', 'ws-tr-iller');
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', attachCities); else attachCities();
    function phoneOk(v) {
        var d = String(v || '').replace(/\D+/g, '');
        if (d.indexOf('0090') === 0) d = d.slice(2);
        if (d.charAt(0) === '0' && d.length === 11) d = '90' + d.slice(1);
        if (d.length === 10) d = '90' + d;
        return /^90[2-5]\d{9}$/.test(d);
    }
    function anchor(el) { return el.type === 'checkbox' && el.closest('label') ? el.closest('label') : el; }
    function clear(el) {
        el.classList.remove('ws-lead-invalid');
        var a = anchor(el), n = a.parentNode.querySelector('.ws-lead-err[data-for="' + el.name + '"]');
        if (n) n.remove();
    }
    function fail(el, msg) {
        clear(el);
        el.classList.add('ws-lead-invalid');
        var n = document.createElement('div');
        n.className = 'ws-lead-err'; n.setAttribute('role', 'alert'); n.setAttribute('data-for', el.name);
        n.textContent = msg;
        anchor(el).insertAdjacentElement('afterend', n);
        return el;
    }
    function mark(first, el) { return first || el; }
    function check(form) {
        var first = null, f;
        form.querySelectorAll('.ws-lead-invalid').forEach(clear);
        f = form.querySelector('[name="name"],[name="full_name"]');
        if (f) { clear(f); var nm = f.value.trim();
            if (!nameOk(nm)) first = mark(first, fail(f, MSG.name)); }
        f = form.querySelector('[name="phone"]');
        if (f) { clear(f); if (!phoneOk(f.value)) first = mark(first, fail(f, MSG.phone)); }
        f = form.querySelector('[name="location"],[name="city"]');
        if (f && (f.required || f.value.trim() !== '')) { clear(f); if (!placeOk(f.value)) first = mark(first, fail(f, MSG.place)); }
        ['name', 'full_name', 'location', 'notes', 'city', 'district'].forEach(function (n) {
            var el = form.querySelector('[name="' + n + '"]');
            if (el && el.value && offensive(el.value) && !el.classList.contains('ws-lead-invalid')) first = mark(first, fail(el, 'Lütfen uygun bir dil kullanın.'));
        });
        f = form.querySelector('[name="contact_consent"]');
        if (f && !f.checked) first = mark(first, fail(f, 'Devam etmek için KVKK onay kutusunu işaretleyin.'));
        return first;
    }
    function guard(e, form) {
        if (!form || !form.querySelector || !form.querySelector('[name="contact_consent"]')) return;
        var bad = check(form);
        if (bad) {
            e.preventDefault(); e.stopImmediatePropagation();
            bad.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function () { try { bad.focus({ preventScroll: true }); } catch (x) {} }, 300);
        }
    }
    // Tarayıcının kendi "bu alanı doldurun" balonundan önce çalışsın diye gönder düğmesine tıklamada da kontrol
    document.addEventListener('click', function (e) {
        var b = e.target.closest && e.target.closest('button[type="submit"],input[type="submit"]');
        if (b) guard(e, b.form);
    }, true);
    document.addEventListener('submit', function (e) { guard(e, e.target); }, true);
    document.addEventListener('blur', function (e) {
        var el = e.target;
        if (!el || !el.form || !el.form.querySelector('[name="contact_consent"]') || !el.value) return;
        var rule = { phone: [phoneOk, MSG.phone], name: [nameOk, MSG.name], full_name: [nameOk, MSG.name], location: [placeOk, MSG.place], city: [placeOk, MSG.place] }[el.name];
        if (rule) { rule[0](el.value) ? clear(el) : fail(el, offensive(el.value) ? 'Lütfen uygun bir dil kullanın.' : rule[1]); }
    }, true);
})();
</script>
@endonce
