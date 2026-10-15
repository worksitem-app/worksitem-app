<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Bağlantı yok | Worksitem</title>
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:Inter,-apple-system,'Segoe UI',sans-serif;background:#F7F7F8;color:#101318;padding:20px;box-sizing:border-box}
        .c{max-width:420px;background:#fff;border:1px solid #E3E6EA;border-radius:20px;padding:28px;text-align:center}
        img{width:64px;height:64px;border-radius:14px}
        h1{font-size:22px;margin:14px 0 8px}
        p{color:#5B6472;line-height:1.6;margin:0 0 18px}
        button{background:#FFB800;color:#101318;border:0;border-radius:12px;font-weight:800;font-size:15px;padding:12px 20px;cursor:pointer}
        @media (prefers-color-scheme:dark){body{background:#0B0F16;color:#E6E9EE}.c{background:#141A24;border-color:#232C3A}p{color:#C9CFD8}}
    </style>
</head>
<body>
<div class="c">
    <img src="/icons/icon-192.png" alt="Worksitem">
    <h1>İnternet bağlantısı yok</h1>
    <p>Fiyatlar her zaman güncel gösterildiği için hesaplamalar internet gerektirir. Bağlantınız gelince kaldığınız yerden devam edebilirsiniz.</p>
    <button type="button" onclick="location.reload()">Tekrar dene</button>
</div>
<script>addEventListener('online',()=>location.reload())</script>
</body>
</html>
