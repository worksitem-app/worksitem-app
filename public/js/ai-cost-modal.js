// AI Cost Modal — window.currentGenerationId kullanır
// Bu dosyada DEĞİŞKEN TANIMI YOK

function openCostModal() {
    var generationId = window.currentGenerationId;

    if (!generationId) {
        alert("Tasarım ID bulunamadı. Lütfen tasarım tamamlanmasını bekleyin.");
        return;
    }

    var area = prompt("Oda alanını girin (m²):", "25");
    if (!area || parseFloat(area) <= 0) return;

    var modal = document.createElement("div");
    modal.id = "costModal";
    modal.style.cssText = "position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.85);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto;";
    modal.innerHTML = '<div style="background:#151c28;border-radius:16px;max-width:600px;width:100%;padding:30px;text-align:center;"><div style="font-size:48px;margin-bottom:20px;">⏳</div><h2 style="font-family:Bebas Neue,sans-serif;color:#38bdf8;font-size:28px;margin:0;">Maliyet Hesaplanıyor...</h2></div>';
    document.body.appendChild(modal);

    fetch("/ai-tasarim/ic-mekan/estimate/" + generationId, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
            "Accept": "application/json"
        },
        body: JSON.stringify({ room_area: parseFloat(area) })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.status === "success") {
            renderCostModal(data);
        } else {
            alert("Hata: " + (data.message || "Bilinmeyen hata"));
            modal.remove();
        }
    })
    .catch(function(err) {
        alert("Bağlantı hatası: " + err.message);
        modal.remove();
    });
}

function renderCostModal(data) {
    var modal = document.getElementById("costModal");
    if (!modal) return;

    var money = function(n) {
        return new Intl.NumberFormat("tr-TR", { maximumFractionDigits: 0 }).format(n) + " TL";
    };

    var itemsHTML = "";
    for (var i = 0; i < data.items.length; i++) {
        var item = data.items[i];
        itemsHTML += '<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #21262d;">'
            + '<div><strong style="color:#f0f6fc;">' + item.label + '</strong><br>'
            + '<span style="color:#8b949e;font-size:12px;">' + item.quantity + " " + item.unit + " × " + money(item.price) + '</span></div>'
            + '<div style="color:#38bdf8;font-weight:700;">' + money(item.total) + '</div>'
            + '</div>';
    }

    modal.innerHTML = '<div style="background:#151c28;border-radius:16px;max-width:700px;width:100%;padding:30px;position:relative;max-height:90vh;overflow-y:auto;">'
        + '<button onclick="document.getElementById(\'costModal\').remove()" style="position:absolute;top:15px;right:15px;background:transparent;border:none;color:#8b949e;font-size:24px;cursor:pointer;">✕</button>'
        + '<h2 style="font-family:Bebas Neue,sans-serif;color:#38bdf8;font-size:28px;margin-bottom:10px;">💰 Maliyet Dökümü</h2>'
        + '<p style="color:#8b949e;margin-bottom:20px;">Stil: ' + data.style + ' | Alan: ' + data.room_area + ' m²</p>'
        + '<div style="background:#0d1117;border-radius:12px;padding:20px;margin-bottom:20px;">' + itemsHTML + '</div>'
        + '<div style="background:#0d1117;border-radius:12px;padding:20px;margin-bottom:20px;">'
        + '<div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:#8b949e;">Ara Toplam</span><span style="color:#f0f6fc;">' + money(data.subtotal) + '</span></div>'
        + '<div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:#8b949e;">KDV (%20)</span><span style="color:#f0f6fc;">' + money(data.vat) + '</span></div>'
        + '<div style="display:flex;justify-content:space-between;padding:15px 0 0;border-top:2px solid #38bdf8;margin-top:10px;"><span style="font-weight:700;color:#f0f6fc;">TOPLAM</span><span style="font-family:Bebas Neue,sans-serif;font-size:28px;color:#ffb700;">' + money(data.total) + '</span></div>'
        + '</div>'
        + '<a href="https://kainta.com" target="_blank" rel="noopener" style="display:block;text-align:center;background:#10b981;color:white;padding:15px;border-radius:8px;text-decoration:none;font-family:Bebas Neue,sans-serif;font-size:22px;">🏗️ Kainta İnşaat\'tan Net Teklif Al →</a>'
        + '<p style="color:#8b949e;font-size:11px;text-align:center;margin-top:15px;">* Bu tahmin referans amaçlıdır.</p>'
        + '</div>';
}
