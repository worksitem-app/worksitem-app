@php
    $title = $job->public_id.' · Firma paneli';
    $f = fn ($v) => number_format((float) $v, 0, ',', '.');
    $fee = $offer && $offer->fee_net ? $offer->fee_net : \App\Services\Market\Marketplace::fee((int) ($offer->amount ?? $job->est_high ?? 0));
@endphp
@include('market._layout-top')
<main class="mk-wrap">
    @include('market._panel-head')
    <div class="mk-card">
        <small style="color:var(--muted)">{{ $job->public_id }} · {{ $job->created_at->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</small>
        <h2 style="font-size:22px;margin-top:4px">{{ $job->title }}</h2>
        <div class="mk-kv" style="margin-top:10px">
            <span>İş kolu</span><b>{{ $job->tradeLabel() }}{{ $job->subservice ? ' · '.$job->subservice : '' }}</b>
            <span>Yer</span><b>{{ $job->location() }}</b>
            @if ($job->range())<span>Kullanıcının hesabı</span><b>{{ $job->range() }} <small style="color:var(--muted);font-weight:500">(Worksitem aracı · bilgi amaçlı)</small></b>@endif
            @if ($job->summary)<span>Hesap özeti</span><b style="font-weight:500">{{ $job->summary }}</b>@endif
            <span>Başlangıç</span><b>{{ config("worksitem_market.start_prefs.{$job->start_pref}") }}</b>
            <span>Müşteri</span><b>{{ $offer && $offer->status === 'matched' ? $job->name : $job->firstName().' · '.$job->district }}</b>
            <span>Diğer teklifler</span><b>{{ max(0, $competitors - ($offer && in_array($offer->status, ['submitted', 'accepted'], true) ? 1 : 0)) }}</b>
        </div>
        @if ($job->description)<p style="margin:12px 0 0;white-space:pre-line;line-height:1.6">{{ \App\Services\Market\Marketplace::mask($job->description) }}</p>@endif
    </div>

    @if ($offer && $offer->status === 'matched')
        <div class="mk-card" style="border-color:#16A34A">
            <h2>✓ Eşleşme tamam: müşteri bilgileri</h2>
            <div class="mk-kv" style="margin-top:8px">
                <span>Ad soyad</span><b>{{ $job->name }}</b>
                <span>Telefon</span><b><a href="tel:+90{{ $job->phone }}" style="color:inherit">0{{ $job->phone }}</a></b>
                @if ($job->email)<span>E-posta</span><b>{{ $job->email }}</b>@endif
                <span>Yer</span><b>{{ $job->location() }}</b>
            </div>
            <div class="mk-note" style="margin:14px 0 0">Müşteriyi 24 saat içinde arayın. İşe başlamadan yazılı sözleşme yapın ve faturanızı kesin. Müşteriye 72 saat içinde hiçbir şekilde ulaşamazsanız WhatsApp hattımıza yazın; doğrulanırsa eşleşme bedeli iade edilir.</div>
        </div>
    @elseif ($offer && $offer->status === 'accepted' && (float) $offer->fee_net <= 0)
        <div class="mk-card" style="border-color:var(--y)">
            <h2>Müşteri teklifinizi kabul etti 🎉</h2>
            <p style="color:var(--muted);line-height:1.6;margin:6px 0 12px">Kurucu firma programı kapsamında eşleşme bedeli yok. Eşleşmeyi onayladığınızda müşterinin adı ve telefonu açılır, müşteriye de sizin numaranız gönderilir. Son onay: <b>{{ $offer->fee_due_at?->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</b>. Süre içinde onaylanmazsa kabul düşer.</p>
            <form method="POST" action="{{ route('market.panel.confirm', $job) }}">@csrf<button class="fm-btn" type="submit">Eşleşmeyi onayla ve müşteri bilgilerini aç</button></form>
        </div>
    @elseif ($offer && $offer->status === 'accepted')
        <div class="mk-card" style="border-color:var(--y)">
            <h2>Müşteri teklifinizi kabul etti 🎉</h2>
            <p style="color:var(--muted);line-height:1.6;margin:6px 0 12px">Eşleşme bedelini ödediğinizde müşterinin adı ve telefonu açılır, müşteriye de sizin numaranız gönderilir. Son ödeme: <b>{{ $offer->fee_due_at?->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}</b>. Süre içinde ödenmezse kabul düşer.</p>
            <div class="mk-kv"><span>Teklifiniz</span><b>₺{{ $f($offer->amount) }}</b><span>Eşleşme bedeli</span><b>₺{{ number_format($offer->fee_net, 2, ',', '.') }} + KDV = ₺{{ number_format($offer->feeGross(), 2, ',', '.') }}</b><span>Fatura</span><b>{{ $firm->invoiceType() }} · {{ $firm->invoiceTitle() }}</b></div>
            <form method="POST" action="{{ route('market.panel.pay', $job) }}" style="margin-top:14px">@csrf<button class="fm-btn" type="submit">Kartla öde ve müşteri bilgilerini aç</button></form>
        </div>
    @elseif ($offer && in_array($offer->status, ['not_selected', 'expired', 'withdrawn'], true))
        <div class="mk-card"><h2>{{ \App\Models\JobOffer::STATUSES[$offer->status] }}</h2><p style="color:var(--muted);margin:0">Teklifiniz: ₺{{ $f($offer->amount) }}. Bu iş için yapılacak başka işlem yok.</p></div>
    @elseif ($job->isOpen())
        <div class="mk-card">
            <h2>{{ $offer ? 'Teklifinizi güncelleyin' : 'Teklif verin' }}</h2>
            @if (\App\Services\Market\Marketplace::isFree())
            <p style="color:var(--muted);line-height:1.6;margin:4px 0 14px">Teklif vermek ücretsizdir. Kurucu firma programı süresince ({{ \App\Services\Market\Marketplace::freeUntil()->format('d.m.Y') }} tarihine kadar) eşleşme bedeli de yoktur: müşteri kabul ederse panelden onaylarsınız.</p>
            @else
            <p style="color:var(--muted);line-height:1.6;margin:4px 0 14px">Teklif vermek ücretsizdir. Müşteri kabul ederse eşleşme bedelini ödersiniz: bu tutar için yaklaşık ₺{{ $f($fee) }} + KDV (teklifin %{{ rtrim(rtrim(number_format(config('worksitem_market.fee_rate') * 100, 1, ',', ''), '0'), ',') }}'i; en az {{ $f(config('worksitem_market.fee_min')) }}, en çok {{ $f(config('worksitem_market.fee_max')) }} TL).</p>
            @endif
            <form method="POST" action="{{ route('market.panel.offer', $job) }}" class="fm-form" style="border:0;padding:0">
                @csrf
                <div><label class="f" for="oAmt">Teklif tutarı (KDV dahil toplam, TL) *</label><input type="number" id="oAmt" name="amount" value="{{ old('amount', $offer->amount ?? '') }}" min="100" step="100" required>@error('amount')<div class="fm-err">{{ $message }}</div>@enderror</div>
                <div><label class="f" for="oDur">Süre (gün)</label><input type="number" id="oDur" name="duration_days" value="{{ old('duration_days', $offer->duration_days ?? '') }}" min="1" max="730"></div>
                <div><label class="f" for="oStart">Ne zaman başlayabilirsiniz?</label><input type="text" id="oStart" name="start_when" value="{{ old('start_when', $offer->start_when ?? '') }}" maxlength="40" placeholder="ör. 1 hafta içinde"></div>
                <div><label class="f" for="oMat">Malzeme</label><select id="oMat" name="includes_material"><option value="1" @selected(old('includes_material', (int) ($offer->includes_material ?? 1)) == 1)>Malzeme dahil</option><option value="0" @selected(old('includes_material', (int) ($offer->includes_material ?? 1)) == 0)>Yalnız işçilik</option></select></div>
                <div class="full"><label class="f" for="oNote">Müşteriye notunuz <small class="o">(telefon, e-posta ve bağlantılar otomatik gizlenir)</small></label><textarea id="oNote" name="note" rows="4" maxlength="1500" placeholder="Tutara neler dahil, garanti, ödeme planı önerisi…">{{ old('note', $offer->note ?? '') }}</textarea></div>
                <label class="fm-check full"><input type="checkbox" name="rules" value="1" required> <span>Teklifim yerinde keşifle netleşecek bir ön tekliftir. Kabul edilirse eşleşmeyi onaylayacağımı (ücret dönemi başladıysa eşleşme bedelini ödeyeceğimi); müşteriye Worksitem dışından ulaşmaya çalışmayacağımı kabul ederim.</span></label>
                <div class="full" style="display:flex;gap:8px;flex-wrap:wrap"><button class="fm-btn" type="submit">{{ $offer ? 'Teklifi güncelle' : 'Teklifi gönder' }}</button></div>
            </form>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
                @if ($offer)
                    <form method="POST" action="{{ route('market.panel.withdraw', $job) }}">@csrf<button class="fm-btn o" type="submit">Teklifi geri çek</button></form>
                @else
                    <form method="POST" action="{{ route('market.panel.decline', $job) }}">@csrf<button class="fm-btn o" type="submit">Bu işle ilgilenmiyorum</button></form>
                @endif
            </div>
        </div>
    @else
        <div class="mk-card"><h2>Bu iş kapandı</h2><p style="color:var(--muted);margin:0">Talep {{ mb_strtolower(\App\Models\JobRequest::STATUSES[$job->status] ?? $job->status) }}.</p></div>
    @endif
</main>
<x-site.contech-footer />
</body>
</html>
