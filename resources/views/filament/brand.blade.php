<div style="display:flex;align-items:center;gap:10px">
    <span style="display:inline-flex;align-items:center;background:#fff;border-radius:10px;padding:4px 8px"><img src="{{ asset('brand/worksitem-logo.png') }}" alt="Worksitem" style="height:1.6rem;width:auto"></span>
    @if (app()->environment('staging'))
        <span style="font-size:11px;font-weight:700;letter-spacing:.02em;padding:3px 8px;border-radius:999px;background:#FFF8E1;color:#8A6200;border:1px solid #FDE68A">Staging</span>
    @endif
</div>
