@extends('poz._layout')

@section('title', 'İnşaat Pozları — 43.290+ Resmî Rayiç ve Birim Fiyat | WORKSİTEM')
@section('description', 'ÇŞB, DSİ, Karayolları ve 12 kurumdan toplam 43.290+ inşaat pozu. 2026 güncel birim fiyatları, detaylı açıklamalar, SSS.')
@section('canonical', url('/pozlar'))
@section('keywords', 'inşaat pozları, rayiç bedeli, birim fiyat, ÇŞB poz, YFK poz, 2026 inşaat maliyeti')

@section('breadcrumb')
  <span>Pozlar</span>
@endsection

@push('head')
<style>
.poz-search-hero{
    padding:34px 0 18px;
}
.poz-search-hero h1{
    margin:0;
    max-width:900px;
    font-size:clamp(38px,5vw,62px);
    line-height:1.02;
    letter-spacing:-2.4px;
    font-weight:950;
}
.poz-search-hero h1 span{color:var(--yellow-dark)}
.poz-search-hero p{
    max-width:760px;
    margin:14px 0 0;
    color:var(--muted);
    font-size:16px;
    line-height:1.7;
}

.poz-search-panel{
    margin-top:24px;
    padding:20px;
    border:1px solid var(--line);
    border-radius:20px;
    background:var(--surface);
    box-shadow:var(--shadow);
}
.poz-search-main{
    display:grid;
    grid-template-columns:minmax(0,2fr) minmax(220px,.75fr) auto;
    gap:10px;
}
.poz-search-main input,
.poz-search-main select{
    width:100%;
    min-height:58px;
    padding:0 18px;
    border:1px solid var(--line);
    border-radius:13px;
    background:var(--surface-2);
    color:var(--text);
    font-size:15px;
}
.poz-search-main input:focus,
.poz-search-main select:focus{
    outline:none;
    border-color:var(--yellow);
    box-shadow:0 0 0 4px rgba(255,184,0,.12);
}
.poz-search-submit{
    min-height:58px;
    padding:0 28px;
    border:0;
    border-radius:13px;
    background:var(--yellow);
    color:#080808;
    font-weight:950;
    cursor:pointer;
}

.poz-keywords{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:8px;
    margin-top:16px;
}
.poz-keywords-label{
    margin-right:3px;
    color:var(--muted);
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.7px;
}
.poz-keyword{
    display:inline-flex;
    align-items:center;
    min-height:34px;
    padding:0 11px;
    border:1px solid var(--line);
    border-radius:999px;
    background:var(--surface);
    color:var(--text);
    font-size:11px;
    font-weight:800;
}
.poz-keyword:hover{
    border-color:var(--yellow);
    background:rgba(255,184,0,.08);
}

.poz-search-info{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
    margin-top:18px;
}
.poz-search-stat{
    padding:16px 18px;
    border:1px solid var(--line);
    border-radius:15px;
    background:var(--surface);
}
.poz-search-stat strong{
    display:block;
    font-size:20px;
    line-height:1;
    font-weight:950;
}
.poz-search-stat span{
    display:block;
    margin-top:6px;
    color:var(--muted);
    font-size:10px;
    font-weight:850;
    text-transform:uppercase;
    letter-spacing:.6px;
}

.poz-empty-search{
    margin-top:18px;
    padding:52px 28px;
    text-align:center;
    border:1px dashed var(--line);
    border-radius:20px;
    background:var(--surface);
}
.poz-empty-search-icon{
    width:56px;
    height:56px;
    margin:0 auto 15px;
    display:grid;
    place-items:center;
    border-radius:50%;
    background:rgba(255,184,0,.13);
    font-size:24px;
}
.poz-empty-search h2{
    margin:0;
    font-size:24px;
    font-weight:950;
}
.poz-empty-search p{
    max-width:590px;
    margin:8px auto 0;
    color:var(--muted);
    font-size:13px;
}

.poz-results-head{
    display:flex;
    align-items:end;
    justify-content:space-between;
    gap:16px;
    margin:30px 0 12px;
}
.poz-results-head h2{
    margin:0;
    font-size:24px;
    font-weight:950;
}
.poz-results-head p{
    margin:4px 0 0;
    color:var(--muted);
    font-size:12px;
}
.poz-clear{
    color:var(--yellow-dark);
    font-size:12px;
    font-weight:900;
}

@media(max-width:760px){
    .poz-search-main{grid-template-columns:1fr}
    .poz-search-info{grid-template-columns:1fr}
    .poz-results-head{align-items:flex-start;flex-direction:column}
}
</style>
@endpush

@section('content')

@php
    $hasSearch = trim((string)$search) !== ''
        || ($institution && $institution !== 'all');
@endphp

<section class="poz-search-hero">
    <h1>İnşaat pozunu <span>bulun.</span></h1>
    <p>
        43.290+ resmî poz arasında poz numarası veya imalat adıyla arama yapın.
        Kuruma göre filtreleyin ve kayıtlı birim fiyat detayına ulaşın.
    </p>

    <form class="poz-search-panel" method="GET" action="/pozlar">
        <div class="poz-search-main">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Örn. 15.530.1251, alçıpan, seramik, beton, boya..."
                aria-label="Poz no veya tanım ara"
            >

            <select name="kurum" aria-label="Kurum seç">
                <option value="all">Tüm Kurumlar</option>
                @foreach($institutions as $inst)
                    <option
                        value="{{ $inst }}"
                        {{ $institution === $inst ? 'selected' : '' }}
                    >
                        {{ $inst }}
                    </option>
                @endforeach
            </select>

            <button class="poz-search-submit" type="submit">
                Ara
            </button>
        </div>

        <div class="poz-keywords">
            <span class="poz-keywords-label">Sık arananlar</span>

            @foreach([
                'beton',
                'kalıp',
                'donatı',
                'alçıpan',
                'seramik',
                'boya',
                'yalıtım',
                'elektrik',
                'mekanik',
                'çatı'
            ] as $keyword)
                <a
                    class="poz-keyword"
                    href="/pozlar?q={{ urlencode($keyword) }}"
                >
                    {{ $keyword }}
                </a>
            @endforeach
        </div>
    </form>

    <div class="poz-search-info">
        <div class="poz-search-stat">
            <strong>43.290+</strong>
            <span>Resmî poz</span>
        </div>

        <div class="poz-search-stat">
            <strong>12</strong>
            <span>Kaynak kurum</span>
        </div>

        <div class="poz-search-stat">
            <strong>2026</strong>
            <span>Güncel referans yılı</span>
        </div>
    </div>
</section>

@if($hasSearch)

    <div class="poz-results-head">
        <div>
            <h2>Arama sonuçları</h2>

            <p>
                <strong>{{ number_format($pozs->total(), 0, ',', '.') }}</strong>
                kayıt bulundu

                @if(trim((string)$search) !== '')
                    · “{{ $search }}”
                @endif

                @if($institution && $institution !== 'all')
                    · {{ $institution }}
                @endif
            </p>
        </div>

        <a class="poz-clear" href="/pozlar">
            Aramayı temizle
        </a>
    </div>

    @if($pozs->count())

        <div class="poz-list">
            @foreach($pozs as $poz)
            <div class="poz-item">
                <a href="{{ route('poz.show', $poz->poz_no) }}">
                    <span class="poz-code">{{ $poz->poz_no }}</span>

                    <span>
                        {{ \Illuminate\Support\Str::limit($poz->description, 100) }}
                    </span>
                </a>

                <span class="poz-price">
                    {{ number_format((float)$poz->unit_price, 2, ',', '.') }}
                    TL/{{ $poz->unit }}
                </span>
            </div>
            @endforeach
        </div>

        @if($pozs->hasPages())
        <nav class="poz-pagination" aria-label="Poz sayfaları">

            @if($pozs->onFirstPage())
                <span class="poz-page disabled">← Önceki</span>
            @else
                <a
                    class="poz-page"
                    href="{{ $pozs->previousPageUrl() }}"
                    rel="prev"
                >
                    ← Önceki
                </a>
            @endif

            <span class="poz-page-info">
                Sayfa {{ $pozs->currentPage() }}
                /
                {{ $pozs->lastPage() }}
            </span>

            @if($pozs->hasMorePages())
                <a
                    class="poz-page"
                    href="{{ $pozs->nextPageUrl() }}"
                    rel="next"
                >
                    Sonraki →
                </a>
            @else
                <span class="poz-page disabled">
                    Sonraki →
                </span>
            @endif

        </nav>
        @endif

    @else

        <div class="poz-empty-search">
            <div class="poz-empty-search-icon">⌕</div>
            <h2>Sonuç bulunamadı</h2>
            <p>
                Daha kısa bir imalat adı, farklı bir poz numarası
                veya başka bir kurum filtresi deneyin.
            </p>
        </div>

    @endif

@else

    <div class="poz-empty-search">
        <div class="poz-empty-search-icon">⌕</div>

        <h2>Aramak istediğiniz pozu yazın.</h2>

        <p>
            Poz listesini tek tek gezmek yerine poz numarası,
            malzeme veya imalat adıyla arayın.
            Örneğin “alçıpan”, “beton”, “seramik” veya
            “15.530.1251”.
        </p>
    </div>

@endif

@endsection