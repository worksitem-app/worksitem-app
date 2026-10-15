<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <div><small style="color:var(--muted)">Firma paneli</small><h1 style="margin:2px 0 0;font-size:24px">{{ $firm->name }}</h1></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a class="fm-btn o" href="{{ route('market.panel') }}">İşler</a>
        <form method="POST" action="{{ route('market.panel.logout') }}">@csrf<button class="fm-btn o" type="submit">Çıkış</button></form>
    </div>
</div>
@if (session('panel_ok'))<div class="mk-ok" role="status">{{ session('panel_ok') }}</div>@endif
@if ($errors->any())<div class="mk-err" role="alert">{{ $errors->first() }}</div>@endif
