@php
    $title = 'Firma paneli';
    $statusOf = function ($inv) use ($offers) {
        $o = $offers->get($inv->job_request_id);
        if ($o) return [\App\Models\JobOffer::STATUSES[$o->status] ?? $o->status, in_array($o->status, ['accepted', 'matched'], true) ? 'y' : ($o->status === 'submitted' ? 'g' : '')];
        if ($inv->status === 'declined') return ['İlgilenmiyorum', ''];
        if (! $inv->job->isOpen()) return ['Kapandı', ''];
        return ['Teklif bekleniyor', 'y'];
    };
@endphp
@include('market._layout-top')
<main class="mk-wrap">
    @include('market._panel-head')
    <x-market.app-card />
    <div class="mk-card" style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center">
        <div class="mk-kv">
            <span>İş kolları</span><b>{{ implode(', ', $firm->serviceLabels()) ?: '—' }}</b>
            <span>İller</span><b>{{ implode(', ', (array) $firm->cities) }}{{ $firm->districts ? ' · yalnız: '.implode(', ', (array) $firm->districts) : '' }}</b>
            <span>Teklif / eşleşme</span><b>{{ $firm->offers_count }} / {{ $firm->matches_count }}</b>
        </div>
        <form method="POST" action="{{ route('market.panel.toggle') }}">@csrf<button class="fm-btn {{ $firm->accepts_jobs ? 'o' : '' }}" type="submit">{{ $firm->accepts_jobs ? 'Yeni iş davetlerini durdur' : 'Yeni iş davetlerini aç' }}</button></form>
    </div>
    @if (! $firm->answers)
        <div class="mk-note" style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center"><span><b>Size daha uygun işler gönderelim:</b> 1 dakikalık hizmet bilgilerini doldurun (en küçük iş, ekip, fiyatlarınız).</span><a class="fm-btn" href="{{ route('market.panel.info') }}">Doldur</a></div>
    @else
        <p style="margin:0 0 14px;font-size:14px"><a href="{{ route('market.panel.info') }}" style="color:inherit;font-weight:700">Hizmet bilgilerini güncelle</a>@if ($firm->min_job) · en küçük iş: {{ number_format($firm->min_job, 0, ',', '.') }} TL @endif</p>
    @endif
    <div class="mk-card">
        <h2>Size gelen işler</h2>
        @if ($invites->isEmpty())
            <p style="color:var(--muted);margin:0">Henüz davet yok. İş kolunuzda ve bölgenizde bir kullanıcı teklif istediğinde SMS ile (bildirimleri açtıysanız uygulamadan da) haber vereceğiz.</p>
        @else
            <table class="mk-table">
                <thead><tr><th>İş</th><th>Yer</th><th>Yaklaşık</th><th>Durum</th></tr></thead>
                <tbody>
                @foreach ($invites as $inv)
                    @php([$label, $c] = $statusOf($inv))
                    <tr class="mk-row">
                        <td><a href="{{ route('market.panel.job', $inv->job) }}">{{ $inv->job->title }}</a><br><small style="color:var(--muted)">{{ $inv->job->public_id }} · {{ $inv->created_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</small></td>
                        <td>{{ $inv->job->location() }}</td>
                        <td>{{ $inv->job->range() ?: '—' }}</td>
                        <td><span class="mk-tag {{ $c }}">{{ $label }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @if (\App\Services\Market\Marketplace::isFree())
        <p class="lead" style="font-size:13px"><b>Kurucu firma programı:</b> {{ \App\Services\Market\Marketplace::freeUntil()->format('d.m.Y') }} tarihine kadar eşleşme bedeli alınmaz. Bu tarihten sonra kabul edilen tekliflerde eşleşme bedeli: teklif tutarının %{{ rtrim(rtrim(number_format(config('worksitem_market.fee_rate') * 100, 1, ',', ''), '0'), ',') }}'i, en az {{ number_format(config('worksitem_market.fee_min'), 0, ',', '.') }} TL, en çok {{ number_format(config('worksitem_market.fee_max'), 0, ',', '.') }} TL + KDV. <a href="{{ route('legal.firma_uyelik') }}" style="color:inherit">Sözleşme</a></p>
    @else
    <p class="lead" style="font-size:13px">Eşleşme bedeli: kabul edilen teklif tutarının %{{ rtrim(rtrim(number_format(config('worksitem_market.fee_rate') * 100, 1, ',', ''), '0'), ',') }}'i, en az {{ number_format(config('worksitem_market.fee_min'), 0, ',', '.') }} TL, en çok {{ number_format(config('worksitem_market.fee_max'), 0, ',', '.') }} TL + KDV. Faturanız {{ $firm->invoiceType() }} olarak {{ $firm->invoiceTitle() }} adına kesilir. <a href="{{ route('legal.firma_uyelik') }}" style="color:inherit">Sözleşme</a></p>
    @endif
</main>
<x-site.contech-footer />
</body>
</html>
