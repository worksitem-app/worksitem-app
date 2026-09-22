// ============================================================
// AI Maliyet Modal
// ============================================================

let currentGenerationId = window.currentGenerationId || null;

function openCostModal() {
    const area = prompt("Oda alanını girin (m²):", "25");
    if (!area || parseFloat(area) <= 0) return;

    if (!window.currentGenerationId) {
        alert("Tasarım ID bulunamadı. Lütfen tasarım tamamlanmasını bekleyin.");
        return;
    }

    const modal = document.createElement("div");
    modal.id = "costModal";
    modal.style.cssText = "position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.85);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto;";
    modal.innerHTML = '<div style="background:var(--card-bg);border-radius:16px;max-width:600px;width:100%;padding:30px;text-align:center;"><div style="font-size:48px;margin-bottom:20px;">⏳</div><h2 style="font-family:Bebas Neue;color:var(--cyan);font-size:28px;">Maliyet Hesaplanıyor...</h2></div>';
    document.body.appendChild(modal);

    fetch("/ai-tasarim/ic-mekan/estimate/" + window.currentGenerationId, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
            "Accept": "application/json",
        },
        body: JSON.stringify({ room_area: parseFloat(area) }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === "success") {
            renderCostModal(data);
        } else {
            alert("Hata: " + (data.message || "Bilinmeyen hata"));
            modal.remove();
        }
    })
    .catch(err => {
        alert("Bağlantı hatası: " + err.message);
        modal.remove();
    });
}

function renderCostModal(data) {
    const modal = document.getElementById("costModal");
    if (!modal) return;

    const money = (n) => new Intl.NumberFormat("tr-TR", { maximumFractionDigits: 0 }).format(n) + " TL";

    let itemsHTML = "";
    data.items.forEach(item => {
        itemsHTML += '<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);"><div><strong style="color:var(--text);">' + item.label + '</strong><br><span style="color:var(--muted);font-size:12px;">' + item.quantity + " " + item.unit + " × " + money(item.price) + '</span></div><div style="color:var(--cyan);font-weight:700;">' + money(item.total) + '</div></div>';
    });

    modal.innerHTML = '<div style="background:var(--card-bg);border-radius:16px;max-width:700px;width:100%;padding:30px;position:relative;max-height:90vh;overflow-y:auto;">'
        + '<button onclick="document.getElementById(\'costModal\').remove()" style="position:absolute;top:15px;right:15px;background:transparent;border:none;color:var(--muted);font-size:24px;cursor:pointer;">✕</button>'
        + '<h2 style="font-family:Bebas Neue;color:var(--cyan);font-size:28px;margin-bottom:10px;">💰 Maliyet Dökümü</h2>'
        + '<p style="color:var(--muted);margin-bottom:20px;">Stil: ' + data.style + ' | Alan: ' + data.room_area + ' m²</p>'
        + '<div style="background:var(--input-bg);border-radius:12px;padding:20px;margin-bottom:20px;">' + itemsHTML + '</div>'
        + '<div style="background:var(--input-bg);border-radius:12px;padding:20px;margin-bottom:20px;">'
        + '<div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--muted);">Ara Toplam</span><span style="color:var(--text);">' + money(data.subtotal) + '</span></div>'
        + '<div style="display:flex;justify-content:space-between;padding:5px 0;"><span style="color:var(--muted);">KDV (%20)</span><span style="color:var(--text);">' + money(data.vat) + '</span></div>'
        + '<div style="display:flex;justify-content:space-between;padding:15px 0 0;border-top:2px solid var(--cyan);margin-top:10px;"><span style="font-weight:700;color:var(--text);">TOPLAM</span><span style="font-family:Bebas Neue;font-size:28px;color:var(--yellow);">' + money(data.total) + '</span></div>'
        + '</div>'
        + '<a href="https://kainta.com" target="_blank" rel="noopener" style="display:block;text-align:center;background:var(--green);color:white;padding:15px;border-radius:8px;text-decoration:none;font-family:Bebas Neue;font-size:22px;">🏗️ Kainta İnşaat\'tan Net Teklif Al →</a>'
        + '<p style="color:var(--muted);font-size:11px;text-align:center;margin-top:15px;">* Bu tahmin referans amaçlıdır.</p>'
        + '</div>';
}
