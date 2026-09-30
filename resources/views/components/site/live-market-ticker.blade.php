@php
    $liveMarket = app(
        \App\Services\Pricing\LiveMarketTickerService::class
    )->data();

    $liveItems = $liveMarket['items'] ?? [];

    $displayUnit = static function (?string $unit): string {
        $raw = trim((string) $unit);
        $u = mb_strtolower($raw, 'UTF-8');

        return match ($u) {
            'sa', 'saat' => 'TL/saat',

            'm2', 'm²',
            'tl/m2', 'tl/m²' => 'TL/m²',

            'm3', 'm³',
            'tl/m3', 'tl/m³' => 'TL/m³',

            'kg', 'tl/kg' => 'TL/kg',
            'ton', 'tl/ton' => 'TL/ton',

            'ad', 'adet',
            'tl/ad', 'tl/adet' => 'TL/adet',

            default => str_starts_with($u, 'tl/')
                ? $raw
                : 'TL/'.$raw,
        };
    };
@endphp

@if(count($liveItems))

<style>
#worksitem-live-market{
    position:relative;
    width:100%;
    overflow:hidden;
    background:#0f172a;
    color:#f8fafc;
    border-top:1px solid rgba(148,163,184,.18);
    border-bottom:1px solid rgba(148,163,184,.18);
}

.wi-market-shell{
    display:flex;
    align-items:center;
    min-height:48px;
    width:100%;
}

.wi-market-title{
    position:relative;
    z-index:3;
    flex:0 0 auto;
    display:flex;
    align-items:center;
    min-height:48px;
    padding:0 18px;
    background:#0f172a;
    font-size:12px;
    font-weight:900;
    letter-spacing:.045em;
    white-space:nowrap;
    box-shadow:12px 0 18px rgba(15,23,42,.92);
}

.wi-market-dot{
    display:inline-block;
    width:7px;
    height:7px;
    margin-right:8px;
    border-radius:50%;
    background:#22c55e;
    box-shadow:0 0 8px rgba(34,197,94,.85);
}

.wi-market-window{
    position:relative;
    flex:1 1 auto;
    min-width:0;
    overflow:hidden;
}

.wi-market-window::before,
.wi-market-window::after{
    content:"";
    position:absolute;
    z-index:2;
    top:0;
    bottom:0;
    width:35px;
    pointer-events:none;
}

.wi-market-window::before{
    left:0;
    background:linear-gradient(
        90deg,
        #0f172a,
        rgba(15,23,42,0)
    );
}

.wi-market-window::after{
    right:0;
    background:linear-gradient(
        270deg,
        #0f172a,
        rgba(15,23,42,0)
    );
}

.wi-market-track{
    display:flex;
    width:max-content;
    will-change:transform;
    animation:
        worksitem-market-scroll
        38s
        linear
        infinite;
}

.wi-market-set{
    display:flex;
    align-items:center;
    flex-shrink:0;
    gap:0;
}

.wi-market-item{
    position:relative;
    flex:0 0 auto;
    padding:0 23px;
    font-size:13px;
    line-height:48px;
    white-space:nowrap;
    color:#e2e8f0;
}

.wi-market-item::after{
    content:"";
    position:absolute;
    top:50%;
    right:0;
    width:3px;
    height:3px;
    border-radius:50%;
    background:#64748b;
    transform:translateY(-50%);
}

.wi-market-item strong{
    color:#fff;
    font-weight:800;
}

.wi-market-price{
    color:#fbbf24;
    font-weight:900;
}

.wi-market-track:hover{
    animation-play-state:paused;
}

@keyframes worksitem-market-scroll{
    from{
        transform:translate3d(0,0,0);
    }
    to{
        transform:translate3d(-50%,0,0);
    }
}

@media (max-width:700px){
    .wi-market-title{
        padding:0 12px;
        font-size:11px;
    }

    .wi-market-item{
        padding:0 18px;
        font-size:12px;
    }

    .wi-market-track{
        animation-duration:30s;
    }
}

@media (prefers-reduced-motion:reduce){
    .wi-market-window{
        overflow-x:auto;
    }

    .wi-market-track{
        animation:none;
    }

    .wi-market-set:nth-child(2){
        display:none;
    }
}
</style>

<section
    id="worksitem-live-market"
    aria-label="WORKSITEM canlı piyasa fiyatları"
>
    <div class="wi-market-shell">

        <div class="wi-market-title">
            <span class="wi-market-dot"></span>
            CANLI PİYASA
        </div>

        <div class="wi-market-window">

            <div class="wi-market-track">

                @for($copy = 0; $copy < 2; $copy++)

                    <div
                        class="wi-market-set"
                        @if($copy === 1)
                            aria-hidden="true"
                        @endif
                    >

                        @foreach($liveItems as $item)

                            <div class="wi-market-item">

                                @if($item['type'] === 'labor')

                                    <strong>
                                        {{ $item['label'] }}
                                    </strong>

                                    <span> · </span>

                                    <span class="wi-market-price">
                                        {{ number_format(
                                            $item['price'],
                                            2,
                                            ',',
                                            '.'
                                        ) }}
                                        {{ $displayUnit($item['unit'] ?? '') }}
                                    </span>

                                @else

                                    <strong>
                                        {{ $item['label'] }}
                                    </strong>

                                    <span> · </span>

                                    <span class="wi-market-price">
                                        {{ number_format(
                                            $item['price'],
                                            2,
                                            ',',
                                            '.'
                                        ) }}
                                        {{ $displayUnit($item['unit'] ?? '') }}
                                    </span>

                                @endif

                            </div>

                        @endforeach

                    </div>

                @endfor

            </div>

        </div>

    </div>
</section>
@endif
