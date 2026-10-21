{{--
    "Neden Worksitem?" — kitleye göre değer önerisi. Yalnızca bugün doğru olan söylenir.
    Kullanım: <x-site.why audience="all" /> (anasayfa: üç kitle) · "users" · "firms" · "brands"
--}}
@props(['audience' => 'all'])
@php
    $free = \App\Services\Market\Marketplace::isFree();
    $freeUntil = \App\Services\Market\Marketplace::freeUntil()?->format('d.m.Y');
    $tools = collect((array) config('worksitem_tools.tools'))->keys()
        ->filter(fn ($s) => (((array) config('worksitem_tools.tool_status.overrides'))[$s] ?? config('worksitem_tools.tool_status.default')) === 'active')->count();
    $groups = [
        'users' => [
            'kicker' => 'Ev sahipleri ve proje sahipleri için',
            'title' => 'Ustayla konuşmadan önce bütçenizi bilin.',
            'cta' => ['Ücretsiz hesapla', '/araclar'],
            'items' => [
                [($tools >= 10 ? $tools : 'Onlarca').' ücretsiz araç', 'Banyo, mutfak, boya, seramik, çatı, inşaat maliyeti, metraj ve 2D/3D planlayıcılar. Üyelik ve ödeme yok.'],
                ['Güncel ve tarihli fiyat', 'Resmî birim fiyatlar ve piyasa araştırması her sabah güncellenir, uzman kontrol eder. Her raporda fiyat tarihi yazar.'],
                ['Tarafsız sonuç', 'Sponsorlar hesabı ve fiyatları etkilemez; sponsorlu içerik her zaman etiketlidir.'],
                ['Kontrol sizde', 'Telefonunuz izniniz olmadan kimseyle paylaşılmaz. Teklif istemek zorunlu değildir.'],
            ],
        ],
        'firms' => [
            'kicker' => 'Usta, firma ve tedarikçiler için',
            'title' => 'Bütçesini bilen müşteri, doğru firmayı arıyor.',
            'cta' => ['Ücretsiz firma kaydı', route('firms.page')],
            'items' => [
                ['Hazırlıklı müşteri', 'Talepler maliyetini hesaplamış, telefonunu SMS ile doğrulamış kullanıcılardan gelir; iş, ilçe ve yaklaşık bütçe baştan bellidir.'],
                [$free ? 'Komisyon yok' : 'Yalnız kazanınca ödersiniz', $free ? "Kurucu firma programı: $freeUntil tarihine kadar eşleşme bedeli alınmaz. Teklif vermek her zaman ücretsiz." : 'Teklif vermek ücretsiz; yalnız müşteri sizi seçerse eşleşme bedeli ödersiniz.'],
                ['Size uygun iş', 'Yalnız iş kolunuza, ilinize/ilçenize uyan ve en küçük iş tutarınızın üstündeki talepler gelir.'],
                ['Kayıtlı firmalar arasında', 'Her firmanın vergi kaydı ve vergi levhası kontrol edilir; telefondan, şifresiz panelden teklif verirsiniz.'],
            ],
        ],
        'pilot' => [
            'kicker' => \App\Support\Pilot::areaLabel().'\'da keşif ve uygulama',
            'title' => 'Hesapladığınız işi '.\App\Support\Pilot::provider().' yapsın.',
            'cta' => ['Yerinde keşif iste', '/maliyet-hesapla'],
            'items' => [
                ['Yerinde keşif', 'Kainta İnşaat uzmanı yerinde ölçer, ihtiyacı netleştirir. Keşif bedeli ve koşulları teklif öncesinde açıkça yazılır.'],
                ['Yazılı teklif', 'Keşiften sonra '.(int) config('worksitem_inspection.report_days', 5).' iş günü içinde rapor ve kalem kalem yazılı teklif.'],
                ['Tek muhatap', 'Keşif, teklif, uygulama ve fatura aynı firmadan; sözleşmede hizmeti veren taraf açıkça yazar.'],
                ['Pilot bölge', \App\Support\Pilot::areaLabel().' dışından gelen talepleri kaydediyoruz; bölgenize açıldığımızda haber veriyoruz.'],
            ],
        ],
        'brands' => [
            'kicker' => 'Markalar ve üreticiler için',
            'title' => 'Ürününüz, malzeme seçilmeden hemen önce görünsün.',
            'cta' => ['Sponsorluk teklifi iste', route('sponsor.page').'#basvuru'],
            'items' => [
                ['Karar anı', 'Kartınız hesap sonucunun altında, ilgili kalemin yanında çıkar: banyo › seramik, boya › iç cephe gibi.'],
                ['Doğru kitle', 'Bütçesini hesaplayan ev sahipleri ile bu araçları kullanan usta, firma ve mühendisler.'],
                ['Güvenilir ortam', '"Sponsorlu" etiketi ve sonuçların sponsorluktan etkilenmemesi, kartınızı reklam değil öneri gibi okutur.'],
                ['Ölçülebilir', 'Gösterim, tıklama ve tıklama oranı aylık raporlanır; kalem başına en fazla 3 marka gösterilir.'],
            ],
        ],
    ];
    if (\App\Support\Pilot::firmSignupOpen()) {
        unset($groups['pilot']);
    } else {
        $groups = ['users' => $groups['users'], 'pilot' => $groups['pilot'], 'brands' => $groups['brands']];
    }
    $show = $audience === 'all' ? $groups : array_intersect_key($groups, [$audience => 1]);
@endphp
<section class="ws-why {{ $audience === 'all' ? 'all' : 'one' }}" aria-labelledby="wsWhy-{{ $audience }}" data-kainta-auto-link="off">
    @if ($audience === 'all')
        <div class="ws-why-head">
            <span class="ws-why-k">Neden Worksitem?</span>
            @if (\App\Support\Pilot::firmSignupOpen())
            <h2 id="wsWhy-{{ $audience }}">Hesaplayan kazanır, iş yapan kazanır, ürün üreten kazanır.</h2>
            <p>Kullanıcı bütçesini görür; doğru bütçeyle doğru firmaya ulaşır; markalar kararın verildiği anda görünür; sahadan gelen fiyatlar hesapları daha da isabetli yapar.</p>
            @else
            <h2 id="wsWhy-{{ $audience }}">Bütçenizi bilin, işi tek elden yaptırın.</h2>
            <p>Araçlarla maliyetinizi ücretsiz hesaplarsınız. {{ \App\Support\Pilot::areaLabel() }}'daysanız keşif ve uygulamayı {{ \App\Support\Pilot::provider() }} üstlenir. Markalar kararın verildiği anda görünür.</p>
            @endif
        </div>
    @else
        <h2 id="wsWhy-{{ $audience }}" class="ws-why-one">Neden Worksitem?</h2>
    @endif
    <div class="ws-why-grid">
        @foreach ($show as $key => $g)
            <article class="ws-why-col">
                <span class="ws-why-k">{{ $g['kicker'] }}</span>
                <h3>{{ $g['title'] }}</h3>
                <ul>@foreach ($g['items'] as [$h, $p])<li><b>{{ $h }}</b><span>{{ $p }}</span></li>@endforeach</ul>
                @if ($audience === 'all')<a class="ws-why-btn" href="{{ $g['cta'][1] }}">{{ $g['cta'][0] }} →</a>@endif
            </article>
        @endforeach
    </div>
</section>
@once
<style>
.ws-why{font-family:Inter,-apple-system,'Segoe UI',sans-serif;color:#101318;text-align:left}
.ws-why.all{max-width:1180px;margin:0 auto;padding:48px 20px}
.ws-why.one{margin:0 0 8px}
.ws-why-head{max-width:820px;margin-bottom:22px}
.ws-why-head h2{font-size:clamp(24px,3vw,34px);letter-spacing:-.02em;line-height:1.15;margin:6px 0 10px}
.ws-why-head p{color:#5B6472;font-size:16px;line-height:1.6;margin:0}
.ws-why-one{font-size:clamp(22px,2.8vw,30px);letter-spacing:-.02em;margin:0 0 14px}
.ws-why-k{display:inline-block;font-size:11.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#8A6200}
.ws-why-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}
.ws-why.one .ws-why-grid{grid-template-columns:1fr}
.ws-why-col{background:#fff;border:1px solid #E3E6EA;border-radius:18px;padding:22px;display:flex;flex-direction:column}
.ws-why-col h3{font-size:18px;line-height:1.3;margin:6px 0 12px}
.ws-why-col ul{list-style:none;margin:0 0 16px;padding:0;display:grid;gap:10px}
.ws-why.one .ws-why-col ul{grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px 18px;margin:0}
.ws-why-col li{display:grid;grid-template-columns:22px 1fr;gap:4px 8px;line-height:1.5}
.ws-why-col li:before{content:"✓";grid-row:span 2;width:20px;height:20px;border-radius:50%;background:#FFF8E1;color:#8A6200;font-weight:900;font-size:12px;display:flex;align-items:center;justify-content:center;margin-top:1px}
.ws-why-col li b{font-size:14.5px}
.ws-why-col li span{font-size:13.5px;color:#5B6472}
.ws-why-btn{margin-top:auto;align-self:flex-start;background:#FFB800;color:#101318;font-weight:800;font-size:14px;text-decoration:none;padding:11px 16px;border-radius:12px}
html[data-theme="dark"] .ws-why{color:#E6E9EE}
html[data-theme="dark"] .ws-why-col{background:#141A24;border-color:#232C3A}
html[data-theme="dark"] .ws-why-col li span,html[data-theme="dark"] .ws-why-head p{color:#C9CFD8}
html[data-theme="dark"] .ws-why-col li:before{background:#2A2410}
</style>
@endonce
