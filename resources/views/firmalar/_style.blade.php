<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    :root { --ink:#101318; --y:#FFB800; --y-50:#FFF8E1; --muted:#5B6472; --line:#E3E6EA; --bg:#F4F5F7; --paper:#fff; }
    html[data-theme="dark"] { --bg:#0B0F17; --paper:#141A24; --line:#232C3A; --muted:#97A1AF; --y-50:#2A2410; }
    * { box-sizing:border-box; }
    body { margin:0; font-family:Inter,-apple-system,'Segoe UI',sans-serif; background:var(--bg); color:var(--ink); -webkit-font-smoothing:antialiased; }
    html[data-theme="dark"] body { color:#E6E9EE; }
    .fm-hero { background:var(--ink); color:#fff; }
    .fm-in { max-width:1120px; margin:0 auto; padding:0 20px; }
    .fm-hero .fm-in { padding-top:44px; padding-bottom:40px; }
    .fm-chip { display:inline-block; background:var(--y); color:var(--ink); font-size:12px; font-weight:800; padding:4px 10px; border-radius:999px; text-transform:uppercase; letter-spacing:.04em; }
    .fm-hero h1 { font-size:clamp(28px,4.2vw,44px); line-height:1.12; letter-spacing:-.025em; margin:14px 0 12px; font-weight:900; max-width:820px; }
    .fm-hero p { color:#C9CED6; font-size:17px; line-height:1.6; margin:0 0 20px; max-width:760px; }
    .fm-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; background:var(--y); color:var(--ink); padding:14px 22px; border-radius:12px; text-decoration:none; border:0; cursor:pointer; font:800 15px Inter,sans-serif; }
    .fm-btn.o { background:transparent; border:1.5px solid var(--line); color:inherit; }
    section.fm { padding:40px 0 8px; }
    section.fm h2 { font-size:clamp(22px,2.8vw,30px); letter-spacing:-.02em; margin:0 0 8px; }
    p.lead { color:var(--muted); margin:0 0 22px; font-size:16px; line-height:1.6; max-width:760px; }
    .fm-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
    .fm-card { background:var(--paper); border:1px solid var(--line); border-radius:16px; padding:20px; }
    .fm-card h3 { margin:6px 0 6px; font-size:17px; }
    .fm-card p { margin:0; color:var(--muted); font-size:14px; line-height:1.6; }
    .fm-card .n { width:34px; height:34px; border-radius:10px; background:var(--y-50); color:#8A6200; display:flex; align-items:center; justify-content:center; font-weight:900; }
    .fm-form { background:var(--paper); border:1px solid var(--line); border-radius:18px; padding:24px; display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .fm-form label.f { display:block; font-size:13px; font-weight:700; margin-bottom:6px; }
    .fm-form input[type=text], .fm-form input[type=tel], .fm-form input[type=email], .fm-form input[type=url], .fm-form input[type=number], .fm-form select, .fm-form textarea { width:100%; padding:12px; border:1.5px solid var(--line); border-radius:10px; font:500 15px Inter,sans-serif; background:var(--paper); color:inherit; }
    .fm-form .full { grid-column:1/-1; }
    .fm-form .row { display:grid; grid-template-columns:1fr auto; gap:8px; }
    .fm-pills { display:flex; flex-wrap:wrap; gap:8px; }
    .fm-pills label { display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border:1.5px solid var(--line); border-radius:999px; font-size:13.5px; font-weight:600; cursor:pointer; background:var(--paper); }
    .fm-pills input { accent-color:#101318; }
    .fm-pills label:has(input:checked) { border-color:var(--y); background:var(--y-50); }
    .fm-cities { max-height:180px; overflow:auto; border:1.5px solid var(--line); border-radius:12px; padding:10px; }
    .fm-ok { background:#DCFCE7; color:#14532D; border:1px solid #86EFAC; padding:14px 16px; border-radius:12px; font-weight:700; margin-bottom:14px; line-height:1.55; }
    .fm-err { color:#B91C1C; font-size:13px; margin-top:4px; }
    .fm-msg { font-size:13px; margin-top:6px; min-height:1em; }
    .fm-msg.ok { color:#15803D; } .fm-msg.err { color:#B91C1C; }
    .fm-check { display:flex; gap:10px; align-items:flex-start; font-size:13px; color:var(--muted); line-height:1.55; }
    .fm-check input { width:18px; height:18px; flex:0 0 18px; }
    .fm-check a { color:inherit; font-weight:700; }
    .fm-list { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
    .fm-firm { background:var(--paper); border:1px solid var(--line); border-radius:16px; padding:18px; text-decoration:none; color:inherit; display:block; }
    .fm-firm.pro { border-color:var(--y); box-shadow:0 0 0 2px rgba(255,184,0,.25); }
    .fm-firm small { color:var(--muted); font-size:13px; }
    .fm-badge { display:inline-block; font-size:11px; font-weight:800; padding:3px 8px; border-radius:999px; background:var(--y); color:#101318; margin-left:6px; vertical-align:middle; }
    .fm-badge.v { background:#DCFCE7; color:#166534; }
    .fm-tags { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
    .fm-tags span { font-size:12px; font-weight:700; background:var(--y-50); color:#5B4A12; border-radius:999px; padding:3px 9px; }
    .fm-filter { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
    .fm-filter select { padding:10px 12px; border:1.5px solid var(--line); border-radius:10px; font:600 14px Inter,sans-serif; background:var(--paper); color:inherit; }
    @media (max-width: 860px) { .fm-grid, .fm-list, .fm-form { grid-template-columns:1fr; } }
</style>
