@php
    $liveMarket = app(
        \App\Services\Pricing\LiveMarketTickerService::class
    )->data();

    $liveItems = $liveMarket['items'] ?? [];
@endphp

@if(count($liveItems))
<section
    id="worksitem-live-market"
    aria-label="WORKSITEM canlı piyasa"
    style="
        border-top:1px solid rgba(148,163,184,.20);
        border-bottom:1px solid rgba(148,163,184,.20);
        background:rgba(15,23,42,.97);
        color:#f8fafc;
    "
>
    <div
        style="
            max-width:1440px;
            margin:0 auto;
            padding:10px 16px;
            display:flex;
            gap:14px;
            align-items:center;
        "
    >
        <div
            style="
                flex:0 0 auto;
                font-size:12px;
                font-weight:800;
                letter-spacing:.04em;
                white-space:nowrap;
            "
        >
            ● CANLI PİYASA
        </div>

        <div
            style="
                display:flex;
                gap:9px;
                overflow-x:auto;
                scrollbar-width:none;
                padding:1px 0;
            "
        >
            @foreach($liveItems as $item)

                <div
                    style="
                        flex:0 0 auto;
                        padding:7px 11px;
                        border:1px solid rgba(148,163,184,.22);
                        border-radius:999px;
                        background:rgba(255,255,255,.05);
                        white-space:nowrap;
                        font-size:12px;
                    "
                >
                    @if($item['type'] === 'labor')
                        <strong>İşçilik</strong>
                        ·
                        {{ $item['poz_no'] }}
                        ·
                        {{ number_format($item['base_price'], 2, ',', '.') }}
                        →
                        <strong>
                            {{ number_format($item['price'], 2, ',', '.') }}
                            TL/{{ match(mb_strtolower(trim($item['unit'] ?? ''), 'UTF-8')) {
                                'sa', 'saat' => 'saat',
                                'm2', 'm²' => 'm²',
                                'm3', 'm³' => 'm³',
                                'kg' => 'kg',
                                'ton' => 'ton',
                                'ad', 'adet' => 'adet',
                                default => $item['unit'] ?? 'birim'
                            } }}
                        </strong>
                        <span style="opacity:.65">
                            (poz ×1,60)
                        </span>
                    @else
                        <strong>{{ $item['label'] }}</strong>
                        ·
                        {{ number_format($item['price'], 2, ',', '.') }}
                        {{ $item['unit'] }}
                        @if($item['source_date'])
                            <span style="opacity:.65">
                                · {{ $item['source_date'] }}
                            </span>
                        @endif
                    @endif
                </div>

            @endforeach
        </div>

        <div
            style="
                margin-left:auto;
                flex:0 0 auto;
                font-size:10px;
                opacity:.60;
                white-space:nowrap;
            "
        >
            Referans fiyat
        </div>
    </div>
</section>
@endif
