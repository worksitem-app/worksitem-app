{{-- Worksitem admin teması: koyu kenar menü, sarı vurgu, yumuşak kartlar --}}
<style>
    :root { --ws-ink:#101318; --ws-yellow:#FFB800; }
    .fi-body { background:#F5F6F8; }
    .dark .fi-body { background:#0B0E13; }
    .fi-sidebar { background:var(--ws-ink) !important; }
    .fi-sidebar-header { background:var(--ws-ink) !important; box-shadow:none !important; border-bottom:1px solid rgba(255,255,255,.06); }
    .fi-sidebar-header img { filter: drop-shadow(0 0 0 transparent); }
    .fi-sidebar-group-label { color:#8b93a0 !important; font-size:11px; letter-spacing:.06em; text-transform:uppercase; }
    .fi-sidebar-item-label { color:#dfe3e8 !important; }
    .fi-sidebar-item-icon { color:#8b93a0 !important; }
    .fi-sidebar-item a:hover, .fi-sidebar-item button:hover { background:rgba(255,255,255,.06) !important; }
    .fi-sidebar-item-active > a, .fi-sidebar-item-active > button { background:rgba(255,184,0,.14) !important; }
    .fi-sidebar-item-active .fi-sidebar-item-label { color:var(--ws-yellow) !important; }
    .fi-sidebar-item-active .fi-sidebar-item-icon { color:var(--ws-yellow) !important; }
    .fi-sidebar-group-collapse-button .fi-icon-btn-icon, .fi-sidebar-group-button svg { color:#8b93a0 !important; }
    .fi-topbar > nav { box-shadow:none; border-bottom:1px solid #E3E6EA; }
    .fi-section, .fi-wi-stats-overview-stat, .fi-ta-ctn { border-radius:16px !important; }
    .fi-wi-stats-overview-stat { box-shadow:0 1px 2px rgba(16,19,24,.04) !important; }
    .fi-wi-stats-overview-stat-value { letter-spacing:-.02em; }
    .fi-header-heading { letter-spacing:-.02em; }
    .fi-btn-color-primary { color:var(--ws-ink) !important; }

    /* ── Kontrol Merkezi ───────────────────────────── */
    .ws-cc { display:grid; grid-template-columns:minmax(0,1.6fr) minmax(0,1fr); gap:16px; background:var(--ws-ink); color:#fff; border-radius:20px; padding:24px; position:relative; overflow:hidden; }
    .ws-cc:before { content:""; position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px); background-size:28px 28px; pointer-events:none; }
    .ws-cc > * { position:relative; }
    .ws-cc-meta { display:flex; flex-wrap:wrap; gap:8px 14px; font-size:12px; color:#9aa3af; }
    .ws-cc-env { font-weight:800; color:#fff; padding:2px 10px; border-radius:999px; background:rgba(255,255,255,.08); }
    .ws-cc-env--production { color:#4ADE80; } .ws-cc-env--staging { color:var(--ws-yellow); }
    .ws-cc-title { font-size:28px; font-weight:900; letter-spacing:-.02em; margin:10px 0 4px; }
    .ws-cc-sub { color:#C9CED6; font-size:14px; margin:0; }
    .ws-cc-sub b.ok { color:#4ADE80; } .ws-cc-sub b.warn { color:var(--ws-yellow); } .ws-cc-sub b.fail { color:#FB7185; }
    .ws-cc-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:18px; }
    .ws-cc-btn { font-size:13px; font-weight:700; padding:8px 12px; border-radius:10px; background:rgba(255,255,255,.07); color:#E6E9EE; border:1px solid rgba(255,255,255,.1); transition:.15s; }
    .ws-cc-btn:hover { background:rgba(255,255,255,.14); }
    .ws-cc-btn--y { background:var(--ws-yellow); color:var(--ws-ink); border-color:var(--ws-yellow); }
    .ws-cc-btn--y:hover { background:#FFC933; }
    .ws-cc-alerts { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:14px; display:flex; flex-direction:column; gap:6px; }
    .ws-cc-alerts-h { font-size:11px; letter-spacing:.06em; text-transform:uppercase; color:#9aa3af; font-weight:700; margin-bottom:2px; }
    .ws-cc-alert { display:grid; grid-template-columns:10px 1fr auto; gap:10px; align-items:center; padding:9px 10px; border-radius:10px; font-size:13.5px; color:#E6E9EE; background:rgba(255,255,255,.04); }
    .ws-cc-alert:hover { background:rgba(255,255,255,.1); }
    .ws-cc-alert i { width:8px; height:8px; border-radius:50%; background:#7FB3C8; }
    .ws-cc-alert--warn i { background:var(--ws-yellow); } .ws-cc-alert--fail i { background:#FB7185; }
    .ws-cc-alert em { font-style:normal; color:#9aa3af; }
    .ws-cc-empty { color:#C9CED6; font-size:14px; padding:8px 2px; }
    @media (max-width: 900px) { .ws-cc { grid-template-columns:1fr; padding:18px; } .ws-cc-title { font-size:22px; } }
    .ws-health { display:flex; flex-direction:column; }
    .ws-health-g { font-size:11px; letter-spacing:.06em; text-transform:uppercase; color:#8b93a0; font-weight:700; margin:12px 0 4px; }
    .ws-health-g:first-child { margin-top:0; }
    .ws-health-row { display:grid; grid-template-columns:12px 1fr; gap:10px; align-items:start; padding:7px 0; border-top:1px solid rgba(16,19,24,.06); }
    .dark .ws-health-row { border-color:rgba(255,255,255,.06); }
    .ws-health-row b { display:block; font-size:13.5px; font-weight:600; }
    .ws-health-row small { display:block; font-size:12px; color:#6b7280; line-height:1.4; }
    .ws-dot { width:10px; height:10px; border-radius:50%; margin-top:5px; background:#9CA3AF; }
    .ws-dot--ok { background:#22C55E; } .ws-dot--warn { background:var(--ws-yellow); } .ws-dot--fail { background:#F43F5E; } .ws-dot--off { background:#CBD5E1; }
    .fi-wi-stats-overview-header-heading, .fi-wi-stats-overview > div > h3 { font-size:13px !important; letter-spacing:.06em; text-transform:uppercase; color:#6b7280; }
</style>
