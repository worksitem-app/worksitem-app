{{-- AI kullanım hakkı: telefonla giriş ve kredi penceresi (InjectAiGate ekler) --}}
@php($wsagCfg = [
    'endpoints' => array_values((array) config('worksitem_ai_credits.endpoints')),
    'statusUrl' => route('ai_account.status'),
    'codeUrl' => route('ai_account.code'),
    'loginUrl' => route('ai_account.login'),
    'logoutUrl' => route('ai_account.logout'),
    'kvkkUrl' => url('/legal/kvkk-aydinlatma'),
    'freePerDay' => (int) config('worksitem_ai_credits.free_per_day', 1),
    'packages' => config('worksitem_ai_credits.packages'),
    'whatsapp' => config('worksitem_inspection.whatsapp'),
    'payUrl' => app(\App\Services\Payments\OnlinePayment::class)->enabled() ? route('odeme.kredi') : null,
    'csrf' => csrf_token(),
])
<script type="application/json" id="wsag-config">{!! json_encode($wsagCfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<style>
    .wsag-back { position: fixed; inset: 0; background: rgba(11,15,23,.55); z-index: 2147483000; display: flex; align-items: center; justify-content: center; padding: 16px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .wsag-back[hidden] { display: none; }
    .wsag { --ink:#101318; --muted:#5B6472; --line:#E3E6EA; --paper:#fff; --y:#FFB800; --y50:#FFF8E1; background: var(--paper); color: var(--ink); width: 100%; max-width: 420px; border-radius: 20px; padding: 24px; box-shadow: 0 24px 60px rgba(0,0,0,.25); position: relative; max-height: calc(100vh - 32px); overflow: auto; }
    html[data-theme="dark"] .wsag { --ink:#E6E9EE; --muted:#97A1AF; --line:#2A3342; --paper:#141A24; --y50:#2A2410; }
    .wsag * { box-sizing: border-box; }
    .wsag-x { position: absolute; top: 12px; right: 12px; border: 0; background: transparent; font-size: 22px; line-height: 1; color: var(--muted); cursor: pointer; padding: 6px; }
    .wsag-tag { display: inline-block; background: var(--y); color: #101318; font-weight: 800; font-size: 12px; padding: 4px 10px; border-radius: 999px; }
    .wsag h2 { font-size: 21px; margin: 12px 0 6px; letter-spacing: -.01em; }
    .wsag p { color: var(--muted); font-size: 14.5px; line-height: 1.55; margin: 0 0 12px; }
    .wsag label.f { display: block; font-weight: 700; font-size: 13.5px; margin: 12px 0 6px; }
    .wsag input[type=tel], .wsag input[type=text] { width: 100%; padding: 12px 14px; border: 1.5px solid var(--line); border-radius: 12px; font: inherit; font-size: 16px; background: var(--paper); color: inherit; }
    .wsag input:focus { outline: none; border-color: var(--y); box-shadow: 0 0 0 3px rgba(255,184,0,.25); }
    .wsag-row { display: grid; grid-template-columns: 1fr auto; gap: 8px; }
    .wsag-btn { display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 12px; padding: 12px 16px; font: inherit; font-weight: 800; cursor: pointer; text-decoration: none; }
    .wsag-btn.y { background: var(--y); color: #101318; width: 100%; margin-top: 14px; font-size: 15.5px; }
    .wsag-btn.o { background: transparent; border: 1.5px solid var(--ink); color: inherit; white-space: nowrap; }
    .wsag-btn:disabled { opacity: .55; cursor: not-allowed; }
    .wsag-chk { display: grid; grid-template-columns: 20px 1fr; gap: 10px; font-size: 13px; line-height: 1.5; margin-top: 12px; color: var(--muted); }
    .wsag-chk input { width: 18px; height: 18px; margin-top: 1px; accent-color: #101318; }
    .wsag-chk a { color: inherit; font-weight: 700; }
    .wsag-msg { font-size: 13px; margin-top: 8px; min-height: 1em; }
    .wsag-msg.ok { color: #16A34A; } .wsag-msg.err { color: #B42318; }
    .wsag-pk { display: grid; gap: 8px; margin: 6px 0 4px; }
    .wsag-pk div { display: flex; justify-content: space-between; align-items: center; border: 1.5px solid var(--line); border-radius: 12px; padding: 11px 14px; font-weight: 700; }
    .wsag-pk div.rec { border-color: var(--y); background: var(--y50); }
    .wsag-pk button { display: flex; width: 100%; justify-content: space-between; align-items: center; border: 1.5px solid var(--line); border-radius: 12px; padding: 11px 14px; font: inherit; font-weight: 700; background: var(--paper); color: inherit; cursor: pointer; }
    .wsag-pk button.rec { border-color: var(--y); background: var(--y50); }
    .wsag-pk button:hover { border-color: var(--ink); }
    .wsag-pk small { font-weight: 800; font-size: 11px; background: var(--y); color: #101318; border-radius: 999px; padding: 2px 8px; margin-left: 6px; }
    .wsag-note { font-size: 12.5px; color: var(--muted); margin-top: 10px; line-height: 1.5; }
    .wsag-note a, .wsag-link { color: inherit; }
    .wsag-link { background: none; border: 0; padding: 0; font: inherit; text-decoration: underline; cursor: pointer; }
    .wsag-toast { position: fixed; left: 50%; bottom: 20px; transform: translateX(-50%); background: #101318; color: #fff; padding: 12px 18px; border-radius: 12px; font: 600 14px/1.4 'Inter', sans-serif; z-index: 2147483001; box-shadow: 0 12px 30px rgba(0,0,0,.25); max-width: calc(100vw - 32px); text-align: center; }
    .wsag-toast b { color: #FFB800; }
</style>
<div class="wsag-back" id="wsagBack" hidden>
    <div class="wsag" role="dialog" aria-modal="true" aria-labelledby="wsagTitle" id="wsagBox"></div>
</div>
<script data-asset="ws-ai-gate.js">{!! \App\Support\Asset::inline('js/ws-ai-gate.js') !!}</script>
