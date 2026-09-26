<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gizlilik Politikası | Worksitem</title>
    <meta name="description" content="Worksitem gizlilik politikası ve veri güvenliği taahhütleri.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/legal/gizlilik-politikasi') }}">
    <style>
        body { margin: 0; background: #f8fafc; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.7; }
        main { max-width: 900px; margin: 40px auto; padding: 32px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        h1, h2 { color: #0f172a; }
        h1 { margin-top: 0; font-size: 2rem; }
        h2 { margin-top: 28px; font-size: 1.2rem; }
        a { color: #2563eb; }
        .version { color: #64748b; font-size: 0.9rem; }
        .nav-links { margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
        .nav-links a { margin-right: 16px; font-weight: 600; }
    </style>
</head>
<body>
<main>
    <h1>Gizlilik Politikası</h1>

    <p class="version">Metin sürümü: 2026-09-v1 · Yürürlük tarihi: 26.09.2026</p>

    <p>
        Worksitem olarak kişisel verilerinizin gizliliğine ve güvenliğine önem veriyoruz.
        Bu politika, verilerinizi nasıl koruduğumuzu ve hangi taahhütleri yerine getirdiğimizi
        açıklar.
    </p>

    <h2>1. Veri Güvenliği Taahhüdümüz</h2>

    <p>Kişisel verilerinizin güvenliği için aşağıdaki teknik ve idari tedbirleri uygularız:</p>

    <ul>
        <li><strong>SSL/TLS şifreleme:</strong> Tüm veri iletimi HTTPS üzerinden şifrelenir.</li>
        <li><strong>Şifre hash'leme:</strong> Kullanıcı şifreleri bcrypt (12 round) ile hash'lenir.</li>
        <li><strong>IP özetleme:</strong> IP adresleri HMAC-SHA256 ile tek yönlü özetlenir.</li>
        <li><strong>CSRF koruması:</strong> Tüm formlar CSRF token ile korunur.</li>
        <li><strong>XSS koruması:</strong> Kullanıcı girdileri HTML-escape edilir.</li>
        <li><strong>SQL injection koruması:</strong> Tüm sorgular parametrik binding kullanır.</li>
        <li><strong>Rate limiting:</strong> Form spam'ine karşı rate limit uygulanır.</li>
        <li><strong>Düzenli yedekleme:</strong> Veriler günlük yedeklenir.</li>
    </ul>

    <h2>2. Verilerinizin Saklanması</h2>

    <p>
        Kişisel verileriniz, Türkiye'de bulunan ve KVKK uyumlu veri merkezlerinde
        (Hostinger EU) saklanır. Yurt dışına aktarım gerektiren durumlarda,
        <a href="/legal/acik-riza">Açık Rıza</a>'nıza dayanarak ve KVKK'nın 9. maddesi
        kapsamında hareket edilir.
    </p>

    <h2>3. Verilerinizin Kimlerle Paylaşıldığı</h2>

    <ul>
        <li>Kainta İnşaat yetkili çalışanları ve satış ekibi</li>
        <li>Bilişim hizmet sağlayıcılarımız (Hostinger, Google)</li>
        <li>Yasal zorunluluk hâlinde yetkili kamu kurumları</li>
    </ul>

    <p>
        Verileriniz hiçbir şekilde <strong>reklam amaçlı üçüncü taraflara satılmaz</strong>.
    </p>

    <h2>4. Veri İhlali Bildirimi</h2>

    <p>
        Bir veri ihlali tespit etmemiz hâlinde, KVKK'nın 12. maddesi ve Kişisel Veri
        İhlali Bildirim Prosedürü kapsamında:
    </p>

    <ul>
        <li><strong>72 saat içinde</strong> KVKK Kurulu'na bildiririz.</li>
        <li>Etkilenen kullanıcılara en kısa sürede bildirim yaparız.</li>
        <li>İhlalin kapsamını ve alınan tedbirleri kamuoyuyla paylaşırız.</li>
    </ul>

    <h2>5. Veri Sahibi Hakları</h2>

    <p>
        KVKK'nın 11. maddesi kapsamındaki haklarınız için
        <a href="/legal/kvkk-basvuru">KVKK Başvuru Formu</a>'nu kullanabilirsiniz.
    </p>

    <h2>6. Politika Güncellemeleri</h2>

    <p>
        Bu gizlilik politikası, yasal mevzuat veya hizmet kapsamındaki değişikliklere
        bağlı olarak güncellenebilir. Önemli değişikliklerde kullanıcılarımıza bildirim
        yapılır.
    </p>

    <h2>7. İletişim</h2>

    <p>
        Gizlilik ile ilgili soru ve talepleriniz için:
        <a href="mailto:kvkk@kainta.com">kvkk@kainta.com</a>
    </p>

    <div class="nav-links">
        <a href="/legal/kvkk-aydinlatma">Aydınlatma Metni</a>
        <a href="/legal/acik-riza">Açık Rıza Metni</a>
        <a href="/legal/cerez-politikasi">Çerez Politikası</a>
        <a href="/legal/kvkk-basvuru">Başvuru Formu</a>
    </div>
</main>
</body>
</html>
