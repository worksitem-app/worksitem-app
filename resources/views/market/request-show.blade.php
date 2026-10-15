@php
    $title = 'Teklifleriniz · '.$job->public_id;
    $f = fn ($v) => number_format((int) $v, 0, ',', '.');
@endphp
@include('market._layout-top')
<main class="mk-wrap">
    <p style="margin:0 0 6px;color:var(--muted);font-size:13px">Talep no {{ $job->public_id }} · bu sayfanın bağlantısını kimseyle paylaşmayın</p>
    <h1 style="margin:0 0 12px;font-size:clamp(24px,3.4vw,32px)">{{ $job->title }}</h1>
    @if (session('job_created') !== null)
        <div class="mk-ok" role="status">Talebiniz alındı. @if (session('job_created') > 0){{ session('job_created') }} firmaya iletildi; teklifler geldikçe SMS ile haber vereceğiz.@else Bölgenizde şu an uygun kayıtlı firma yok; Worksitem ekibi firma bulup size dönecek.@endif</div>
    @endif
    @if (session('job_ok'))<div class="mk-ok" role="status">{{ session('job_ok') }}</div>@endif
    @if ($errors->any())<div class="mk-err" role="alert">{{ $errors->first() }}</div>@endif

    <div class="mk-card">
        <div class="mk-kv">
            <span>Durum</span><b>{{ \App\Models\JobRequest::STATUSES[$job->status] ?? $job->status }}</b>
            <span>İş kolu</span><b>{{ $job->tradeLabel() }}</b>
            <span>Yer</span><b>{{ $job->location() }}</b>
            @if ($job->range())<span>Hesapladığınız</span><b>{{ $job->range() }}</b>@endif
            <span>Davet edilen firma</span><b>{{ $job->invited_count }}</b>
            @if ($job->isOpen() && $job->expires_at)<span>Açık kalma</span><b>{{ $job->expires_at->timezone('Europe/Istanbul')->format('d.m.Y') }} tarihine kadar</b>@endif
        </div>
    </div>

    @if ($matched)
        <div class="mk-card" style="border-color:#16A34A">
            <h2>✓ {{ $matched->firm->name }} ile eşleştiniz</h2>
            <p style="margin:6px 0 12px;color:var(--muted)">Firma sizi arayacak. Siz de arayabilirsiniz.</p>
            <div class="mk-kv"><span>Firma telefonu</span><b><a href="tel:+90{{ $matched->firm->phone }}" style="color:inherit">0{{ $matched->firm->phone }}</a></b><span>Teklif</span><b>₺{{ $f($matched->amount) }} (KDV dahil)</b></div>
            <div class="mk-note" style="margin:14px 0 0"><b>İşe başlamadan önce:</b> yazılı sözleşme (iş tanımı, malzeme, süre, ödeme planı, garanti) isteyin; ödemeyi aşamalara bölün, tamamını peşin vermeyin; fatura isteyin. Worksitem bu sözleşmenin tarafı değildir. Sorun yaşarsanız WhatsApp: {{ config('worksitem_inspection.phone') }}</div>
        </div>
    @elseif ($pending)
        <div class="mk-note"><b>{{ $pending->firm->name }}</b> teklifini kabul ettiniz. Firmanın eşleşmeyi onaylaması bekleniyor (en geç {{ $pending->fee_due_at?->timezone('Europe/Istanbul')->format('d.m.Y H:i') }}). Onaylarsa telefonunuz firmayla paylaşılır ve size SMS gelir; onaylamazsa başka bir teklif seçebilirsiniz.</div>
    @endif

    <h2 style="font-size:20px;margin:22px 0 10px">Teklifler ({{ $offers->whereIn('status', ['submitted', 'accepted', 'matched'])->count() }})</h2>
    @forelse ($offers as $o)
        @php($firm = $o->firm)
        <div class="mk-offer {{ in_array($o->status, ['accepted', 'matched'], true) ? 'on' : '' }}">
            <div class="head">
                <div>
                    <b style="font-size:17px">{{ $firm->name }}</b><br>
                    <span class="mk-tag">{{ $firm->typeLabel() }}</span>
                    @if ($firm->tax_verified_at)<span class="mk-tag g">Vergi kaydı doğrulandı</span>@endif
                    @if ($firm->isPro())<span class="mk-tag y">Öne çıkan</span>@endif
                    @if ($firm->founded_year)<span class="mk-tag">{{ $firm->founded_year }}'den beri</span>@endif
                    @if ($firm->matches_count)<span class="mk-tag">Worksitem'de {{ $firm->matches_count }} iş</span>@endif
                    @if ($o->status === 'expired')<span class="mk-tag r">Firma süresi içinde onaylamadı</span>@endif
                </div>
                <div style="text-align:right"><div class="amt">₺{{ $f($o->amount) }}</div><small style="color:var(--muted)">KDV dahil · {{ $o->includes_material ? 'malzeme dahil' : 'malzeme hariç' }}</small></div>
            </div>
            <div class="mk-kv" style="margin-top:10px">
                @if ($o->duration_days)<span>Süre</span><b>{{ $o->duration_days }} gün</b>@endif
                @if ($o->start_when)<span>Başlangıç</span><b>{{ $o->start_when }}</b>@endif
            </div>
            @if ($o->note)<p style="margin:10px 0 0;white-space:pre-line;line-height:1.6">{{ $o->note }}</p>@endif
            @if ($o->status === 'submitted' && $job->isOpen() && ! $pending)
                <details class="mk-acc" style="margin-top:12px">
                    <summary class="fm-btn">Bu teklifi kabul et</summary>
                    <form method="POST" action="{{ route('market.request.accept', ['job' => $job->public_id, 'offer' => $o->public_id]) }}" style="margin-top:12px">
                        @csrf
                        <input type="hidden" name="t" value="{{ $t }}">
                        <div class="mk-note" style="margin-bottom:10px"><b>Kabul etmeden önce okuyun:</b> Kabul ettiğinizde, firma eşleşmeyi onaylarsa <b>adınız, telefon numaranız ve ilçeniz {{ $firm->name }} ile paylaşılacak</b> ve firma sizi arayacak. Kabul etmek sözleşme yapmak değildir; fiyat ve koşulları firmayla yüz yüze netleştirip yazılı sözleşme yapmalısınız. Worksitem bu sözleşmenin tarafı değildir.</div>
                        <label class="fm-check"><input type="checkbox" name="share" value="1" required> <span>Telefon numaramın ve adımın bu firmayla paylaşılmasını kabul ediyorum.</span></label>
                        <button class="fm-btn" type="submit" style="margin-top:10px">Onayla ve kabul et</button>
                    </form>
                </details>
            @endif
        </div>
    @empty
        <div class="mk-card"><p style="margin:0;color:var(--muted)">Henüz teklif yok. Firmalar teklif verdikçe SMS ile haber vereceğiz.</p></div>
    @endforelse

    @if ($job->status === 'open')
        <form method="POST" action="{{ route('market.request.close', $job) }}" style="margin-top:22px" onsubmit="return confirm('Talebi kapatmak istediğinize emin misiniz? Firmalar yeni teklif veremez.')">
            @csrf <input type="hidden" name="t" value="{{ $t }}">
            <button class="fm-btn o" type="submit">Talebi kapat (artık teklif istemiyorum)</button>
        </form>
    @endif
    <p class="lead" style="font-size:13px;margin-top:22px">Worksitem, kullanıcılar ile firmaları buluşturan bir aracı hizmet sağlayıcıdır; firmaların yaptığı işin tarafı değildir. Firmaların vergi kaydı kontrol edilir; yine de sözleşme ve ödeme öncesi firmayı kendiniz de değerlendirin. <a href="{{ route('legal.teklif_alma') }}" style="color:inherit">Koşullar</a></p>
</main>
<x-site.contech-footer />
</body>
</html>
