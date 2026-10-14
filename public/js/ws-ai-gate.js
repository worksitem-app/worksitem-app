/* Worksitem — AI kullanım hakkı penceresi.
 * AI uç noktalarından dönen 401 (giriş gerekli) / 402 (hak bitti) yanıtlarını yakalar.
 * Telefonla SMS koduyla giriş yapılınca asıl isteği otomatik olarak yeniden gönderir.
 */
(function () {
    'use strict';
    var cfgEl = document.getElementById('wsag-config');
    if (!cfgEl || window.__wsAiGate) return;
    window.__wsAiGate = true;

    var C = {};
    try { C = JSON.parse(cfgEl.textContent); } catch (e) { return; }

    var back = document.getElementById('wsagBack');
    var box = document.getElementById('wsagBox');
    var nativeFetch = window.fetch.bind(window);
    var nf = new Intl.NumberFormat('tr-TR');

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return (m && m.content) || C.csrf || '';
    }

    function isAi(input) {
        var url = typeof input === 'string' ? input : (input && input.url) || '';
        var path;
        try { path = new URL(url, location.href).pathname; } catch (e) { return false; }
        if (/^\/maliyet-hesapla\/proje\/[^/]+\/render$/.test(path)) return true;
        return (C.endpoints || []).indexOf(path) !== -1;
    }

    function post(url, data) {
        return nativeFetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(data || {})
        }).then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { j.__status = r.status; return j; }); });
    }

    function open(html) {
        box.innerHTML = '<button type="button" class="wsag-x" aria-label="Kapat" data-close>×</button>' + html;
        back.hidden = false;
        document.documentElement.style.overflow = 'hidden';
    }

    function close() {
        back.hidden = true;
        box.innerHTML = '';
        document.documentElement.style.overflow = '';
    }

    function toast(html, ms) {
        var t = document.createElement('div');
        t.className = 'wsag-toast';
        t.setAttribute('role', 'status');
        t.innerHTML = html;
        document.body.appendChild(t);
        setTimeout(function () { t.remove(); }, ms || 5000);
    }

    function track(name, params) {
        if (typeof window.wsTrack === 'function') window.wsTrack(name, params || {});
    }

    /* ── Giriş penceresi: true (giriş yapıldı) ya da false (kapatıldı) ile çözülür ── */
    function loginDialog(data) {
        track('ai_gate_view', { gate: 'login' });
        return new Promise(function (resolve) {
            open(
                '<span class="wsag-tag">Günde ' + C.freePerDay + ' ücretsiz</span>' +
                '<h2 id="wsagTitle">Telefonunuzla giriş yapın</h2>' +
                '<p>' + esc(data.message || 'Yapay zekâ özellikleri telefonla giriş yapan kullanıcılara günde ' + C.freePerDay + ' kez ücretsizdir.') + '</p>' +
                '<form id="wsagForm" novalidate>' +
                '<label class="f" for="wsagPhone">Cep telefonu</label>' +
                '<div class="wsag-row"><input type="tel" id="wsagPhone" inputmode="tel" autocomplete="tel" maxlength="30" placeholder="05XX XXX XX XX" required>' +
                '<button type="button" class="wsag-btn o" id="wsagSend">Kod gönder</button></div>' +
                '<div class="wsag-msg" id="wsagSendMsg" aria-live="polite"></div>' +
                '<label class="f" for="wsagCode">SMS kodu</label>' +
                '<input type="text" id="wsagCode" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="6 haneli kod" required>' +
                '<label class="wsag-chk"><input type="checkbox" id="wsagKvkk"><span><a href="' + esc(C.kvkkUrl) + '" target="_blank" rel="noopener">KVKK Aydınlatma Metni</a>\'ni okudum. Telefonum giriş ve kullanım hakkı takibi için işlenir.</span></label>' +
                '<button type="submit" class="wsag-btn y" id="wsagGo">Giriş yap ve devam et</button>' +
                '<div class="wsag-msg" id="wsagMsg" aria-live="polite"></div>' +
                '</form>' +
                '<p class="wsag-note">Sizi aramayız; numaranız yalnızca giriş ve kullanım hakkı için kullanılır. Temel hesaplayıcılar girişsiz ve ücretsizdir.</p>'
            );

            var done = false;
            function finish(ok) { if (done) return; done = true; close(); resolve(ok); }
            box.querySelector('[data-close]').addEventListener('click', function () { finish(false); });
            back.onclick = function (e) { if (e.target === back) finish(false); };

            var phone = box.querySelector('#wsagPhone');
            var send = box.querySelector('#wsagSend');
            var sendMsg = box.querySelector('#wsagSendMsg');
            var msg = box.querySelector('#wsagMsg');
            setTimeout(function () { phone.focus(); }, 50);

            send.addEventListener('click', function () {
                sendMsg.className = 'wsag-msg';
                if (phone.value.replace(/\D/g, '').length < 10) { sendMsg.className = 'wsag-msg err'; sendMsg.textContent = 'Lütfen 05XX XXX XX XX biçiminde yazın.'; return; }
                send.disabled = true; send.textContent = 'Gönderiliyor…';
                post(C.codeUrl, { phone: phone.value }).then(function (j) {
                    var ok = j.__status === 200 && j.ok;
                    sendMsg.className = 'wsag-msg ' + (ok ? 'ok' : 'err');
                    sendMsg.textContent = j.message || (ok ? 'Kod gönderildi.' : 'Kod gönderilemedi.');
                    if (ok) box.querySelector('#wsagCode').focus();
                    var wait = ok ? 60 : 5;
                    var t = setInterval(function () {
                        wait--; send.textContent = wait > 0 ? 'Tekrar (' + wait + ')' : 'Tekrar gönder';
                        if (wait <= 0) { clearInterval(t); send.disabled = false; }
                    }, 1000);
                }).catch(function () { sendMsg.className = 'wsag-msg err'; sendMsg.textContent = 'Bağlantı hatası.'; send.disabled = false; send.textContent = 'Kod gönder'; });
            });

            box.querySelector('#wsagForm').addEventListener('submit', function (e) {
                e.preventDefault();
                msg.className = 'wsag-msg err';
                var code = box.querySelector('#wsagCode').value.replace(/\D/g, '');
                if (code.length !== 6) { msg.textContent = 'Telefonunuza gelen 6 haneli kodu yazın.'; return; }
                if (!box.querySelector('#wsagKvkk').checked) { msg.textContent = 'Devam etmek için aydınlatma metnini onaylayın.'; return; }
                var go = box.querySelector('#wsagGo');
                go.disabled = true; go.textContent = 'Doğrulanıyor…';
                post(C.loginUrl, { phone: phone.value, code: code, kvkk: '1' }).then(function (j) {
                    if (j.__status === 200 && j.ok) {
                        if (j.csrf) {
                            C.csrf = j.csrf;
                            var m = document.querySelector('meta[name="csrf-token"]');
                            if (m) m.content = j.csrf;
                        }
                        track('login', { method: 'sms' });
                        finish(true);
                        toast('Giriş yapıldı · bugün <b>' + j.free_left + '</b> ücretsiz hak' + (j.credits ? ', <b>' + nf.format(j.credits) + '</b> kredi' : ''));
                    } else {
                        msg.textContent = j.message || 'Giriş yapılamadı.';
                        go.disabled = false; go.textContent = 'Giriş yap ve devam et';
                    }
                }).catch(function () { msg.textContent = 'Bağlantı hatası.'; go.disabled = false; go.textContent = 'Giriş yap ve devam et'; });
            });
        });
    }

    /* ── Hak bitti penceresi ── */
    function creditsDialog(data) {
        track('ai_gate_view', { gate: 'credits' });
        var pay = !!C.payUrl;
        var pk = (C.packages || []).map(function (p, i) {
            var inner = '<span>' + nf.format(p.credits) + ' kredi' + (p.badge ? '<small>' + esc(p.badge) + '</small>' : '') + '</span><span>' + nf.format(p.price) + ' TL' + (pay ? ' · Kartla öde →' : '') + '</span>';
            return pay ? '<button type="button" data-pk="' + i + '" class="' + (p.badge ? 'rec' : '') + '">' + inner + '</button>'
                : '<div class="' + (p.badge ? 'rec' : '') + '">' + inner + '</div>';
        }).join('');
        var waText = 'Merhaba, Worksitem yapay zekâ kredisi almak istiyorum.' + (data.phone ? ' Giriş telefonum: ' + data.phone : '');
        open(
            '<span class="wsag-tag">Kredi</span>' +
            '<h2 id="wsagTitle">Bugünkü ücretsiz hakkınız bitti</h2>' +
            '<p>' + esc(data.message || '') + '</p>' +
            '<div class="wsag-pk">' + pk + '</div>' +
            '<p class="wsag-note" id="wsagPayMsg" role="status"></p>' +
            '<a class="wsag-btn ' + (pay ? '' : 'y') + '" target="_blank" rel="noopener" id="wsagWa" href="https://wa.me/' + esc(C.whatsapp) + '?text=' + encodeURIComponent(waText) + '">WhatsApp ile kredi al</a>' +
            '<p class="wsag-note">1 kredi = 1 yapay zekâ işlemi. Fiyatlara KDV dahildir. ' + (pay ? 'Kartla ödeme iyzico güvenli ödeme sayfasında yapılır; kredi ödemeden hemen sonra hesabınıza yüklenir.' : 'Online kartla ödeme yakında; şimdilik kredi talebiniz Kurumsal Çağrı Merkezi üzerinden alınır ve hesabınıza yüklenir.') + ' Yarın yine ' + C.freePerDay + ' ücretsiz hakkınız olacak.' +
            (data.phone ? '<br>Giriş: ' + esc(data.phone) + ' · <button type="button" class="wsag-link" id="wsagOut">Çıkış yap</button>' : '') + '</p>'
        );
        box.querySelector('[data-close]').addEventListener('click', close);
        back.onclick = function (e) { if (e.target === back) close(); };
        box.querySelector('#wsagWa').addEventListener('click', function () { track('ai_credit_request', {}); });
        box.querySelectorAll('[data-pk]').forEach(function (b) {
            b.addEventListener('click', function () {
                var m = box.querySelector('#wsagPayMsg'); m.textContent = 'Ödeme sayfası açılıyor…';
                track('ai_credit_checkout', { package: b.getAttribute('data-pk') });
                post(C.payUrl, { package: +b.getAttribute('data-pk') }).then(function (j) {
                    if (j && j.ok && j.url) { location.href = j.url; } else { m.textContent = (j && j.message) || 'Ödeme başlatılamadı.'; }
                }).catch(function () { m.textContent = 'Bağlantı hatası.'; });
            });
        });
        var out = box.querySelector('#wsagOut');
        if (out) out.addEventListener('click', function () { post(C.logoutUrl).then(function () { close(); toast('Çıkış yapıldı'); }); });
    }

    function quotaToast(res) {
        var h = res.headers && res.headers.get('X-AI-Quota');
        if (!h || !res.ok) return;
        var q; try { q = JSON.parse(h); } catch (e) { return; }
        if (q.used === 'free') {
            toast(q.free_left > 0 ? 'Ücretsiz hakkınızdan kullanıldı · bugün <b>' + q.free_left + '</b> hak kaldı'
                : 'Bugünkü ücretsiz hakkınızı kullandınız' + (q.credits ? ' · <b>' + nf.format(q.credits) + '</b> krediniz var' : ''), 6000);
        } else if (q.used === 'use') {
            toast('1 kredi kullanıldı · kalan <b>' + nf.format(q.credits) + '</b> kredi', 6000);
        }
    }

    function withCsrf(init) {
        if (!init || !init.headers) return init;
        var token = csrf();
        if (init.headers instanceof Headers) {
            if (init.headers.has('X-CSRF-TOKEN')) init.headers.set('X-CSRF-TOKEN', token);
        } else if (init.headers['X-CSRF-TOKEN'] !== undefined) {
            init.headers['X-CSRF-TOKEN'] = token;
        }
        return init;
    }

    function readGate(res) {
        if (res.status !== 401 && res.status !== 402) return Promise.resolve(null);
        return res.clone().json().then(function (j) { return j && j.ai_gate ? j : null; }).catch(function () { return null; });
    }

    window.fetch = function (input, init) {
        if (!isAi(input)) return nativeFetch(input, init);
        return nativeFetch(input, init).then(function (res) {
            return readGate(res).then(function (gate) {
                if (!gate) { quotaToast(res); return res; }
                if (gate.ai_gate === 'credits') { creditsDialog(gate); return res; }
                return loginDialog(gate).then(function (ok) {
                    if (!ok) return res;
                    return nativeFetch(input, withCsrf(init)).then(function (res2) {
                        return readGate(res2).then(function (g2) {
                            if (g2 && g2.ai_gate === 'credits') creditsDialog(g2);
                            else quotaToast(res2);
                            return res2;
                        });
                    });
                });
            });
        });
    };
})();
