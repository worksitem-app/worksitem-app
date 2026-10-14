<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>Boya Hesaplama 2026: Kaç Litre Boya Gider? | WORKSİTEM</title>
    <meta name="description" content="Boya hesaplama aracıyla duvar ve tavan için kaç litre boya gerektiğini; kapı-pencere düşümünü, fireyi, işçilik ve malzeme bütçesini hesaplayın.">

    <link rel="stylesheet" href="/css/worksitem-design.css">

    <style>
        :root{
            --p:#ff6b2c;
            --ink:#111827;
            --muted:#475569;
            --line:#dbe2ea;
            --bg:#f7f8fa;
            --card:#fff;
            --good:#067647;
            --warn:#93370d;
        }

        *{box-sizing:border-box}

        body{
            margin:0;
            background:var(--bg);
            color:var(--ink);
            font-family:Inter,system-ui,-apple-system,sans-serif;
        }

        .paint-wrap{
            max-width:1180px;
            margin:0 auto;
            padding:36px 18px 96px;
        }

        .paint-hero{
            max-width:820px;
            margin:0 auto 28px;
            text-align:center;
        }

        .paint-hero h1{
            margin:0 0 12px;
            font-size:clamp(30px,5vw,48px);
            line-height:1.06;
            letter-spacing:-.03em;
        }

        .paint-hero p{
            margin:0;
            color:var(--muted);
            font-size:17px;
            line-height:1.65;
        }

        .qa-chip{
            display:inline-flex;
            margin-bottom:14px;
            padding:6px 10px;
            border:1px solid #fed7aa;
            border-radius:999px;
            background:#fff7ed;
            color:#c2410c;
            font-size:12px;
            font-weight:800;
        }

        .layout{
            display:grid;
            grid-template-columns:minmax(0,1.25fr) minmax(310px,.75fr);
            gap:22px;
            align-items:start;
        }

        .panel{
            background:var(--card);
            border:1px solid var(--line);
            border-radius:20px;
            padding:22px;
            box-shadow:0 8px 24px rgba(15,23,42,.04);
        }

        .panel h2{
            margin:0 0 18px;
            font-size:20px;
        }

        .tabs{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:8px;
            margin-bottom:20px;
            padding:5px;
            background:#eef2f6;
            border-radius:14px;
        }

        .tab{
            min-height:48px;
            border:0;
            border-radius:10px;
            padding:10px;
            background:transparent;
            color:#475569;
            font-weight:800;
            cursor:pointer;
        }

        .tab.active{
            background:#fff;
            color:#111827;
            box-shadow:0 2px 8px rgba(15,23,42,.08);
        }

        .grid2{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:14px;
        }

        .field{
            margin-bottom:15px;
        }

        .field label{
            display:block;
            margin-bottom:7px;
            font-size:13px;
            font-weight:800;
        }

        .field small,
        .detail-help{
            display:block;
            margin-top:6px;
            color:#475569;
            font-size:12px;
            line-height:1.5;
        }

        input,
        select{
            width:100%;
            min-height:48px;
            padding:10px 12px;
            border:1px solid #cbd5e1;
            border-radius:11px;
            background:#fff;
            color:#111827;
            font:inherit;
        }

        input:focus,
        select:focus,
        button:focus-visible,
        summary:focus-visible,
        a:focus-visible{
            outline:3px solid rgba(255,107,44,.25);
            outline-offset:2px;
        }

        .section-title{
            margin:6px 0 14px;
            padding-top:18px;
            border-top:1px solid var(--line);
            color:#475569;
            font-size:12px;
            font-weight:900;
            letter-spacing:.07em;
            text-transform:uppercase;
        }

        /* STEPPER */

        .stepper-card{
            border:1px solid var(--line);
            border-radius:14px;
            background:#fafbfc;
            padding:13px;
        }

        .stepper-card > label{
            display:block;
            margin-bottom:9px;
            font-size:13px;
            font-weight:800;
        }

        .stepper{
            display:grid;
            grid-template-columns:48px 1fr 48px;
            align-items:center;
            gap:8px;
        }

        .stepper button{
            width:48px;
            height:48px;
            border:1px solid #cbd5e1;
            border-radius:11px;
            background:#fff;
            color:#111827;
            font-size:24px;
            line-height:1;
            font-weight:700;
            cursor:pointer;
        }

        .stepper button:hover{
            border-color:var(--p);
            background:#fff7ed;
        }

        .stepper input{
            min-height:48px;
            padding:0;
            text-align:center;
            font-size:18px;
            font-weight:900;
            background:#fff;
        }

        /* DETAILS */

        .mini-details,
        .advanced-settings{
            margin:14px 0;
            border:1px solid var(--line);
            border-radius:13px;
            background:#fafbfc;
            overflow:hidden;
        }

        .mini-details summary,
        .advanced-settings summary{
            display:flex;
            align-items:center;
            justify-content:space-between;
            min-height:50px;
            padding:12px 14px;
            cursor:pointer;
            list-style:none;
            color:#1e293b;
            font-size:13px;
            font-weight:900;
        }

        .mini-details summary::-webkit-details-marker,
        .advanced-settings summary::-webkit-details-marker{
            display:none;
        }

        .mini-details summary::after,
        .advanced-settings summary::after{
            content:"⌄";
            color:#475569;
            font-size:21px;
            line-height:1;
            transition:transform .2s ease;
        }

        .mini-details[open] summary::after,
        .advanced-settings[open] summary::after{
            transform:rotate(180deg);
        }

        .details-body{
            padding:4px 14px 15px;
            border-top:1px solid #e9edf2;
        }

        /* CHECKBOX */

        .check-row{
            display:flex;
            align-items:center;
            gap:12px;
            min-height:56px;
            padding:12px 13px;
            border:1px solid var(--line);
            border-radius:12px;
            background:#fff;
            cursor:pointer;
        }

        .check-row input{
            flex:0 0 20px;
            width:20px;
            height:20px;
            min-height:20px;
            margin:0;
        }

        .check-row small{
            color:#475569;
            line-height:1.45;
        }

        .btn-calc{
            width:100%;
            min-height:52px;
            border:0;
            border-radius:13px;
            padding:15px 18px;
            background:var(--p);
            color:#fff;
            font-size:15px;
            font-weight:900;
            cursor:pointer;
        }

        .btn-calc:hover{
            filter:brightness(.96);
        }

        .error{
            display:none;
            margin:0 0 14px;
            padding:12px;
            border:1px solid #fecaca;
            border-radius:10px;
            background:#fef2f2;
            color:#b42318;
            font-size:14px;
            line-height:1.5;
        }

        .hidden{display:none!important}

        /* RESULT */

        .result-panel{
            position:sticky;
            top:18px;
        }

        .result-empty{
            padding:22px 0;
            color:#475569;
            font-size:14px;
            line-height:1.6;
        }

        .result-live{
            display:none;
        }

        .metric-main{
            margin-bottom:12px;
            padding:20px;
            border-radius:17px;
            background:#111827;
            color:#fff;
        }

        .metric-main > span{
            display:block;
            font-size:11px;
            font-weight:800;
            letter-spacing:.05em;
            text-transform:uppercase;
            opacity:.76;
        }

        .metric-main strong{
            display:block;
            margin-top:7px;
            font-size:30px;
            line-height:1.1;
        }

        .metric-main small{
            display:block;
            margin-top:7px;
            color:#d1d5db;
            font-size:12px;
            line-height:1.45;
        }

        /* PRIMARY CONVERSION CTA */

        .result-cta{
            margin:0 0 16px;
            padding:15px;
            border:1px solid #fed7aa;
            border-radius:14px;
            background:#fff7ed;
        }

        .result-cta strong{
            display:block;
            margin-bottom:4px;
            font-size:14px;
        }

        .result-cta p{
            margin:0 0 11px;
            color:#7c2d12;
            font-size:12px;
            line-height:1.5;
        }

        .result-cta a{
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:48px;
            border-radius:11px;
            background:var(--p);
            color:#fff;
            text-decoration:none;
            font-size:13px;
            font-weight:900;
        }

        .metrics{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:10px;
        }

        .metric{
            min-height:86px;
            padding:13px;
            border:1px solid var(--line);
            border-radius:13px;
            background:#fff;
        }

        .metric span{
            display:block;
            margin-bottom:6px;
            color:#475569;
            font-size:12px;
        }

        .metric strong{
            font-size:17px;
            line-height:1.35;
        }

        .metric.full{
            grid-column:1/-1;
            min-height:auto;
        }

        .status{
            margin-top:14px;
            padding:13px;
            border-radius:13px;
            font-size:13px;
            line-height:1.55;
        }

        .status.warn{
            border:1px solid #fedf89;
            background:#fffaeb;
            color:#7a2e0e;
        }

        .status.good{
            border:1px solid #abefc6;
            background:#ecfdf3;
            color:#05603a;
        }

        .shopping{
            margin-top:18px;
            padding-top:17px;
            border-top:1px solid var(--line);
        }

        .shopping h3{
            margin:0 0 10px;
            font-size:15px;
        }

        .shop-row{
            display:flex;
            justify-content:space-between;
            gap:12px;
            padding:10px 0;
            border-bottom:1px dashed #dbe2ea;
            font-size:13px;
            line-height:1.45;
        }

        .shop-row span:first-child{
            color:#475569;
        }

        .shop-row span:last-child{
            max-width:60%;
            text-align:right;
            font-weight:800;
        }

        .source{
            margin-top:16px;
            color:#475569;
            font-size:12px;
            line-height:1.6;
        }

        .secondary-cta{
            margin-top:20px;
            padding:17px;
            border-radius:15px;
            background:#f8fafc;
            border:1px solid var(--line);
        }

        .secondary-cta strong{
            display:block;
            margin-bottom:5px;
        }

        .secondary-cta p{
            margin:0 0 11px;
            color:#475569;
            font-size:12px;
            line-height:1.5;
        }

        .secondary-cta a{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:44px;
            padding:10px 14px;
            border-radius:10px;
            background:#111827;
            color:#fff;
            text-decoration:none;
            font-size:12px;
            font-weight:800;
        }

        /* MOBILE STICKY CTA */

        .mobile-sticky-cta{
            display:none;
        }


        .result-more{
            margin-top:14px;
            border:1px solid var(--line);
            border-radius:12px;
            background:#fafbfc;
            overflow:hidden;
        }

        .result-more summary{
            min-height:46px;
            padding:12px 13px;
            cursor:pointer;
            color:#334155;
            font-size:13px;
            font-weight:800;
        }

        .result-more-body{
            padding:0 13px 13px;
        }

        @media(min-width:701px){

            .layout{
                grid-template-columns:
                    minmax(0,1fr)
                    minmax(390px,.90fr);
            }

            .result-panel{
                padding:18px;
            }

            .result-panel h2{
                margin-bottom:12px;
            }

            .metric-main{
                padding:16px;
            }

            .metric-main strong{
                font-size:27px;
            }

            .result-cta{
                padding:12px;
            }

            .metrics{
                gap:8px;
            }

            .metric{
                min-height:72px;
                padding:10px;
            }

            .metric span{
                margin-bottom:4px;
                font-size:11px;
            }

            .metric strong{
                font-size:15px;
            }
        }


        /* BOYA V2 — desktop/right summary final */
        @media(min-width:701px){

            .paint-wrap{
                max-width:1380px;
            }

            .layout{
                display:grid !important;
                grid-template-columns:
                    minmax(0,1.08fr)
                    minmax(350px,.92fr) !important;
                gap:16px !important;
                align-items:start;
            }

            .result-panel{
                position:sticky !important;
                top:12px;
                width:auto !important;
                min-width:0;
                max-height:calc(100vh - 24px);
                overflow-y:auto;
                padding:16px;
            }

            .metric-main{
                margin-bottom:10px;
                padding:14px 16px;
            }

            .metric-main strong{
                font-size:25px;
            }

            .result-cta{
                margin-bottom:10px;
                padding:11px;
            }

            .result-cta p{
                margin-bottom:8px;
            }

            .result-cta a{
                min-height:44px;
            }

            .metrics{
                gap:7px;
            }

            .metric{
                min-height:66px;
                padding:9px 10px;
            }

            .metric span{
                font-size:11px;
            }

            .metric strong{
                font-size:14px;
            }

            .shopping{
                margin-top:10px;
                padding-top:10px;
            }

            .shopping h3{
                margin-bottom:5px;
            }

            .shop-row{
                padding:6px 0;
                font-size:12px;
            }

            .source{
                margin-top:10px;
                font-size:11px;
            }

            .result-more{
                margin-top:9px;
            }

            .result-more > summary{
                display:none;
            }

            .result-more-body{
                padding:0 10px 10px;
            }
        }

        @media(max-width:700px){

            .layout{
                grid-template-columns:1fr;
            }

            .result-panel{
                position:static;
            }

            .grid2{
                grid-template-columns:1fr;
                gap:0;
            }

            .paint-wrap{
                padding-top:22px;
                padding-bottom:115px;
            }

            .paint-hero p{
                font-size:15px;
            }

            .panel{
                padding:17px;
                border-radius:16px;
            }

            .metrics{
                grid-template-columns:1fr 1fr;
            }

            .metric-main strong{
                font-size:27px;
            }

            input,
            select{
                font-size:16px;
            }

            .field small,
            .detail-help{
                font-size:13px;
            }

            .mobile-sticky-cta.active{
                position:fixed;
                z-index:1000;
                left:0;
                right:0;
                bottom:0;
                display:block;
                padding:10px 14px calc(10px + env(safe-area-inset-bottom));
                border-top:1px solid #e5e7eb;
                background:rgba(255,255,255,.97);
                box-shadow:0 -8px 24px rgba(15,23,42,.10);
                backdrop-filter:blur(8px);
            }

            .mobile-sticky-cta a{
                display:flex;
                align-items:center;
                justify-content:center;
                width:100%;
                min-height:50px;
                border-radius:12px;
                background:var(--p);
                color:#fff;
                text-decoration:none;
                font-size:14px;
                font-weight:900;
            }
        }

        @media(max-width:470px){
            .metrics{
                grid-template-columns:1fr;
            }

            .metric.full{
                grid-column:auto;
            }

            .tabs{
                grid-template-columns:1fr;
            }
        }

        @media(prefers-reduced-motion:reduce){
            *{
                scroll-behavior:auto!important;
                transition:none!important;
            }
        }
    
        @media(min-width:701px){

            .layout > .panel:first-child{
                grid-column:1 !important;
                grid-row:1 !important;
                width:100% !important;
                min-width:0 !important;
            }

            .layout > .result-panel{
                grid-column:2 !important;
                grid-row:1 !important;
                width:100% !important;
                min-width:0 !important;
            }

            .result-panel{
                max-height:none !important;
                overflow:visible !important;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BOYA V2 — FINAL DESKTOP TWO COLUMN
        |--------------------------------------------------------------------------
        */

        @media(min-width:1100px){

            .paint-wrap{
                max-width:1480px !important;
                padding-left:18px;
                padding-right:18px;
            }

            .layout{
                display:grid !important;

                grid-template-columns:
                    minmax(0,1fr)
                    minmax(430px,520px) !important;

                gap:18px !important;
                align-items:start !important;
            }

            .layout > .panel:first-child{
                grid-column:1 !important;
                grid-row:1 !important;

                width:100% !important;
                max-width:none !important;
                min-width:0 !important;
            }

            .layout > .result-panel{
                grid-column:2 !important;
                grid-row:1 !important;

                width:100% !important;
                max-width:none !important;
                min-width:0 !important;

                align-self:start !important;

                position:sticky !important;
                top:12px !important;

                max-height:none !important;
                overflow:visible !important;
            }


            /*
            |--------------------------------------------------------------------------
            | COMPACT RESULT
            |--------------------------------------------------------------------------
            */

            .result-panel{
                padding:15px !important;
            }

            .result-panel h2{
                margin:0 0 10px !important;
                font-size:19px;
            }

            .result-panel .metric-main{
                margin-bottom:8px !important;
                padding:13px 14px !important;
                border-radius:14px;
            }

            .result-panel .metric-main strong{
                margin-top:4px;
                font-size:25px !important;
            }

            .result-panel .metric-main small{
                margin-top:4px;
            }


            /*
            |--------------------------------------------------------------------------
            | CTA COMPACT
            |--------------------------------------------------------------------------
            */

            .result-panel .result-cta{
                margin-bottom:8px !important;
                padding:10px !important;
            }

            .result-panel .result-cta strong{
                margin-bottom:2px;
                font-size:13px;
            }

            .result-panel .result-cta p{
                margin:0 0 7px !important;
                font-size:11px;
            }

            .result-panel .result-cta a{
                min-height:42px !important;
            }


            /*
            |--------------------------------------------------------------------------
            | METRICS 2 COLUMN
            |--------------------------------------------------------------------------
            */

            .result-panel .metrics{
                display:grid !important;

                grid-template-columns:
                    repeat(2,minmax(0,1fr)) !important;

                gap:6px !important;
            }

            .result-panel .metric{
                min-height:58px !important;
                padding:8px 10px !important;
            }

            .result-panel .metric span{
                margin-bottom:3px !important;
                font-size:10px !important;
            }

            .result-panel .metric strong{
                font-size:14px !important;
                line-height:1.25;
            }

            .result-panel .metric.full{
                grid-column:1 / -1 !important;
            }


            /*
            |--------------------------------------------------------------------------
            | WARNINGS
            |--------------------------------------------------------------------------
            */

            .result-panel .status{
                margin-top:7px !important;
                padding:8px 10px !important;
                font-size:11px !important;
            }


            /*
            |--------------------------------------------------------------------------
            | MATERIAL LIST — COLLAPSED
            |--------------------------------------------------------------------------
            */

            .result-panel .result-more{
                margin-top:8px !important;
                border-radius:10px;
            }

            .result-panel .result-more > summary{
                display:flex !important;
                align-items:center;
                justify-content:space-between;

                min-height:42px !important;
                padding:9px 11px !important;

                cursor:pointer;

                color:#334155;
                font-size:12px !important;
                font-weight:800;
            }

            .result-panel .result-more > summary::after{
                content:"⌄";
                font-size:18px;
                line-height:1;
            }

            .result-panel .result-more[open] > summary::after{
                content:"⌃";
            }

            .result-panel .result-more:not([open])
            .result-more-body{
                display:none !important;
            }

            .result-panel .result-more[open]
            .result-more-body{
                display:block !important;
            }

            .result-panel .result-more-body{
                padding:0 10px 10px !important;
            }

            .result-panel .shopping{
                margin-top:8px !important;
                padding-top:7px !important;
            }

            .result-panel .shopping h3{
                margin:0 0 5px !important;
                font-size:13px;
            }

            .result-panel .shop-row{
                padding:5px 0 !important;
                font-size:11px !important;
            }

            .result-panel .source{
                margin-top:8px !important;
                font-size:10px !important;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TABLET / MOBILE
        |--------------------------------------------------------------------------
        */

        @media(max-width:1099px){

            .layout{
                display:block !important;
            }

            .result-panel{
                position:static !important;
                width:100% !important;
                margin-top:18px;
            }

            .result-more > summary{
                display:flex !important;
            }
        }

</style>

    <link rel="canonical" href="https://worksitem.com/araclar/boya-hesaplayici">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >

    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <meta
        property="og:image"
        content="https://worksitem.com/brand/og-image.png"
    >

    <meta
        name="twitter:card"
        content="summary_large_image"
    >

    <meta
        name="theme-color"
        content="#ffffff"
    >


<style id="paint-beton-design-v1">

/*
|--------------------------------------------------------------------------
| WORKSITEM DESIGN TOKENS
|--------------------------------------------------------------------------
*/

:root{
    --bg:#f5f6f8 !important;
    --surface:#ffffff !important;
    --surface-2:#f8f9fb !important;

    --text:#101318 !important;
    --ink:#101318 !important;

    --muted:#6b7280 !important;
    --line:#e4e7eb !important;

    --accent:#ffb800 !important;
    --p:#ffb800 !important;

    --control-bg:#ffffff !important;

    --button-bg:#101318 !important;
    --button-text:#ffffff !important;

    --card:#ffffff !important;

    --radius:16px !important;
}


html[data-theme="dark"]{

    --bg:#0f1720 !important;
    --surface:#17212d !important;
    --surface-2:#1c2734 !important;

    --text:#f8fafc !important;
    --ink:#f8fafc !important;

    --muted:#aab4c3 !important;

    --line:rgba(255,255,255,.12) !important;

    --control-bg:#1c2734 !important;

    --button-bg:#f8fafc !important;
    --button-text:#111827 !important;

    --card:#17212d !important;
}


/*
|--------------------------------------------------------------------------
| BASE
|--------------------------------------------------------------------------
*/

body{
    font-family:'Inter',sans-serif !important;
    background:var(--bg) !important;
    color:var(--text) !important;
}


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

.paint-wrap{
    max-width:1280px !important;
    margin:0 auto !important;
    padding:40px 20px 80px !important;
}


.paint-hero{
    max-width:800px !important;
    margin:0 auto 30px !important;
    text-align:center !important;
}


.paint-hero h1{
    margin:0 0 10px !important;

    color:var(--text) !important;

    font-size:28px !important;
    font-weight:900 !important;

    line-height:1.2 !important;
    letter-spacing:-1px !important;
}


.paint-hero p{
    color:var(--muted) !important;

    font-size:15px !important;
    line-height:1.6 !important;
}


/*
|--------------------------------------------------------------------------
| STAGING CHIP
|--------------------------------------------------------------------------
*/

.qa-chip{
    display:none !important;
}


/*
|--------------------------------------------------------------------------
| DESKTOP LAYOUT
|--------------------------------------------------------------------------
*/

.layout{
    display:grid !important;

    grid-template-columns:
        minmax(0,1fr)
        minmax(400px,480px) !important;

    gap:24px !important;

    align-items:start !important;
}


.layout > .panel:first-child{
    grid-column:1 !important;
    grid-row:1 !important;
}


.layout > .result-panel{
    grid-column:2 !important;
    grid-row:1 !important;
}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.panel{
    margin:0 !important;

    padding:30px !important;

    background:var(--surface) !important;

    border:1px solid var(--line) !important;

    border-radius:var(--radius) !important;

    box-shadow:none !important;
}


.panel h2{
    margin:0 0 20px !important;

    color:var(--text) !important;

    font-size:20px !important;
    font-weight:900 !important;
}


/*
|--------------------------------------------------------------------------
| MODE SWITCH
|--------------------------------------------------------------------------
*/

.tabs{
    display:grid !important;

    grid-template-columns:1fr 1fr !important;

    gap:10px !important;

    margin-bottom:20px !important;

    padding:0 !important;

    background:transparent !important;
}


.tab{
    min-height:48px !important;

    padding:0 14px !important;

    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--surface) !important;

    color:var(--text) !important;

    font-weight:800 !important;

    box-shadow:none !important;
}


.tab.active{
    border-color:var(--accent) !important;

    background:rgba(255,184,0,.12) !important;

    color:var(--text) !important;

    box-shadow:none !important;
}


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

.grid2{
    gap:15px !important;
}


.field{
    margin-bottom:20px !important;
}


.field label,
.stepper-card > label{
    display:block !important;

    margin-bottom:8px !important;

    color:var(--text) !important;

    font-size:13px !important;
    font-weight:800 !important;
}


.field small,
.detail-help{
    color:var(--muted) !important;

    font-size:11px !important;
    line-height:1.55 !important;
}


input,
select,
textarea{
    width:100% !important;

    min-height:50px !important;

    padding:0 15px !important;

    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--control-bg) !important;

    color:var(--text) !important;

    font-family:inherit !important;
    font-size:15px !important;
}


/*
|--------------------------------------------------------------------------
| SECTION LABEL
|--------------------------------------------------------------------------
*/

.section-title{
    margin:8px 0 14px !important;

    padding-top:18px !important;

    border-top:1px solid var(--line) !important;

    color:var(--muted) !important;

    font-size:12px !important;
    font-weight:900 !important;
    letter-spacing:.07em !important;
}


/*
|--------------------------------------------------------------------------
| STEPPER
|--------------------------------------------------------------------------
*/

.stepper-card{
    padding:14px !important;

    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--surface-2) !important;
}


.stepper{
    grid-template-columns:50px 1fr 50px !important;
    gap:8px !important;
}


.stepper button{
    width:50px !important;
    height:50px !important;

    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--surface) !important;

    color:var(--text) !important;

    font-size:22px !important;
    font-weight:900 !important;
}


.stepper button:hover{
    border-color:var(--accent) !important;

    background:rgba(255,184,0,.10) !important;
}


.stepper input{
    height:50px !important;

    padding:0 !important;

    text-align:center !important;

    font-weight:900 !important;
}


/*
|--------------------------------------------------------------------------
| DETAILS
|--------------------------------------------------------------------------
*/

.mini-details,
.advanced-settings,
.result-more{
    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--surface-2) !important;

    overflow:hidden !important;
}


.mini-details summary,
.advanced-settings summary,
.result-more summary{
    min-height:48px !important;

    padding:12px 14px !important;

    color:var(--text) !important;

    font-size:13px !important;
    font-weight:800 !important;
}


.details-body,
.result-more-body{
    border-top:1px solid var(--line) !important;
}


/*
|--------------------------------------------------------------------------
| CHECKBOX
|--------------------------------------------------------------------------
*/

.check-row{
    min-height:54px !important;

    padding:12px 14px !important;

    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--surface) !important;
}


/*
|--------------------------------------------------------------------------
| CALCULATE BUTTON
|--------------------------------------------------------------------------
*/

.btn-calc{
    width:100% !important;

    min-height:50px !important;

    padding:0 18px !important;

    border:0 !important;

    border-radius:12px !important;

    background:var(--button-bg) !important;

    color:var(--button-text) !important;

    font-size:14px !important;
    font-weight:800 !important;
}


.btn-calc:hover{
    filter:brightness(.94) !important;
}


/*
|--------------------------------------------------------------------------
| ERROR
|--------------------------------------------------------------------------
*/

.error{
    border:1px solid #fecaca !important;

    border-radius:12px !important;

    background:#fef2f2 !important;

    color:#b42318 !important;
}


/*
|--------------------------------------------------------------------------
| RESULT PANEL
|--------------------------------------------------------------------------
*/

.result-panel{
    position:sticky !important;

    top:16px !important;

    max-height:none !important;

    overflow:visible !important;
}


/*
|--------------------------------------------------------------------------
| MAIN RESULT — BETON STYLE
|--------------------------------------------------------------------------
*/

.metric-main{
    margin:0 0 12px !important;

    padding:25px !important;

    border-radius:var(--radius) !important;

    background:#111 !important;

    color:#fff !important;

    text-align:center !important;
}


.metric-main > span{
    color:#a1a1aa !important;

    font-size:12px !important;

    font-weight:700 !important;

    letter-spacing:1px !important;
}


.metric-main strong{
    margin:5px 0 10px !important;

    color:var(--accent) !important;

    font-size:32px !important;
    font-weight:900 !important;

    letter-spacing:-1px !important;
}


.metric-main small{
    color:#d1d5db !important;
}


/*
|--------------------------------------------------------------------------
| KAINTA / WHATSAPP CTA
|--------------------------------------------------------------------------
*/

.result-cta{
    margin:0 0 12px !important;

    padding:18px !important;

    border:1px solid var(--line) !important;

    border-radius:var(--radius) !important;

    background:var(--surface) !important;
}


.result-cta strong{
    color:var(--text) !important;
}


.result-cta p{
    color:var(--muted) !important;
}


.result-cta a{
    min-height:50px !important;

    border-radius:12px !important;

    background:var(--accent) !important;

    color:#1b1300 !important;

    font-weight:900 !important;
}


.result-cta a:hover{
    filter:brightness(.96) !important;
}


/*
|--------------------------------------------------------------------------
| RESULT METRICS
|--------------------------------------------------------------------------
*/

.metrics{
    grid-template-columns:1fr 1fr !important;

    gap:10px !important;
}


.metric{
    min-height:72px !important;

    padding:12px !important;

    border:1px solid var(--line) !important;

    border-radius:12px !important;

    background:var(--surface-2) !important;
}


.metric span{
    color:var(--muted) !important;

    font-size:11px !important;
}


.metric strong{
    color:var(--text) !important;

    font-size:15px !important;
    font-weight:800 !important;
}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

.status{
    border-radius:12px !important;
}


.status.warn{
    border:1px solid rgba(255,184,0,.35) !important;

    background:rgba(255,184,0,.08) !important;

    color:var(--text) !important;
}


.status.good{
    border:1px solid #abefc6 !important;

    background:#ecfdf3 !important;

    color:#05603a !important;
}


/*
|--------------------------------------------------------------------------
| MATERIAL DETAILS
|--------------------------------------------------------------------------
*/

.result-more{
    margin-top:12px !important;
}


.result-more > summary{
    cursor:pointer !important;
}


.result-more:not([open])
.result-more-body{
    display:none !important;
}


.result-more[open]
.result-more-body{
    display:block !important;
}


.shopping{
    margin-top:12px !important;

    padding-top:10px !important;

    border-top:0 !important;
}


.shopping h3{
    color:var(--text) !important;

    font-size:14px !important;
}


.shop-row{
    padding:8px 0 !important;

    border-bottom:1px dashed var(--line) !important;

    font-size:12px !important;
}


.shop-row span:first-child{
    color:var(--muted) !important;
}


.shop-row span:last-child{
    color:var(--text) !important;
}


/*
|--------------------------------------------------------------------------
| SOURCE
|--------------------------------------------------------------------------
*/

.source{
    color:var(--muted) !important;

    font-size:11px !important;
    line-height:1.55 !important;
}


/*
|--------------------------------------------------------------------------
| MOBILE STICKY CTA
|--------------------------------------------------------------------------
*/

.mobile-sticky-cta a{
    background:var(--accent) !important;

    color:#1b1300 !important;
}


/*
|--------------------------------------------------------------------------
| FOCUS — BETON ACCESSIBILITY RULE
|--------------------------------------------------------------------------
*/

button:focus-visible,
a:focus-visible,
input:focus-visible,
select:focus-visible,
textarea:focus-visible,
summary:focus-visible{

    outline:3px solid var(--accent) !important;

    outline-offset:3px !important;
}


/*
|--------------------------------------------------------------------------
| DESKTOP
|--------------------------------------------------------------------------
*/

@media(min-width:1100px){

    .paint-wrap{
        max-width:1280px !important;
    }

    .layout{
        display:grid !important;

        grid-template-columns:
            minmax(0,1fr)
            minmax(400px,480px) !important;

        gap:24px !important;
    }

    .result-panel{
        position:sticky !important;
        top:16px !important;
    }
}


/*
|--------------------------------------------------------------------------
| TABLET / MOBILE
|--------------------------------------------------------------------------
*/

@media(max-width:1099px){

    .paint-wrap{
        max-width:800px !important;
    }

    .layout{
        display:block !important;
    }

    .result-panel{
        position:static !important;

        margin-top:20px !important;
    }
}


@media(max-width:640px){

    .paint-wrap{
        padding:
            24px
            14px
            100px !important;
    }

    .panel{
        padding:20px !important;
    }

    .grid2,
    .tabs,
    .metrics{
        grid-template-columns:1fr !important;
    }

    .metric-main strong{
        font-size:
            clamp(
                26px,
                7.5vw,
                34px
            ) !important;

        line-height:1.15 !important;
    }

    .paint-hero h1{
        font-size:26px !important;
    }
}


/*
|--------------------------------------------------------------------------
| REDUCED MOTION
|--------------------------------------------------------------------------
*/

@media(prefers-reduced-motion:reduce){

    *{
        transition:none !important;
        scroll-behavior:auto !important;
    }
}

</style>


<!-- PAINT_SEO_META_V2 -->

<meta
    name="robots"
    content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1"
>

<meta
    property="og:type"
    content="website"
>

<meta
    property="og:title"
    content="Boya Hesaplama 2026: Kaç Litre Boya Gider?"
>

<meta
    property="og:description"
    content="Duvar ve tavan boya ihtiyacını, kapı-pencere düşümünü, fireyi, malzeme ve işçilik bütçesini hesaplayın."
>

<meta
    property="og:url"
    content="https://worksitem.com/araclar/boya-hesaplayici"
>

<meta
    name="twitter:title"
    content="Boya Hesaplama 2026: Kaç Litre Boya Gider?"
>

<meta
    name="twitter:description"
    content="Kaç litre boya gerekir? Oda veya cephe ölçülerinizden boya ve yaklaşık maliyet hesabı yapın."
>


<style id="paint-seo-content-v2">

.paint-seo-section{
    max-width:816px;
    margin:24px auto 0;
    padding:0 20px;
    color:var(--text);
}

.paint-seo-section.first{
    margin-top:48px;
}

.paint-seo-card{
    background:var(--surface);
    border:1px solid var(--line);
    border-radius:16px;
    padding:30px;
}

.paint-seo-card h2{
    margin:0 0 14px;
    font-size:26px;
    line-height:1.25;
    letter-spacing:-.4px;
}

.paint-seo-card h3{
    margin:24px 0 8px;
    font-size:18px;
}

.paint-seo-card p{
    margin:0 0 16px;
    color:var(--muted);
    line-height:1.7;
}

.paint-short-answer{
    margin:18px 0 26px;
    padding:16px;
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--surface-2);
}

.paint-short-answer strong{
    display:block;
    margin-bottom:6px;
    color:var(--text);
}

.paint-formula{
    margin:16px 0;
    padding:16px;
    border-left:4px solid var(--accent);
    border-radius:10px;
    background:var(--surface-2);
}

.paint-formula strong{
    color:var(--text);
}

.paint-example-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
    margin:18px 0 24px;
}

.paint-example-item{
    padding:14px;
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--surface-2);
}

.paint-example-item span{
    display:block;
    margin-bottom:4px;
    color:var(--muted);
    font-size:12px;
}

.paint-example-item strong{
    color:var(--text);
    font-size:15px;
}

.paint-seo-table-wrap{
    overflow-x:auto;
    margin:20px 0 26px;
}

.paint-seo-table{
    width:100%;
    min-width:620px;
    border-collapse:collapse;
    font-size:14px;
}

.paint-seo-table th,
.paint-seo-table td{
    padding:12px;
    border:1px solid var(--line);
}

.paint-seo-table th{
    background:var(--surface-2);
    text-align:left;
}

.paint-seo-table td:not(:first-child),
.paint-seo-table th:not(:first-child){
    text-align:right;
}

.paint-info-box{
    padding:16px;
    border:1px solid rgba(255,184,0,.35);
    border-radius:12px;
    background:rgba(255,184,0,.08);
}

.paint-info-box strong{
    display:block;
    margin-bottom:6px;
}

.paint-faq{
    display:grid;
    gap:10px;
}

.paint-faq details{
    padding:14px 16px;
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--surface-2);
}

.paint-faq summary{
    cursor:pointer;
    font-weight:800;
}

.paint-faq p{
    margin:12px 0 0;
}

.paint-related{
    display:grid;
    gap:10px;
    margin-top:16px;
}

.paint-related a{
    display:block;
    padding:16px;
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--surface-2);
    color:var(--text);
}

.paint-related a strong{
    display:block;
}

.paint-related a span{
    display:block;
    margin-top:4px;
    color:var(--muted);
    font-size:13px;
}

.paint-source-box{
    margin-top:28px;
    padding-top:20px;
    border-top:1px solid var(--line);
}

.paint-source-box p{
    margin-bottom:8px;
    font-size:13px;
}

@media(max-width:640px){

    .paint-seo-section{
        padding:0 14px;
    }

    .paint-seo-card{
        padding:20px;
    }

    .paint-seo-card h2{
        font-size:22px;
    }

    .paint-example-grid{
        grid-template-columns:1fr;
    }
}

</style>


<style id="paint-result-details-ui">
.result-more > summary{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    min-height:50px;
    padding:13px 16px;

    border:2px solid #d1d5db;
    border-radius:12px;

    background:#ffffff;
    color:#111827;

    font-size:14px;
    font-weight:800;
    line-height:1.35;

    cursor:pointer;
    user-select:none;

    transition:
        background-color .18s ease,
        border-color .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}

.result-more > summary::after{
    content:'＋';
    flex:0 0 auto;
    font-size:20px;
    font-weight:900;
    line-height:1;
}

.result-more[open] > summary::after{
    content:'−';
}

.result-more > summary:hover{
    background:#ffb800;
    border-color:#ffb800;
    color:#111827;
    transform:translateY(-1px);
    box-shadow:0 6px 18px rgba(17,24,39,.10);
}

.result-more > summary:focus-visible{
    outline:3px solid rgba(255,184,0,.45);
    outline-offset:3px;
    border-color:#ffb800;
}

[data-theme="dark"] .result-more > summary{
    background:#18181b;
    border-color:#3f3f46;
    color:#f4f4f5;
}

[data-theme="dark"] .result-more > summary:hover{
    background:#ffb800;
    border-color:#ffb800;
    color:#111827;
}
</style>

<style id="paint-visualizer-module-style">
#paint-visualizer-module{
    max-width:1180px;
    margin:26px auto 42px;
    padding:0 20px;
}

.paint-visualizer-launch{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:24px;
    padding:24px;
    border:1px solid #e4e7eb;
    border-radius:16px;
    background:#fff;
}

.paint-visualizer-launch-copy{
    max-width:720px;
}

.paint-visualizer-eyebrow{
    display:inline-flex;
    margin-bottom:8px;
    font-size:11px;
    font-weight:900;
    letter-spacing:.5px;
    text-transform:uppercase;
    color:#8a5a00;
}

.paint-visualizer-launch h2{
    margin:0 0 7px;
    font-size:22px;
    line-height:1.25;
    color:#101318;
}

.paint-visualizer-launch p{
    margin:0;
    color:#6b7280;
    font-size:14px;
    line-height:1.6;
}

#paintVisualizerToggle{
    flex:0 0 auto;
    min-height:48px;
    padding:0 19px;
    border:1px solid #ffb800;
    border-radius:12px;
    background:#ffb800;
    color:#111827;
    font:inherit;
    font-size:14px;
    font-weight:900;
    cursor:pointer;
    transition:.18s ease;
}

#paintVisualizerToggle:hover{
    transform:translateY(-1px);
    box-shadow:0 8px 20px rgba(17,24,39,.12);
}

#paintVisualizerToggle:focus-visible{
    outline:3px solid rgba(255,184,0,.40);
    outline-offset:3px;
}

.paint-visualizer-frame-wrap{
    margin-top:16px;
    border:1px solid #e4e7eb;
    border-radius:16px;
    overflow:hidden;
    background:#f8f9fb;
}

.paint-visualizer-frame-wrap[hidden]{
    display:none !important;
}

#paintVisualizerFrame{
    display:block;
    width:100%;
    height:900px;
    border:0;
    background:#f8f9fb;
}

.paint-visualizer-disclaimer{
    padding:10px 14px;
    border-top:1px solid #e4e7eb;
    color:#6b7280;
    font-size:12px;
    line-height:1.5;
}

[data-theme="dark"] .paint-visualizer-launch{
    background:#18181b;
    border-color:#3f3f46;
}

[data-theme="dark"] .paint-visualizer-launch h2{
    color:#f4f4f5;
}

[data-theme="dark"] .paint-visualizer-frame-wrap{
    background:#18181b;
    border-color:#3f3f46;
}

@media(max-width:720px){
    .paint-visualizer-launch{
        align-items:stretch;
        flex-direction:column;
    }

    #paintVisualizerToggle{
        width:100%;
    }

    #paintVisualizerFrame{
        height:780px;
    }
}
</style>
    <x-calc.head />
</head>

<body data-tool-slug="paint-calculator" class="wc">

<x-site.contech-header />

@php
    $priceBag = collect($paintPrices ?? []);

    $laborOfficial = $priceBag->get('labor_reference');
    $scaffoldA = $priceBag->get('scaffold_3_51');
    $scaffoldB = $priceBag->get('scaffold_51_plus');

    $wallPaintPrice = $priceBag->get('wall_paint');
    $ceilingPaintPrice = $priceBag->get('ceiling_paint');
    $primerPrice = $priceBag->get('primer');
    $coverPrice = $priceBag->get('cover');
    $tapePrice = $priceBag->get('masking_tape');
    $rollerPrice = $priceBag->get('roller');
    $brushPrice = $priceBag->get('brush');
    $trayPrice = $priceBag->get('tray');
@endphp

<main class="paint-wrap" id="ana-icerik">

    <x-calc.hero title="Boya hesaplama: kaç litre boya gider?" crumb="Boya hesaplama" :facts="['İç ve dış cephe', 'Malzeme ve işçilik bütçesi']">
        5 × 4 m ve 2,8 m tavan yüksekliğindeki bir odanın duvarları, 1 kapı ve 2 pencere düşüldükten sonra yaklaşık 45 m² eder; 2 kat boya ve %10 fire ile yaklaşık 10 litre duvar boyası gerekir. Ölçüleri girin; boya litresini, kutu sayısını, işçiliği ve gerekiyorsa dış cephe iskele bedelini görün.
    </x-calc.hero>
    <div style="height:24px"></div>

    <div class="layout">

        <section class="panel">
            <h2>Proje bilgileri</h2>

            <div class="tabs" role="tablist" aria-label="Hesaplama yöntemi">

                <button
                    class="tab active"
                    type="button"
                    id="modeDimensions"
                    role="tab"
                    aria-selected="true">
                    Ölçülerden Hesapla
                </button>

                <button
                    class="tab"
                    type="button"
                    id="modeNet"
                    role="tab"
                    aria-selected="false">
                    Net Alanı (m²) Biliyorum
                </button>

            </div>

            <div id="calcError" class="error" role="alert"></div>

            <div class="grid2">

                <div class="field">
                    <label for="applicationType">Uygulama alanı</label>

                    <select id="applicationType">
                        <option value="interior">İç cephe</option>
                        <option value="exterior">Dış cephe</option>
                    </select>
                </div>

                <div class="field" id="surfaceScopeField"><label for="surfaceScope">Boyanacak yer</label>

                    <select id="surfaceScope">
                        <option value="walls">Sadece duvarlar</option>
                        <option value="walls_ceiling" selected>Duvarlar + tavan</option>
                        <option value="ceiling">Sadece tavan</option>
                    </select>
                </div>

            </div>

            <!-- ÖLÇÜLERDEN -->

            <div id="dimensionsPanel">

                <div class="grid2">

                    <div class="field">
                        <label for="length">Oda uzunluğu (m)</label>
                        <input id="length" type="number" step="0.01" min="0.1" value="5">
                    </div>

                    <div class="field">
                        <label for="width">Oda genişliği (m)</label>
                        <input id="width" type="number" step="0.01" min="0.1" value="4">
                    </div>

                </div>

                <div class="field">
                    <label for="height">Tavan yüksekliği (m)</label>
                    <input id="height" type="number" step="0.01" min="0.1" value="2.8">

                    <small>
                        Standart bir oda için yaklaşık tavan yüksekliğini girmeniz yeterlidir.
                    </small>
                </div>

                <div class="section-title">Kapı ve pencereler</div>

                <div class="grid2">

                    <div class="stepper-card">

                        <label for="doorCount">Kapı sayısı</label>

                        <div class="stepper">
                            <button
                                type="button"
                                data-step-target="doorCount"
                                data-step-direction="-1"
                                aria-label="Kapı sayısını azalt">
                                −
                            </button>

                            <input
                                id="doorCount"
                                type="number"
                                value="1"
                                min="0"
                                max="20"
                                readonly
                                aria-label="Kapı sayısı">

                            <button
                                type="button"
                                data-step-target="doorCount"
                                data-step-direction="1"
                                aria-label="Kapı sayısını artır">
                                +
                            </button>
                        </div>

                    </div>

                    <div class="stepper-card">

                        <label for="windowCount">Pencere sayısı</label>

                        <div class="stepper">

                            <button
                                type="button"
                                data-step-target="windowCount"
                                data-step-direction="-1"
                                aria-label="Pencere sayısını azalt">
                                −
                            </button>

                            <input
                                id="windowCount"
                                type="number"
                                value="2"
                                min="0"
                                max="30"
                                readonly
                                aria-label="Pencere sayısı">

                            <button
                                type="button"
                                data-step-target="windowCount"
                                data-step-direction="1"
                                aria-label="Pencere sayısını artır">
                                +
                            </button>

                        </div>

                    </div>

                </div>

                <small class="detail-help" style="margin:10px 2px 0">
                    Aynı ölçüde birden fazla kapı veya pencere için sadece + / − ile adedi değiştirin.
                </small>

                <details class="mini-details">

                    <summary>
                        Kapı / pencere ölçülerini değiştir
                    </summary>

                    <div class="details-body">

                        <span class="detail-help">
                            Başlangıçta yaygın planlama ölçüleri kullanılır.
                            Gerçek ölçüleri biliyorsanız aşağıdan değiştirebilirsiniz.
                        </span>

                        <div class="grid2">

                            <div class="field">
                                <label for="doorWidth">Kapı genişliği (m)</label>
                                <input id="doorWidth" type="number" step="0.01" min="0.1" value="0.90">
                            </div>

                            <div class="field">
                                <label for="doorHeight">Kapı yüksekliği (m)</label>
                                <input id="doorHeight" type="number" step="0.01" min="0.1" value="2.10">
                            </div>

                            <div class="field">
                                <label for="windowWidth">Pencere genişliği (m)</label>
                                <input id="windowWidth" type="number" step="0.01" min="0.1" value="1.20">
                            </div>

                            <div class="field">
                                <label for="windowHeight">Pencere yüksekliği (m)</label>
                                <input id="windowHeight" type="number" step="0.01" min="0.1" value="1.50">
                            </div>

                        </div>

                    </div>

                </details>

            </div>

            <!-- NET ALAN -->

            <div id="netPanel" class="hidden">

                <div class="grid2">

                    <div class="field" id="netWallField">
                        <label for="netWallArea">Net duvar alanı (m²)</label>
                        <input id="netWallArea" type="number" step="0.01" min="0" value="100">
                    </div>

                    <div class="field" id="netCeilingField">
                        <label for="netCeilingArea">Net tavan alanı (m²)</label>
                        <input id="netCeilingArea" type="number" step="0.01" min="0" value="20">
                    </div>

                </div>

                <div class="field">
                        <label for="netHeight">Uygulama yüksekliği (m)</label>
                        <input id="netHeight" type="number" step="0.01" min="0.1" value="2.8">

                        <small>
                            Dış cephede erişim ve iskele değerlendirmesi için kullanılır.
                        </small>
                    </div>

                </div>

            </div>

            <!-- DIŞ CEPHE -->

            <div id="accessPanel" class="hidden">

                <div class="section-title" id="accessSectionTitle">Erişim / iskele durumu</div>

                <div class="field">

                    <label for="accessType">Çalışma alanına nasıl erişilecek?</label>

                    <select id="accessType">
                        <option value="normal">Zeminden / normal erişim</option>
                        <option value="ladder">Merdiven ile erişim</option>
                        <option value="scaffold">İskele gerekli</option>
                        <option value="platform">Sepetli platform / vinç</option>
                        <option value="unknown">Emin değilim</option>
                    </select>

                    <small>
                        İskele seçilirse uygun resmi dış cephe pozuna göre ayrıca hesaplanır.
                    </small>

                </div>

            </div>

            <!-- ADVANCED -->

            <details class="advanced-settings">

                <summary>
                    Hesap ayarları (isteğe bağlı)
                </summary>

                <div class="details-body">

                    <div class="grid2">

                        <div class="field">
                            <label for="coats">Boya katı</label>

                            <select id="coats">
                                <option value="1">1 kat</option>
                                <option value="2" selected>2 kat</option>
                                <option value="3">3 kat</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="coverage">Ürün sarfiyatı (m²/L · kat)</label>

                            <input
                                id="coverage"
                                type="number"
                                step="0.1"
                                min="0.1"
                                value="10">

                            <small>
                                Başlangıçta 10 m²/L planlama varsayımı kullanılır.
                                Ürünün teknik föy değerini biliyorsanız değiştirebilirsiniz.
                            </small>
                        </div>

                    </div>

                    <div class="grid2">

                        <div class="field">
                            <label for="waste">Fire / emniyet payı (%)</label>

                            <input
                                id="waste"
                                type="number"
                                step="1"
                                min="0"
                                max="30"
                                value="10">
                        </div>

                        <div class="field">
                            <label for="surfaceState">Yüzey durumu</label>

                            <select id="surfaceState">
                                <option value="painted">İyi / eski boyalı yüzey</option>
                                <option value="satin">Saten alçı yüzey</option>
                                <option value="raw">Ham / emici yüzey</option>
                                <option value="rough">Tamiratlı / pürüzlü yüzey</option>
                            </select>
                        </div>

                    </div>

                    <div class="grid2">

                        <div class="field">
                            <label for="colorChange">Renk geçişi</label>

                            <select id="colorChange">
                                <option value="similar">Benzer renk</option>
                                <option value="dark_light">Koyu → açık</option>
                                <option value="light_dark">Açık → koyu</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="primerCoverage">Astar sarfiyatı (m²/L)</label>

                            <input
                                id="primerCoverage"
                                type="number"
                                step="0.1"
                                min="0.1"
                                value="10">
                        </div>

                    </div>

                    <label class="check-row">

                        <input
                            type="checkbox"
                            id="includePrimer">

                        <span>
                            <strong>Astarı ayrıca hesapla</strong><br>

                            <small>
                                1 kat astar kabul edilir.
                            </small>
                        </span>

                    </label>

                </div>

            </details>

            <button
                type="button"
                class="btn-calc"
                id="paintCalcBtn">
                Boya İhtiyacını Hesapla →
            </button>

        </section>

        <!-- RESULT -->

        <aside class="panel result-panel">

            <h2>Hesap özeti</h2>

            <div id="resultEmpty" class="result-empty" data-wc-empty>
                Ölçülerinizi girip hesaplama yaptığınızda boya miktarı ve yaklaşık işçilik bütçesi burada görünecek.
            </div>

            <div
                id="resultLive"
                class="result-live"
                data-wc-result
                aria-live="polite">

                <div class="metric-main">

                    <span id="budgetTitle">Tahmini toplam bütçe</span>

                    <strong id="budgetRange" data-wc-price>—</strong>

                    <small>
                        Malzeme, işçilik ve varsa erişim bedeli birlikte hesaplanır.
                    </small>

                    <small id="assumptionNote"></small>

                </div>

                <!-- YÜKSEK NİYET CTA -->
                <div class="result-cta">

                    <strong>Uygulama için teklif alın</strong>

                    <p>
                        Hesap sonucunu şirket WhatsApp hattımıza gönderin.
                        Ekip proje detaylarını inceleyerek sizinle iletişime geçsin.
                    </p>

                    <a
                        id="whatsappCta"
                        href="https://wa.me/908503464983"
                        target="_blank"
                        rel="noopener">
                        WhatsApp'tan Teklif İste →
                    </a>

                </div>

                <div class="metrics">

                    <div class="metric" id="wallAreaMetric"><span id="wallAreaLabel">Duvar alanı</span>
                        <strong id="wallAreaResult">—</strong>
                    </div>

                    <div class="metric" id="ceilingAreaMetric"><span>Tavan alanı</span>
                        <strong id="ceilingAreaResult">—</strong>
                    </div>

                    <div class="metric">
                        <span>Duvar boyası</span>
                        <strong id="wallPaintResult">—</strong>
                    </div>

                    <div class="metric" id="ceilingPaintMetric"><span>Tavan boyası</span>
                        <strong id="ceilingPaintResult">—</strong>
                    </div>

                    <div class="metric">
                        <span>Astar</span>
                        <strong id="primerLiters">—</strong>
                    </div>

                    <div class="metric">
                        <span>Malzeme maliyeti</span>
                        <strong id="materialTotal">—</strong>
                    </div>

                    <div class="metric">
                        <span>İşçilik</span>
                        <strong id="laborTotal">—</strong>
                    </div>

                    <div class="metric">
                        <span>İşçilik birim referansı</span>
                        <strong id="laborRate">—</strong>
                    </div>

                    <div class="metric">
                        <span>İskele / erişim</span>
                        <strong id="accessCost">—</strong>
                    </div>

                    <div class="metric full">
                        <span>Hesaplanan alan</span>
                        <strong
                            id="areaDetail"
                            style="font-size:14px;line-height:1.5">
                            —
                        </strong>
                    </div>

                </div>

                <div
                    id="largeAreaNotice"
                    class="status good hidden">
                </div>

                <div
                    id="accessNotice"
                    class="status warn hidden">
                </div>

                <div
                    id="surfaceNotice"
                    class="status warn hidden">
                </div>

                <!-- MALZEME -->
                <details class="result-more">
                    <summary>Malzeme listesini ve hesap detaylarını göster</summary>
                    <div class="result-more-body">

                <div class="shopping">

                    <h3>Uygulama malzeme listesi</h3>

                    <div class="shop-row">
                        <span>Boya</span>
                        <span id="shopPaint">—</span>
                    </div>

                    <div class="shop-row">
                        <span>Astar</span>
                        <span id="shopPrimer">—</span>
                    </div>

                    <div class="shop-row">
                        <span>Koruma örtüsü / naylon</span>
                        <span id="shopCover">—</span>
                    </div>

                    <div class="shop-row">
                        <span>Maskeleme bandı</span>
                        <span id="shopTape">—</span>
                    </div>

                    <div class="shop-row">
                        <span>Macun · zımpara</span>
                        <span id="shopPrep">—</span>
                    </div>

                    <div class="shop-row">
                        <span>Rulo · fırça · boya tavası</span>
                        <span id="shopToolSet">Uygulama seti</span>
                    </div>

                </div>

                <div class="source">

                    <strong>Fiyat kaynakları</strong><br>

                    İşçilik m² referansı:
                    Kainta İnşaat Uzmanları (saha) · 03.10.2026.<br>

                    Resmi kontrol:
                    Boyacı Ustası
                    {{ $laborOfficial
                        ? number_format($laborOfficial->base_price, 2, ',', '.') . ' TL/' . $laborOfficial->unit
                        : 'veri bulunamadı'
                    }}.<br>

                    İskele / erişim bedeli seçilen erişim yöntemine göre hesaplanır.<br>

                </div>
                    </div>
                </details>

            </div>

        </aside>

    </div>

</main>


<section id="paint-visualizer-module">
    <div class="paint-visualizer-launch">
        <div class="paint-visualizer-launch-copy">
            <span class="paint-visualizer-eyebrow">Renk Önizleme</span>

            <h2>🎨 Yapınız nasıl görünür?</h2>

            <p>
                Seçtiğiniz rengi oda veya cephe fotoğrafınız üzerinde deneyin.
                Boyanacak alanı fırçayla işaretleyin ve sonucu anında önizleyin.
            </p>
        </div>

        <button
            type="button"
            id="paintVisualizerToggle"
            aria-expanded="false"
            aria-controls="paintVisualizerFrameWrap">
            Rengi Yapımda Gör →
        </button>
    </div>

    <div
        id="paintVisualizerFrameWrap"
        class="paint-visualizer-frame-wrap"
        hidden>

        <iframe
            id="paintVisualizerFrame"
            title="Duvar Rengi Görselleştirici"
            data-src="/araclar/duvar-rengi-gorsellestirici?embed=1"
            loading="lazy">
        </iframe>

        <div class="paint-visualizer-disclaimer">
            Dijital renk önizlemesi yaklaşık sonuç verir. Nihai renk seçimini
            fiziksel numune veya üretici kartelasıyla doğrulayın.
        </div>
    </div>
</section>

<script>
(function(){
    const button = document.getElementById('paintVisualizerToggle');
    const wrap = document.getElementById('paintVisualizerFrameWrap');
    const frame = document.getElementById('paintVisualizerFrame');

    if (!button || !wrap || !frame) return;

    button.addEventListener('click', function(){
        const willOpen = wrap.hidden;

        if (willOpen && !frame.getAttribute('src')) {
            frame.setAttribute(
                'src',
                frame.dataset.src
            );
        }

        wrap.hidden = !willOpen;

        button.setAttribute(
            'aria-expanded',
            willOpen ? 'true' : 'false'
        );

        button.textContent = willOpen
            ? 'Görselleştiriciyi Kapat ↑'
            : 'Rengi Yapımda Gör →';

        if (willOpen) {
            window.setTimeout(function(){
                wrap.scrollIntoView({
                    behavior:'smooth',
                    block:'start'
                });
            }, 80);
        }
    });
})();
</script>
<!-- MOBILE CTA -->

<div
    id="mobileStickyCta"
    class="mobile-sticky-cta"
    aria-hidden="true">

    <a
        id="mobileWhatsappCta"
        href="https://wa.me/908503464983"
        target="_blank"
        rel="noopener">
        WhatsApp'tan Teklif İste →
    </a>

</div>

<script>
const SETTINGS = @json($paintSettings ?? []);

const OFFICIAL = {
    laborReference: @json($laborOfficial?->base_price),
    scaffoldA: @json($scaffoldA?->price),
    scaffoldB: @json($scaffoldB?->price)
};

const MARKET = {
    wallPaint: @json($wallPaintPrice?->price),
    ceilingPaint: @json($ceilingPaintPrice?->price),
    primer: @json($primerPrice?->price),
    cover: @json($coverPrice?->price),
    tape: @json($tapePrice?->price),
    roller: @json($rollerPrice?->price),
    brush: @json($brushPrice?->price),
    tray: @json($trayPrice?->price)
};

let mode = 'dimensions';
let lastInteriorScope = 'walls_ceiling';
let accessWasVisible = false;

const $ = id => document.getElementById(id);

const num = id => Number($(id).value);

const money = value =>
    new Intl.NumberFormat('tr-TR', {
        maximumFractionDigits:0
    }).format(value) + ' TL';


/*
|--------------------------------------------------------------------------
| STEPPER
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('[data-step-target]')
    .forEach(button => {

        button.addEventListener('click', () => {

            const target =
                $(button.dataset.stepTarget);

            const direction =
                Number(button.dataset.stepDirection);

            const min =
                Number(target.min || 0);

            const max =
                Number(target.max || 999);

            let value =
                Number(target.value || 0) + direction;

            value =
                Math.max(
                    min,
                    Math.min(max,value)
                );

            target.value = value;
        });
    });


/*
|--------------------------------------------------------------------------
| MODES
|--------------------------------------------------------------------------
*/

function setMode(next){

    mode = next;

    const dimensions =
        next === 'dimensions';

    $('modeDimensions')
        .classList.toggle(
            'active',
            dimensions
        );

    $('modeNet')
        .classList.toggle(
            'active',
            !dimensions
        );

    $('modeDimensions')
        .setAttribute(
            'aria-selected',
            dimensions ? 'true' : 'false'
        );

    $('modeNet')
        .setAttribute(
            'aria-selected',
            dimensions ? 'false' : 'true'
        );

    $('dimensionsPanel')
        .classList.toggle(
            'hidden',
            !dimensions
        );

    $('netPanel')
        .classList.toggle(
            'hidden',
            dimensions
        );
}


function syncSurfaceScope(){

    if(mode !== 'net') return;

    const scope = $('surfaceScope').value;

    $('netWallField').classList.toggle(
        'hidden',
        scope === 'ceiling'
    );

    $('netCeilingField').classList.toggle(
        'hidden',
        scope === 'walls'
    );
}


function syncApplication(){

    const exterior =
        $('applicationType').value === 'exterior';

    const lengthLabel =
        document.querySelector('label[for="length"]');

    const widthLabel =
        document.querySelector('label[for="width"]');

    const heightLabel =
        document.querySelector('label[for="height"]');

    const widthField =
        $('width').closest('.field');

    const heightField =
        $('height').closest('.field');

    const heightHelp =
        heightField.querySelector('small');

    const netWallLabel =
        document.querySelector('label[for="netWallArea"]');

    const surfaceField =
        $('surfaceScopeField');


    if(surfaceField){
        surfaceField.classList.toggle(
            'hidden',
            exterior
        );
    }


    if(exterior){

        if(!$('surfaceScope').disabled){
            lastInteriorScope =
                $('surfaceScope').value;
        }


        lengthLabel.textContent =
            'Cephe genişliği (m)';

        heightLabel.textContent =
            'Cephe yüksekliği (m)';


        widthField
            .classList
            .add('hidden');


        if(heightHelp){
            heightHelp.textContent =
                'Erişim ve iskele değerlendirmesinde de kullanılır.';
        }


        if(netWallLabel){
            netWallLabel.textContent =
                'Net cephe alanı (m²)';
        }


        $('surfaceScope').value =
            'walls';

        $('surfaceScope').disabled =
            true;


    }else{


        lengthLabel.textContent =
            'Oda uzunluğu (m)';

        widthLabel.textContent =
            'Oda genişliği (m)';

        heightLabel.textContent =
            'Tavan yüksekliği (m)';


        widthField
            .classList
            .remove('hidden');


        if(heightHelp){
            heightHelp.textContent =
                'Yaklaşık tavan yüksekliğini girmeniz yeterlidir.';
        }


        if(netWallLabel){
            netWallLabel.textContent =
                'Net duvar alanı (m²)';
        }


        $('surfaceScope').disabled =
            false;

        $('surfaceScope').value =
            lastInteriorScope ||
            'walls_ceiling';
    }


    if($('wallAreaLabel')){
        $('wallAreaLabel').textContent =
            exterior
                ? 'Cephe alanı'
                : 'Duvar alanı';
    }


    if($('ceilingAreaMetric')){
        $('ceilingAreaMetric')
            .classList
            .toggle(
                'hidden',
                exterior
            );
    }


    if($('ceilingPaintMetric')){
        $('ceilingPaintMetric')
            .classList
            .toggle(
                'hidden',
                exterior
            );
    }


    syncSurfaceScope();
    syncAccessPanel();
}


function syncAccessPanel(){

    const exterior =
        $('applicationType').value === 'exterior';


    const height =
        mode === 'dimensions'
            ? Number($('height').value)
            : Number($('netHeight').value);


    const highInterior =
        !exterior &&
        Number.isFinite(height) &&
        height > 3;


    const showAccess =
        exterior ||
        highInterior;


    $('accessPanel')
        .classList
        .toggle(
            'hidden',
            !showAccess
        );


    /*
    | Bölüm ilk kez açılıyorsa kullanıcıya
    | bilinçli seçim yaptır.
    */
    if(
        showAccess &&
        !accessWasVisible &&
        $('accessType').value === 'normal'
    ){
        $('accessType').value =
            'unknown';
    }


    if(!showAccess){

        $('accessType').value =
            'normal';
    }


    if($('accessSectionTitle')){

        $('accessSectionTitle').textContent =
            exterior
                ? 'Erişim / iskele durumu'
                : '3 m üzeri çalışma erişimi';
    }


    const help =
        document.querySelector(
            '#accessPanel .field small'
        );


    if(help){

        help.textContent =
            exterior
                ? 'Cephede iskele, merdiven veya platform gerekip gerekmediğini seçin.'
                : '3 m üzerindeki iç mekân çalışmalarında erişim yöntemini seçin.';
    }


    accessWasVisible =
        showAccess;
}


function error(message){

    $('calcError').textContent =
        message;

    $('calcError').style.display =
        'block';

    $('calcError')
        .scrollIntoView({
            behavior:'smooth',
            block:'center'
        });
}


function clearError(){

    $('calcError').style.display =
        'none';
}


function liters(
    area,
    coats,
    coverage,
    waste
){
    return (
        area *
        coats /
        coverage *
        (1 + waste / 100)
    );
}


$('modeDimensions')
    .addEventListener(
        'click',
        () => { setMode('dimensions'); syncAccessPanel(); }
    );


$('modeNet')
    .addEventListener(
        'click',
        () => { setMode('net'); syncAccessPanel(); }
    );


$('applicationType')
    .addEventListener(
        'change',
        syncApplication
    );

$('height').addEventListener(
    'input',
    syncAccessPanel
);

$('netHeight').addEventListener(
    'input',
    syncAccessPanel
);


$('surfaceScope').addEventListener('change', function(){

    if($('applicationType').value === 'interior'){
        lastInteriorScope = this.value;
    }

    syncSurfaceScope();
});


/*
|--------------------------------------------------------------------------
| CALCULATE
|--------------------------------------------------------------------------
*/

$('paintCalcBtn')
    .addEventListener(
        'click',
        () => {

    clearError();

    const exterior =
        $('applicationType').value ===
        'exterior';

    const scope =
        $('surfaceScope').value;

    const coats =
        num('coats');

    const coverage =
        num('coverage');

    const waste =
        num('waste');


    if(
        !Number.isFinite(coverage) ||
        coverage <= 0
    ){
        error(
            'Boya sarfiyat değeri 0’dan büyük olmalıdır.'
        );

        return;
    }


    if(
        !Number.isFinite(waste) ||
        waste < 0 ||
        waste > 30
    ){
        error(
            'Fire oranı %0–30 arasında olmalıdır.'
        );

        return;
    }


    let totalArea = 0;
    let wallArea = 0;
    let ceilingArea = 0;
    let floorArea = null;
    let height = 0;
    let detail = '';
    let openingPerimeter = 0;


    /*
    |--------------------------------------------------------------------------
    | ÖLÇÜLERDEN
    |--------------------------------------------------------------------------
    */

    if(mode === 'dimensions'){

        const length =
            num('length');

        const width =
            num('width');

        height =
            num('height');

        const doorCount =
            num('doorCount');

        const windowCount =
            num('windowCount');

        const doorWidth =
            num('doorWidth');

        const doorHeight =
            num('doorHeight');

        const windowWidth =
            num('windowWidth');

        const windowHeight =
            num('windowHeight');


        if(
            !Number.isFinite(length) ||
            !Number.isFinite(height) ||
            length <= 0 ||
            height <= 0 ||
            (
                !exterior &&
                (
                    !Number.isFinite(width) ||
                    width <= 0
                )
            )
        ){
            error(
                'Lütfen oda uzunluğu, genişliği ve yüksekliği için geçerli değerler girin.'
            );

            return;
        }


        if(
            doorCount < 0 ||
            windowCount < 0 ||
            !Number.isInteger(doorCount) ||
            !Number.isInteger(windowCount)
        ){
            error(
                'Kapı ve pencere adetlerini kontrol edin.'
            );

            return;
        }


        if(
            (
                doorCount > 0 &&
                (
                    doorWidth <= 0 ||
                    doorHeight <= 0
                )
            )
            ||
            (
                windowCount > 0 &&
                (
                    windowWidth <= 0 ||
                    windowHeight <= 0
                )
            )
        ){
            error(
                'Kapı ve pencere ölçülerini kontrol edin.'
            );

            return;
        }


        const openings =
            (
                doorCount *
                doorWidth *
                doorHeight
            )
            +
            (
                windowCount *
                windowWidth *
                windowHeight
            );


        openingPerimeter =
            (
                doorCount *
                2 *
                (
                    doorWidth +
                    doorHeight
                )
            )
            +
            (
                windowCount *
                2 *
                (
                    windowWidth +
                    windowHeight
                )
            );


        if(exterior){

            /*
            |--------------------------------------------------------------------------
            | DIŞ CEPHE
            | Cephe genişliği x cephe yüksekliği
            |--------------------------------------------------------------------------
            */

            const grossFacade =
                length *
                height;


            if(openings >= grossFacade){

                error(
                    'Kapı ve pencere toplam alanı cephe alanından küçük olmalıdır.'
                );

                return;
            }


            wallArea =
                grossFacade -
                openings;

            ceilingArea =
                0;

            floorArea =
                null;

            totalArea =
                wallArea;


            detail =
                'Brüt cephe ' +
                length.toFixed(2) +
                ' × ' +
                height.toFixed(2) +
                ' m = ' +
                grossFacade.toFixed(2) +
                ' m² · ' +
                doorCount +
                ' kapı + ' +
                windowCount +
                ' pencere: −' +
                openings.toFixed(2) +
                ' m²';

        }else{

            /*
            |--------------------------------------------------------------------------
            | İÇ CEPHE
            |--------------------------------------------------------------------------
            */

            const grossWalls =
                2 *
                (length + width) *
                height;


            if(openings >= grossWalls){

                error(
                    'Kapı ve pencere toplam alanı boyanacak yüzey alanından küçük olmalıdır.'
                );

                return;
            }


            wallArea =
                grossWalls -
                openings;

            ceilingArea =
                length *
                width;

            floorArea =
                ceilingArea;


            if(scope === 'walls'){

                totalArea =
                    wallArea;

                ceilingArea =
                    0;

            }else if(scope === 'ceiling'){

                totalArea =
                    ceilingArea;

                wallArea =
                    0;

            }else{

                totalArea =
                    wallArea +
                    ceilingArea;
            }


            detail =
                'Brüt duvar ' +
                grossWalls.toFixed(2) +
                ' m² · ' +
                doorCount +
                ' kapı + ' +
                windowCount +
                ' pencere: −' +
                openings.toFixed(2) +
                ' m²';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NET ALAN
    |--------------------------------------------------------------------------
    */

    else{

        const netWall =
            scope === 'ceiling'
                ? 0
                : num('netWallArea');

        const netCeiling =
            scope === 'walls'
                ? 0
                : num('netCeilingArea');

        height =
            num('netHeight');


        if(
            !Number.isFinite(netWall) ||
            !Number.isFinite(netCeiling) ||
            !Number.isFinite(height) ||
            netWall < 0 ||
            netCeiling < 0 ||
            height <= 0 ||
            (netWall + netCeiling) <= 0
        ){

            error(
                'Net duvar/tavan alanlarını ve uygulama yüksekliğini kontrol edin.'
            );

            return;
        }


        wallArea = netWall;
        ceilingArea = netCeiling;
        totalArea = wallArea + ceilingArea;


        detail =
            'Net duvar: ' +
            wallArea.toFixed(2) +
            ' m² · Net tavan: ' +
            ceilingArea.toFixed(2) +
            ' m²';
    }


    if(totalArea <= 0){

        error(
            'Hesaplanabilir boyanacak alan bulunamadı.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | BOYA
    |--------------------------------------------------------------------------
    */

    const wallPaintLiters =
        wallArea > 0
            ? liters(
                wallArea,
                coats,
                coverage,
                waste
            )
            : 0;


    const ceilingPaintLiters =
        ceilingArea > 0
            ? liters(
                ceilingArea,
                coats,
                coverage,
                waste
            )
            : 0;


    const totalPaintLiters =
        wallPaintLiters +
        ceilingPaintLiters;


    /*
    |--------------------------------------------------------------------------
    | ASTAR
    |--------------------------------------------------------------------------
    */

    const primerCoverage =
        num('primerCoverage');


    if(
        $('includePrimer').checked &&
        (
            !Number.isFinite(primerCoverage) ||
            primerCoverage <= 0
        )
    ){

        error(
            'Astar sarfiyat değeri 0’dan büyük olmalıdır.'
        );

        return;
    }


    const primerLiters =
        $('includePrimer').checked
            ? liters(
                totalArea,
                1,
                primerCoverage,
                waste
            )
            : 0;


    /*
    |--------------------------------------------------------------------------
    | MALZEME MALİYETİ
    |--------------------------------------------------------------------------
    */

    let materialComplete = true;
    let materialTotalValue = 0;

    const wallBuckets =
        wallPaintLiters > 0
            ? Math.ceil(wallPaintLiters / 15)
            : 0;

    const ceilingBuckets =
        ceilingPaintLiters > 0
            ? Math.ceil(ceilingPaintLiters / 10)
            : 0;

    const primerBuckets =
        primerLiters > 0
            ? Math.ceil(primerLiters / 15)
            : 0;

    const coverPacks =
        floorArea !== null
            ? Math.ceil(floorArea / 10)
            : 0;

    const tapeMeters =
        floorArea !== null
            ? (
                2 * (
                    num('length') +
                    num('width')
                )
                +
                openingPerimeter
            )
            : 0;

    const tapeRolls =
        tapeMeters > 0
            ? Math.ceil(tapeMeters / 25)
            : 0;


    function addMaterial(required, price, quantity){

        if(!required) {
            return 0;
        }

        if(
            !Number.isFinite(Number(price)) ||
            Number(price) <= 0
        ){
            materialComplete = false;
            return 0;
        }

        const total =
            Number(price) * quantity;

        materialTotalValue += total;

        return total;
    }


    const wallPaintCost =
        addMaterial(
            wallBuckets > 0,
            MARKET.wallPaint,
            wallBuckets
        );


    const ceilingPaintCost =
        addMaterial(
            ceilingBuckets > 0,
            MARKET.ceilingPaint,
            ceilingBuckets
        );


    const primerCost =
        addMaterial(
            primerBuckets > 0,
            MARKET.primer,
            primerBuckets
        );


    const coverCost =
        addMaterial(
            coverPacks > 0,
            MARKET.cover,
            coverPacks
        );


    const tapeCost =
        addMaterial(
            tapeRolls > 0,
            MARKET.tape,
            tapeRolls
        );


    /*
    | Minimum uygulama seti:
    | 1 rulo + 1 fırça + 1 tava.
    | Büyük projelerde gerçek ekipman adedi saha planına göre artabilir.
    */

    const rollerCost =
        addMaterial(
            true,
            MARKET.roller,
            1
        );

    const brushCost =
        addMaterial(
            true,
            MARKET.brush,
            1
        );

    const trayCost =
        addMaterial(
            true,
            MARKET.tray,
            1
        );


    /*
    |--------------------------------------------------------------------------
    | İŞÇİLİK
    |--------------------------------------------------------------------------
    */

    const threshold =
        Number(
            SETTINGS.labor_large_threshold ||
            500
        );


    const standardRate =
        Number(
            SETTINGS.labor_standard_m2 ||
            250
        );


    const largeRate =
        Number(
            SETTINGS.labor_large_base_m2 ||
            175
        );


    const largeMin =
        Number(
            SETTINGS.labor_large_min_m2 ||
            150
        );


    const largeMax =
        Number(
            SETTINGS.labor_large_max_m2 ||
            200
        );


    const laborRate =
        totalArea > threshold
            ? largeRate
            : standardRate;


    const labor =
        totalArea *
        laborRate;


    /*
    |--------------------------------------------------------------------------
    | ERİŞİM
    |--------------------------------------------------------------------------
    */

    let accessCost = 0;

    let accessText =
        'Dahil değil';

    let accessWarning =
        '';


    const accessRequired =
        exterior ||
        (
            !exterior &&
            height > 3
        );


    if(accessRequired){

        const access =
            $('accessType').value;


        /*
        |--------------------------------------------------------------------------
        | DIŞ CEPHE
        |--------------------------------------------------------------------------
        */

        if(
            exterior &&
            access === 'scaffold'
        ){

            if(
                height > 51 &&
                Number(OFFICIAL.scaffoldB) > 0
            ){

                accessCost =
                    totalArea *
                    Number(
                        OFFICIAL.scaffoldB
                    );

                accessText =
                    money(accessCost);

            }else if(
                height > 3 &&
                Number(OFFICIAL.scaffoldA) > 0
            ){

                accessCost =
                    totalArea *
                    Number(
                        OFFICIAL.scaffoldA
                    );

                accessText =
                    money(accessCost);

            }else{

                accessText =
                    'Teklif ile';

                accessWarning =
                    'Seçilen cephe yüksekliğinde erişim bedeli otomatik eklenmedi.';
            }


        /*
        |--------------------------------------------------------------------------
        | İÇ MEKÂN > 3 M
        |--------------------------------------------------------------------------
        */

        }else if(
            !exterior &&
            access === 'scaffold'
        ){

            accessText =
                'Teklif ile';

            accessWarning =
                'İç mekân iskele bedeli saha koşullarına göre belirlenir; otomatik bütçeye eklenmedi.';


        }else if(
            access === 'platform'
        ){

            accessText =
                'Teklif ile';

            accessWarning =
                'Platform / vinç bedeli saha koşullarına göre ayrıca belirlenir.';


        }else if(
            access === 'unknown'
        ){

            accessText =
                'Belirlenmedi';

            accessWarning =
                exterior
                    ? 'Cephe erişim yöntemi seçilmedi. İskele veya platform maliyeti oluşabilir.'
                    : '3 m üzerindeki çalışma için erişim yöntemi seçilmelidir.';


        }else if(
            access === 'ladder'
        ){

            accessText =
                'Merdiven';

            accessWarning =
                'Merdiven erişimi seçildi; ilave erişim bedeli otomatik eklenmedi.';


        }else if(
            !exterior &&
            height > 3 &&
            access === 'normal'
        ){

            accessWarning =
                '3 m üzerindeki çalışma için erişim yöntemini kontrol edin.';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | BÜTÇE
    |--------------------------------------------------------------------------
    */

    const projectTotal =
        labor +
        accessCost +
        materialTotalValue;


    const tolerance =
        Number(
            SETTINGS.budget_tolerance ||
            .10
        );


    const low =
        projectTotal *
        (1 - tolerance);


    const high =
        projectTotal *
        (1 + tolerance);


    /*
    |--------------------------------------------------------------------------
    | RESULT
    |--------------------------------------------------------------------------
    */

    $('wallAreaResult').textContent =
        wallArea.toFixed(2) + ' m²';

    $('ceilingAreaResult').textContent =
        ceilingArea.toFixed(2) + ' m²';

    $('wallPaintResult').textContent =
        wallPaintLiters > 0
            ? wallPaintLiters.toFixed(2) +
              ' L · ' +
              wallBuckets +
              ' × 15 L'
            : 'Dahil değil';

    $('ceilingPaintResult').textContent =
        ceilingPaintLiters > 0
            ? ceilingPaintLiters.toFixed(2) +
              ' L · ' +
              ceilingBuckets +
              ' × 10 L'
            : 'Dahil değil';


    $('primerLiters').textContent =
        primerLiters > 0
            ? primerLiters.toFixed(2) + ' L'
            : 'Dahil değil';


    $('materialTotal').textContent =
        materialComplete
            ? money(materialTotalValue)
            : 'Eksik fiyat verisi';

    $('laborTotal').textContent =
        money(labor);


    $('laborRate').textContent =
        laborRate.toFixed(0) +
        ' TL/m²';


    $('accessCost').textContent =
        accessText;


    $('areaDetail').textContent =
        detail;


    $('budgetRange').textContent =
        money(low).replace(' TL','') +
        ' – ' +
        money(high);


    $('budgetTitle').textContent =
        materialComplete
            ? 'Tahmini toplam bütçe'
            : 'Kısmi tahmini bütçe';

    if(!materialComplete){
        $('budgetRange').textContent =
            'Malzeme fiyatı tamamlanıyor';
    }

    $('assumptionNote').textContent =
        'Varsayımlar: ' +
        coats +
        ' kat boya · ' +
        coverage +
        ' m²/L · %' +
        waste +
        ' fire.';

    /*
    |--------------------------------------------------------------------------
    | WHATSAPP TEKLİF MESAJI
    |--------------------------------------------------------------------------
    */

    const whatsappMessage =
        'Merhaba, WORKSITEM Boya Hesaplayıcı üzerinden hesap yaptım.\n\n' +
        'Uygulama: ' +
        (exterior ? 'Dış cephe' : 'İç cephe') +
        '\nDuvar / cephe alanı: ' +
        wallArea.toFixed(2) +
        ' m²' +
        '\nTavan alanı: ' +
        ceilingArea.toFixed(2) +
        ' m²' +
        '\nDuvar boya ihtiyacı: ' +
        wallPaintLiters.toFixed(2) +
        ' L' +
        '\nTavan boya ihtiyacı: ' +
        ceilingPaintLiters.toFixed(2) +
        ' L' +
        '\nTahmini bütçe: ' +
        $('budgetRange').textContent +
        '\n\nTeklif almak istiyorum.';


    const whatsappUrl =
        'https://wa.me/908503464983?text=' +
        encodeURIComponent(
            whatsappMessage
        );


    if($('whatsappCta')){
        $('whatsappCta').href =
            whatsappUrl;
    }


    if($('mobileWhatsappCta')){
        $('mobileWhatsappCta').href =
            whatsappUrl;
    }


    /*
    |--------------------------------------------------------------------------
    | MALZEME SEPETİ
    |--------------------------------------------------------------------------
    */

    $('shopPaint').textContent =
        wallPaintLiters > 0
            ? wallBuckets +
              ' × 15 L duvar boyası · ' +
              (
                  Number(MARKET.wallPaint) > 0
                      ? money(wallPaintCost)
                      : 'fiyat yok'
              )
            : 'Duvar boyası yok';

    if(ceilingPaintLiters > 0){

        $('shopPaint').textContent +=
            ' + ' +
            ceilingBuckets +
            ' × 10 L tavan boyası · ' +
            (
                Number(MARKET.ceilingPaint) > 0
                    ? money(ceilingPaintCost)
                    : 'fiyat yok'
            );
    }


    $('shopPrimer').textContent =
        primerLiters > 0
            ? primerBuckets +
              ' × 15 L · ' +
              (
                  Number(MARKET.primer) > 0
                      ? money(primerCost)
                      : 'fiyat yok'
              )
            : 'Bu hesapta seçilmedi';


    if(floorArea !== null){

        $('shopCover').textContent =
            coverPacks +
            ' × 10 m² paket · ' +
            (
                Number(MARKET.cover) > 0
                    ? money(coverCost)
                    : 'fiyat yok'
            );


        $('shopTape').textContent =
            tapeRolls +
            ' rulo · ' +
            tapeMeters.toFixed(2) +
            ' m çevre · ' +
            (
                Number(MARKET.tape) > 0
                    ? money(tapeCost)
                    : 'fiyat yok'
            );

    }else{

        $('shopCover').textContent =
            exterior ? 'Saha koşuluna göre belirlenir' : 'Oda ölçüsü girildiğinde hesaplanabilir';


        $('shopTape').textContent =
            exterior
        ? (
            openingPerimeter > 0
                ? 'Kapı / pencere çevresi ≈ ' +
                  openingPerimeter.toFixed(2) +
                  ' m · saha koşuluna göre'
                : 'Saha koşuluna göre belirlenir'
        )
        : 'Çevre ölçüsü girildiğinde hesaplanabilir';
    }


    $('shopToolSet').textContent =
        (
            Number(MARKET.roller) > 0 &&
            Number(MARKET.brush) > 0 &&
            Number(MARKET.tray) > 0
        )
        ? '1 rulo + 1 fırça + 1 tava · ' +
          money(
              rollerCost +
              brushCost +
              trayCost
          )
        : 'Fiyat verisi eksik';


    const surfaceState =
        $('surfaceState').value;


    $('shopPrep').textContent =
        (
            surfaceState === 'raw' ||
            surfaceState === 'rough'
        )
        ? 'Yüzey hazırlığı gerekebilir'
        : 'Gerekirse lokal uygulama';


    /*
    |--------------------------------------------------------------------------
    | NOTICES
    |--------------------------------------------------------------------------
    */

    const largeNotice =
        $('largeAreaNotice');


    if(totalArea > threshold){

        largeNotice.textContent =
            'Büyük metraj saha referansı: ' +
            largeMin.toFixed(0) +
            '–' +
            largeMax.toFixed(0) +
            ' TL/m². Hesaplamada ' +
            largeRate.toFixed(0) +
            ' TL/m² baz değer kullanıldı.';

        largeNotice
            .classList
            .remove('hidden');

    }else{

        largeNotice
            .classList
            .add('hidden');
    }


    const accessNotice =
        $('accessNotice');


    if(accessWarning){

        accessNotice.textContent =
            accessWarning;

        accessNotice
            .classList
            .remove('hidden');

    }else{

        accessNotice
            .classList
            .add('hidden');
    }


    const surfaceNotice =
        $('surfaceNotice');


    if(
        surfaceState === 'raw' ||
        surfaceState === 'rough'
    ){

        surfaceNotice.textContent =
            'Seçilen yüzeyde astar veya hazırlık gerekebilir. Doğrulanmış bir hazırlık katsayısı olmadığı için otomatik fiyat artışı uygulanmadı.';

        surfaceNotice
            .classList
            .remove('hidden');

    }else{

        surfaceNotice
            .classList
            .add('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW RESULT + MOBILE CTA
    |--------------------------------------------------------------------------
    */

    $('resultEmpty').style.display =
        'none';


    $('resultLive').style.display =
        'block';


    $('mobileStickyCta')
        .classList
        .add('active');


    $('mobileStickyCta')
        .setAttribute(
            'aria-hidden',
            'false'
        );


    if(window.innerWidth < 701){

        $('resultLive')
            .scrollIntoView({
                behavior:'smooth',
                block:'start'
            });
    }

});


syncApplication();
syncSurfaceScope();


/*
|--------------------------------------------------------------------------
| BOYA V2 — DESKTOP RESULT POSITION
|--------------------------------------------------------------------------
*/

(function(){

    const layout =
        document.querySelector('.layout');

    const resultPanel =
        document.querySelector('.result-panel');

    const details =
        document.querySelector('.result-more');


    if(
        layout &&
        resultPanel &&
        resultPanel.parentElement !== layout
    ){
        layout.appendChild(resultPanel);
    }


    /*
    | Malzeme listesi kullanıcı isterse açılır.
    */
    if(details){
        details.open = false;
    }

})();
</script>



<!-- PAINT_SEO_LLM_CONTENT_V2 -->

<section
    class="paint-seo-section first"
    aria-labelledby="boya-hesaplama-rehberi"
>
    <div class="paint-seo-card">

        <h2 id="boya-hesaplama-rehberi">
            Boya miktarı nasıl hesaplanır?
        </h2>

        <p>
            Boya ihtiyacını hesaplamak için önce net boyanacak yüzey alanı
            bulunur. Kapı ve pencere alanları duvar yüzeyinden düşülür.
            Ardından boya katı, ürünün teknik föyünde belirtilen
            m²/L sarfiyat değeri ve fire payı dikkate alınır.
        </p>

        <div class="paint-short-answer">

            <strong>Kısa cevap</strong>

            <p style="margin:0;">
                WORKSİTEM'in kullandığı temel hesap:
                <strong style="display:inline;color:var(--text);">
                    Boya (L) =
                    Boyanacak Alan × Kat Sayısı ÷ Sarfiyat ×
                    (1 + Fire Oranı)
                </strong>
            </p>

        </div>

        <h3>Duvar alanı nasıl hesaplanır?</h3>

        <div class="paint-formula">
            <strong>
                Brüt duvar alanı =
                2 × (Uzunluk + Genişlik) × Yükseklik
            </strong>

            <p style="margin:7px 0 0;">
                Net duvar alanı =
                Brüt duvar alanı − Kapı alanları − Pencere alanları
            </p>
        </div>

        <p>
            Tavan boyanacaksa tavan alanı ayrıca
            <strong style="color:var(--text);">
                Uzunluk × Genişlik
            </strong>
            formülüyle hesaplanır. WORKSİTEM duvar ve tavan boyasını
            ayrı gösterir; çünkü kullanılacak ürün ve ambalaj boyutu farklı olabilir.
        </p>

    </div>
</section>


<section
    class="paint-seo-section"
    aria-labelledby="boya-hesaplama-ornek"
>
    <div class="paint-seo-card">

        <h2 id="boya-hesaplama-ornek">
            Örnek boya hesabı: 5 × 4 metre oda
        </h2>

        <p>
            5 metre uzunluk, 4 metre genişlik ve 2,8 metre tavan yüksekliği
            olan bir oda düşünelim. Hesapta 1 adet 0,90 × 2,10 m kapı ve
            2 adet 1,20 × 1,50 m pencere bulunduğunu varsayalım.
        </p>

        <div class="paint-example-grid">

            <div class="paint-example-item">
                <span>Brüt duvar alanı</span>
                <strong>50,40 m²</strong>
            </div>

            <div class="paint-example-item">
                <span>Kapı + pencere düşümü</span>
                <strong>5,49 m²</strong>
            </div>

            <div class="paint-example-item">
                <span>Net duvar alanı</span>
                <strong>44,91 m²</strong>
            </div>

            <div class="paint-example-item">
                <span>Tavan alanı</span>
                <strong>20,00 m²</strong>
            </div>

        </div>

        <p>
            2 kat uygulama, 10 m²/L ürün sarfiyatı ve %10 fire
            varsayımında:
        </p>

        <div class="paint-formula">

            <p style="margin:0 0 7px;">
                <strong>Duvar boyası:</strong>
                44,91 × 2 ÷ 10 × 1,10 =
                yaklaşık <strong>9,88 L</strong>
            </p>

            <p style="margin:0;">
                <strong>Tavan boyası:</strong>
                20 × 2 ÷ 10 × 1,10 =
                yaklaşık <strong>4,40 L</strong>
            </p>

        </div>

        <p style="font-size:12px;">
            Bu örnekteki 10 m²/L ve %10 fire değerleri planlama
            varsayımıdır. Gerçek sarfiyat ürünün teknik föyüne,
            yüzey emiciliğine, uygulama yöntemine ve yüzey durumuna göre değişebilir.
        </p>

    </div>
</section>


<section
    class="paint-seo-section"
    aria-labelledby="bir-litre-boya"
>
    <div class="paint-seo-card">

        <h2 id="bir-litre-boya">
            1 litre, 10 litre ve 15 litre boya kaç m² boyar?
        </h2>

        <p>
            Tek bir sabit cevap yoktur. Boyanın örtücülüğü ürüne,
            yüzeye ve uygulama katına göre değişir.
            Aşağıdaki tablo yalnızca
            <strong style="color:var(--text);">
                10 m²/L · kat
            </strong>
            planlama varsayımını göstermektedir.
        </p>

        <div class="paint-seo-table-wrap">

            <table class="paint-seo-table">

                <thead>
                    <tr>
                        <th>Boya miktarı</th>
                        <th>1 kat</th>
                        <th>2 kat</th>
                        <th>2 kat + %10 fire</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td><strong>1 L</strong></td>
                        <td>≈ 10 m²</td>
                        <td>≈ 5 m²</td>
                        <td>≈ 4,5 m²</td>
                    </tr>

                    <tr>
                        <td><strong>10 L</strong></td>
                        <td>≈ 100 m²</td>
                        <td>≈ 50 m²</td>
                        <td>≈ 45 m²</td>
                    </tr>

                    <tr>
                        <td><strong>15 L</strong></td>
                        <td>≈ 150 m²</td>
                        <td>≈ 75 m²</td>
                        <td>≈ 68 m²</td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="paint-info-box">

            <strong>
                Ambalaj üzerindeki sarfiyat değerini kullanın
            </strong>

            <p style="margin:0;">
                WORKSİTEM'deki 10 m²/L başlangıç değeri değiştirilebilir.
                Satın alacağınız boyanın etiketi veya teknik föyünde farklı
                bir değer varsa hesaplayıcıya o değeri girmeniz daha doğru sonuç verir.
            </p>

        </div>

    </div>
</section>


<section
    class="paint-seo-section"
    aria-labelledby="100-m2-boya"
>
    <div class="paint-seo-card">

        <h2 id="100-m2-boya">
            100 m² alan için kaç litre boya gerekir?
        </h2>

        <p>
            Örneğin 100 m² net boyanacak alan, 2 kat uygulama,
            10 m²/L sarfiyat ve %10 fire varsayımında:
        </p>

        <div class="paint-formula">
            <strong>
                100 × 2 ÷ 10 × 1,10 = 22 litre
            </strong>
        </div>

        <p>
            Bu, yaklaşık sarfiyat hesabıdır. Satın alma sırasında
            boya ambalajları tam kova/adet olarak alınacağı için gerçek
            satın alma miktarı ürünün paket hacmine göre yukarı yuvarlanabilir.
        </p>


        <h2 style="font-size:22px;margin:30px 0 12px;">
            Boya hesabında kat sayısı neden önemlidir?
        </h2>

        <p>
            Aynı yüzeye 1 kat yerine 2 kat boya uygulanması teorik boya
            ihtiyacını yaklaşık iki katına çıkarır. Renk değişimi,
            yüzeyin mevcut durumu ve ürünün örtücülüğü uygulama kararını
            etkileyebilir.
        </p>


        <h2 style="font-size:22px;margin:30px 0 12px;">
            Fire oranı ne işe yarar?
        </h2>

        <p>
            Fire; rulo, fırça, tava, yüzey emiciliği ve uygulama sırasında
            oluşabilecek kayıplar için bırakılan planlama payıdır.
            WORKSİTEM başlangıçta %10 kullanır; kullanıcı bunu değiştirebilir.
            Bu oran her proje için zorunlu bir standart değildir.
        </p>

    </div>
</section>


<section
    class="paint-seo-section"
    aria-labelledby="astar-ve-yuzey"
>
    <div class="paint-seo-card">

        <h2 id="astar-ve-yuzey">
            Astar ne zaman hesaba dahil edilmelidir?
        </h2>

        <p>
            Astar ihtiyacı yüzeyin mevcut durumuna ve kullanılacak boya
            sistemine bağlıdır. Ham, emici, tamiratlı veya renk geçişi
            yapılacak yüzeylerde üreticinin önerdiği astar sistemi
            gerekebilir.
        </p>

        <p>
            WORKSİTEM astarı otomatik olarak zorunlu kabul etmez.
            Kullanıcı “Astarı ayrıca hesapla” seçeneğini açtığında
            1 kat astar ve girilen m²/L sarfiyat değeri üzerinden miktar hesabı yapar.
        </p>


        <h2 style="font-size:22px;margin:30px 0 12px;">
            Dış cephe boya hesabı nasıl yapılır?
        </h2>

        <p>
            Dış cephede brüt alan
            <strong style="color:var(--text);">
                cephe genişliği × cephe yüksekliği
            </strong>
            ile hesaplanır. Kapı ve pencere alanları bu değerden düşülür.
        </p>

        <p>
            Erişim yöntemi ayrıca değerlendirilir. İskele, merdiven veya
            platform ihtiyacı yalnız cephe yüksekliğine göre otomatik
            varsayılmaz; kullanıcı erişim yöntemini seçer.
        </p>

    </div>
</section>


<section
    class="paint-seo-section"
    aria-labelledby="boya-hatalari"
>
    <div class="paint-seo-card">

        <h2 id="boya-hatalari">
            Boya hesabında sık yapılan hatalar
        </h2>

        <div style="display:grid;gap:10px;">

            <div class="paint-example-item">
                <strong>1. Kapı ve pencereleri düşmemek</strong>
                <p style="margin:5px 0 0;">
                    Açıklık alanlarını düşmeden yapılan hesap boya miktarını gereğinden fazla gösterebilir.
                </p>
            </div>

            <div class="paint-example-item">
                <strong>2. Kat sayısını dikkate almamak</strong>
                <p style="margin:5px 0 0;">
                    Bir kat ve iki kat uygulamanın boya tüketimi aynı değildir.
                </p>
            </div>

            <div class="paint-example-item">
                <strong>3. Her boya için aynı m²/L değerini kullanmak</strong>
                <p style="margin:5px 0 0;">
                    Ürünün etiket veya teknik föy sarfiyatı esas alınmalıdır.
                </p>
            </div>

            <div class="paint-example-item">
                <strong>4. Tavan boyasını duvar boyasıyla karıştırmak</strong>
                <p style="margin:5px 0 0;">
                    WORKSİTEM tavan ve duvar miktarlarını ayrı hesaplar ve ayrı ürün olarak gösterir.
                </p>
            </div>

            <div class="paint-example-item">
                <strong>5. Net litre ile satın alınacak ambalajı karıştırmak</strong>
                <p style="margin:5px 0 0;">
                    Hesaplanan tüketim 9,88 litre olsa bile ürün 15 litrelik ambalajda satılıyorsa satın alma miktarı 1 adet 15 L olur.
                </p>
            </div>

        </div>


        <div class="paint-source-box">

            <h2 style="font-size:20px;margin:0 0 10px;">
                Kaynaklar ve hesaplama yöntemi
            </h2>

            <p>
                Alan hesapları temel geometrik formüllere dayanır.
                Boya sarfiyatı için kullanıcı tarafından girilen ürün etiketi /
                teknik föy m²/L değeri esas alınır.
            </p>

            <p>
                Malzeme fiyatları WORKSİTEM merkezi fiyat motorundaki
                güncel ve yayın onaylı piyasa kayıtlarından alınır.
                Doğrulanmış fiyat bulunmadığında sistem fiyat uydurmamalıdır.
            </p>

            <p>
                İşçilik bütçesi WORKSİTEM'de tanımlı güncel saha
                fiyat referansları üzerinden hesaplanır.
            </p>

            <p style="font-size:12px;margin:14px 0 0;">
                Son gözden geçirme: Ekim 2026 ·
                Hesaplama yaklaşımı: WORKSİTEM teknik hesaplama motoru
            </p>

        </div>

    </div>
</section>


<section
    class="paint-seo-section"
    aria-labelledby="boya-sss"
>
    <div class="paint-seo-card">

        <h2 id="boya-sss">
            Boya hesaplama hakkında sık sorulan sorular
        </h2>

        <div class="paint-faq">


            <details>
                <summary>
                    1 litre boya kaç m² boyar?
                </summary>

                <p>
                    Ürüne göre değişir. Örneğin 10 m²/L sarfiyat değerine
                    sahip bir boya teorik olarak tek katta yaklaşık 10 m²,
                    iki katta yaklaşık 5 m² yüzey için yeterlidir.
                </p>
            </details>


            <details>
                <summary>
                    15 litre boya kaç m² boyar?
                </summary>

                <p>
                    10 m²/L varsayımında 15 litre boya tek katta yaklaşık
                    150 m², iki katta yaklaşık 75 m² yüzey karşılığıdır.
                    %10 fire eklendiğinde iki kat için planlama alanı yaklaşık
                    68 m² olur.
                </p>
            </details>


            <details>
                <summary>
                    100 m² için kaç litre boya gerekir?
                </summary>

                <p>
                    2 kat, 10 m²/L sarfiyat ve %10 fire varsayımında
                    yaklaşık 22 litre boya gerekir.
                </p>
            </details>


            <details>
                <summary>
                    Kapı ve pencere alanı boya hesabından düşülür mü?
                </summary>

                <p>
                    Boyanmayacak kapı ve pencere yüzeyleri duvar alanından
                    düşülmelidir. WORKSİTEM kapı ve pencere adetleri ile
                    ölçülerini kullanarak bu düşümü otomatik yapar.
                </p>
            </details>


            <details>
                <summary>
                    Tavan için ayrı boya hesabı yapılır mı?
                </summary>

                <p>
                    Evet. Tavan alanı oda uzunluğu × oda genişliği ile
                    ayrı hesaplanır ve tavan boya miktarı duvar boyasından
                    ayrı gösterilir.
                </p>
            </details>


            <details>
                <summary>
                    Boya hesabında fire oranı kaç olmalı?
                </summary>

                <p>
                    Tek bir zorunlu oran yoktur. WORKSİTEM başlangıçta
                    %10 planlama varsayımı kullanır ve kullanıcı bu değeri
                    proje koşullarına göre değiştirebilir.
                </p>
            </details>


            <details>
                <summary>
                    Astar her boya işinde gerekli midir?
                </summary>

                <p>
                    Hayır. Astar ihtiyacı yüzey durumuna ve kullanılacak
                    ürün sistemine bağlıdır. Üreticinin teknik föyü ve
                    saha koşulları dikkate alınmalıdır.
                </p>
            </details>


            <details>
                <summary>
                    Boya maliyeti nasıl hesaplanıyor?
                </summary>

                <p>
                    WORKSİTEM boya ve yardımcı malzemelerin güncel piyasa
                    fiyatlarını, hesaplanan ambalaj adetlerini ve işçilik
                    referansını kullanarak yaklaşık bütçe oluşturur.
                    Saha koşulları nedeniyle nihai teklif farklı olabilir.
                </p>
            </details>


            <details>
                <summary>
                    Dış cephe boya hesabı nasıl yapılır?
                </summary>

                <p>
                    Cephe genişliği × cephe yüksekliği ile brüt cephe alanı
                    bulunur; kapı ve pencere alanları düşülür.
                    Erişim veya iskele ihtiyacı ayrıca değerlendirilir.
                </p>
            </details>


        </div>


        <h2 style="font-size:22px;margin:32px 0 14px;">
            Boya ve tadilat için kullanabileceğiniz diğer araçlar
        </h2>


        <div class="paint-related">

            <a href="/araclar/duvar-rengi-gorsellestirici">
                <strong>
                    Duvar Rengi Görselleştirici →
                </strong>

                <span>
                    Boya rengini seçmeden önce farklı renkleri duvar üzerinde görselleştirin.
                </span>
            </a>


            <a href="/araclar/duvar-hesaplayici">
                <strong>
                    Duvar Hesaplayıcı →
                </strong>

                <span>
                    Duvar ölçüsü, alan ve malzeme ihtiyacınızı hesaplayın.
                </span>
            </a>


            <a href="/araclar/beton-hesaplayici">
                <strong>
                    Beton Hesaplayıcı →
                </strong>

                <span>
                    Beton hacmi, sipariş miktarı ve yaklaşık bütçeyi hesaplayın.
                </span>
            </a>


            <a href="/araclar">
                <strong>
                    Tüm İnşaat ve Tadilat Araçları →
                </strong>

                <span>
                    WORKSİTEM'deki tüm hesaplama ve metraj araçlarını görüntüleyin.
                </span>
            </a>

        </div>

    </div>
</section>


<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@graph": [
        {
            "@type": "WebApplication",
            "@id": "https://worksitem.com/araclar/boya-hesaplayici#app",
            "name": "WORKSİTEM Boya Hesaplayıcı",
            "url": "https://worksitem.com/araclar/boya-hesaplayici",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "Web",
            "description": "Duvar ve tavan için boya miktarı, kapı-pencere düşümü, fire, yaklaşık malzeme ve işçilik bütçesi hesaplama aracı."
        },
        {
            "@type": "BreadcrumbList",
            "@id": "https://worksitem.com/araclar/boya-hesaplayici#breadcrumb",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "WORKSİTEM",
                    "item": "https://worksitem.com/"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Araçlar",
                    "item": "https://worksitem.com/araclar"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "Boya Hesaplayıcı",
                    "item": "https://worksitem.com/araclar/boya-hesaplayici"
                }
            ]
        },
        {
            "@type": "FAQPage",
            "@id": "https://worksitem.com/araclar/boya-hesaplayici#faq",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "1 litre boya kaç m² boyar?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Ürüne göre değişir. 10 m²/L sarfiyat değerine sahip bir boya teorik olarak tek katta yaklaşık 10 m², iki katta yaklaşık 5 m² yüzey için yeterlidir."
                    }
                },
                {
                    "@type": "Question",
                    "name": "15 litre boya kaç m² boyar?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "10 m²/L varsayımında 15 litre boya tek katta yaklaşık 150 m², iki katta yaklaşık 75 m² yüzey karşılığıdır. Yüzde 10 fire ile iki kat için planlama alanı yaklaşık 68 m² olur."
                    }
                },
                {
                    "@type": "Question",
                    "name": "100 m² için kaç litre boya gerekir?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "2 kat, 10 m²/L sarfiyat ve yüzde 10 fire varsayımında yaklaşık 22 litre boya gerekir."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Kapı ve pencere alanı boya hesabından düşülür mü?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Boyanmayacak kapı ve pencere yüzeyleri duvar alanından düşülmelidir. WORKSİTEM bu düşümü adet ve ölçülere göre otomatik hesaplar."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Tavan için ayrı boya hesabı yapılır mı?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Evet. Tavan alanı oda uzunluğu ile oda genişliğinin çarpımıyla ayrı hesaplanır ve tavan boya miktarı duvar boyasından ayrı gösterilir."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Dış cephe boya hesabı nasıl yapılır?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Cephe genişliği ile cephe yüksekliği çarpılarak brüt cephe alanı bulunur. Kapı ve pencere alanları düşülür ve erişim yöntemi ayrıca değerlendirilir."
                    }
                }
            ]
        }
    ]
}
</script>


<x-calc.kit :dock="false" />{{-- boyanın kendi mobil teklif çubuğu var --}}
<x-site.contech-footer />
<script src="/js/p1-product-events.js" defer></script>
</body>
</html>
