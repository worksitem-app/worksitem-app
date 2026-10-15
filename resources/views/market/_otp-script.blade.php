{{-- SMS kodu gönder düğmesi. Değişkenler: $url (POST), $btn, $phone, $msg, $code (eleman id'leri) --}}
<script>
(function () {
    var btn = document.getElementById(@json($btn)), msg = document.getElementById(@json($msg));
    if (!btn) return;
    var token = document.querySelector('meta[name="csrf-token"]').content;
    btn.addEventListener('click', function () {
        var phone = document.getElementById(@json($phone)).value.trim();
        msg.className = 'fm-msg';
        if (phone.replace(/\D/g, '').length < 10) { msg.className = 'fm-msg err'; msg.textContent = 'Lütfen 05XX XXX XX XX biçiminde yazın.'; return; }
        btn.disabled = true; btn.textContent = 'Gönderiliyor…';
        fetch(@json($url), { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ phone: phone }) })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (j) {
                msg.className = 'fm-msg ' + (j.ok ? 'ok' : 'err');
                msg.textContent = j.message || (j.ok ? 'Kod gönderildi.' : 'Kod gönderilemedi, biraz sonra tekrar deneyin.');
                if (j.ok) document.getElementById(@json($code)).focus();
                var wait = j.ok ? 60 : 5;
                var t = setInterval(function () { wait--; btn.textContent = wait > 0 ? 'Tekrar (' + wait + ')' : 'Kodu tekrar gönder'; if (wait <= 0) { clearInterval(t); btn.disabled = false; } }, 1000);
            }).catch(function () { msg.className = 'fm-msg err'; msg.textContent = 'Bağlantı hatası. Tekrar deneyin.'; btn.disabled = false; btn.textContent = 'Kod gönder'; });
    });
})();
</script>
