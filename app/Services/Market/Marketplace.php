<?php

namespace App\Services\Market;

use App\Models\Firm;
use App\Models\JobInvite;
use App\Models\JobOffer;
use App\Models\JobRequest;
use App\Models\Order;
use App\Services\Payments\OnlinePayment;
use App\Services\Sms\NetgsmSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Teklif pazaryeri akışı:
 *   talep → uygun firmalara davet (iş kolu + il/ilçe) → firma teklifi → kullanıcı kabulü
 *   → firma eşleşme bedelini öder → iletişim bilgileri iki tarafa açılır, Worksitem aradan çekilir.
 * Ödenmezse (48 saat) kabul düşer, kullanıcı başka teklif seçebilir.
 * Kurucu firma programı süresince (worksitem_market.free_until) bedel 0'dır: firma ödeme yerine eşleşmeyi onaylar.
 */
class Marketplace
{
    public function __construct(private NetgsmSms $sms) {}

    /** Kurucu firma programı: bu tarihe kadar (dahil) eşleşme bedeli alınmaz */
    public static function freeUntil(): ?\Carbon\CarbonImmutable
    {
        $d = (string) config('worksitem_market.free_until');

        return $d !== '' ? \Carbon\CarbonImmutable::parse($d, 'Europe/Istanbul')->endOfDay() : null;
    }

    public static function isFree(): bool
    {
        $u = self::freeUntil();

        return $u !== null && now()->lte($u);
    }

    /** Eşleşme bedeli (KDV hariç): teklifin %5'i, en az 250, en çok 7.500 TL */
    public static function fee(int $amount): float
    {
        $c = (array) config('worksitem_market');

        return (float) min((int) $c['fee_max'], max((int) $c['fee_min'], round($amount * (float) $c['fee_rate'])));
    }

    /** Telefon, e-posta ve bağlantıları gizler (iletişim, eşleşme bedeli ödenmeden paylaşılmasın) */
    public static function mask(?string $text): string
    {
        $t = (string) $text;
        $t = preg_replace('/(?:\+?9?0?[\s.\-()]*)?(?:\d[\s.\-()]*){10,}/u', '[iletişim bilgisi gizlendi]', $t);
        $t = preg_replace('/[\w.+\-]+@[\w\-]+\.[\w.\-]+/u', '[iletişim bilgisi gizlendi]', $t);
        $t = preg_replace('~(?:https?://|www\.)\S+~iu', '[bağlantı gizlendi]', $t);

        return trim((string) $t);
    }

    /** Talebe uygun firmalar: onaylı, iş almaya açık, iş kolu + il (+ ilçe kısıtı) uyuyor; öne çıkan ve en uzun süredir davet almayan önce */
    public function candidates(JobRequest $job): \Illuminate\Support\Collection
    {
        $already = $job->invites()->pluck('firm_id')->all();

        return Firm::query()->approved()->where('accepts_jobs', true)->whereNotIn('id', $already)
            ->whereJsonContains('trades', $job->trade)->whereJsonContains('cities', $job->city)
            ->get()
            ->filter(fn (Firm $f) => $f->serves($job->trade, $job->city, $job->district))
            ->filter(fn (Firm $f) => ! $f->min_job || ! $job->est_high || $job->est_high >= $f->min_job)
            ->sortBy(fn (Firm $f) => [$f->isPro() ? 0 : 1, $f->last_invited_at?->timestamp ?? 0])
            ->values();
    }

    /** Davet gönderir; davet edilen firma sayısını döner */
    public function invite(JobRequest $job): int
    {
        $max = (int) config('worksitem_market.max_invites', 5) - $job->invites()->count();
        $firms = $max > 0 ? $this->candidates($job)->take($max) : collect();
        foreach ($firms as $f) {
            JobInvite::query()->create(['job_request_id' => $job->id, 'firm_id' => $f->id]);
            $f->forceFill(['last_invited_at' => now()])->save();
            $url = route('market.panel.job', $job);
            $this->push($f, 'Yeni iş: '.$job->tradeLabel(), $job->location().($job->range() ? ' · yaklaşık '.$job->range() : '').'. Teklif vermek için dokunun.', $url);
            $this->sms($f->phone, "Worksitem: {$job->location()} bölgesinde yeni iş: {$job->tradeLabel()}".($job->range() ? ', yaklaşık '.$job->range() : '').". Teklif vermek için: $url");
            $this->mail($f->email, 'Yeni iş: '.$job->title.' · '.$job->location(),
                "Merhaba {$f->contact_name},\n\nBölgenizde yeni bir iş talebi var.\n\nİş: {$job->title}\nİş kolu: {$job->tradeLabel()}\nYer: {$job->location()}\n"
                .($job->range() ? "Kullanıcının hesapladığı yaklaşık maliyet: {$job->range()}\n" : '')
                .'Başlangıç: '.(config("worksitem_market.start_prefs.{$job->start_pref}") ?: '-')."\n\n"
                .(self::isFree()
                    ? 'Teklif vermek ücretsizdir. Kurucu firma programı süresince eşleşme bedeli de alınmaz: müşteri teklifinizi kabul edince panelden onaylarsınız, müşterinin adı ve telefonu açılır.'
                    : 'Teklif vermek ücretsizdir. Müşteri teklifinizi kabul ederse eşleşme bedelini ödersiniz; ardından müşterinin adı ve telefonu açılır.')
                ."\n\nTeklif ver: $url\n\nWorksitem");
        }
        $job->forceFill(['invited_count' => $job->invites()->count()])->save();

        if ($job->invited_count === 0) {
            $this->mail((string) config('worksitem_leads.notification_email'), 'Firma bulunamadı: '.$job->tradeLabel().' · '.$job->location(),
                "Teklif talebi {$job->public_id} için uygun kayıtlı firma yok.\n\nİş: {$job->title}\nYer: {$job->location()}\nMüşteri: {$job->name} · 0{$job->phone}\n"
                ."\nBölgede firma bulup kaydettirin (saha satış) veya Worksitem ekibi kullanıcıya dönüş yapsın.\nAdmin: ".url('/admin/job-requests'));
        }

        return $firms->count();
    }

    public function submitOffer(Firm $firm, JobRequest $job, array $data): JobOffer
    {
        return DB::transaction(function () use ($firm, $job, $data) {
            $offer = JobOffer::query()->updateOrCreate(
                ['job_request_id' => $job->id, 'firm_id' => $firm->id],
                ['amount' => (int) $data['amount'], 'duration_days' => $data['duration_days'] ?? null, 'start_when' => $data['start_when'] ?? null,
                    'includes_material' => (bool) ($data['includes_material'] ?? false), 'note' => self::mask($data['note'] ?? null) ?: null, 'status' => 'submitted']
            );
            JobInvite::query()->where('job_request_id', $job->id)->where('firm_id', $firm->id)->update(['status' => 'offered']);
            if ($offer->wasRecentlyCreated) {
                $firm->increment('offers_count');
                $this->sms($job->phone, "Worksitem: {$job->public_id} talebinize yeni teklif geldi ({$firm->name}). Teklifleri görün: ".$job->customerUrl());
            }

            return $offer;
        });
    }

    /** Kullanıcı bir teklifi kabul eder: firma eşleşme bedelini ödeyene kadar iletişim kapalı kalır */
    public function accept(JobRequest $job, JobOffer $offer): void
    {
        DB::transaction(function () use ($job, $offer) {
            $offer->update([
                'status' => 'accepted', 'accepted_at' => now(), 'fee_net' => self::isFree() ? 0 : self::fee((int) $offer->amount),
                'fee_due_at' => now()->addHours((int) config('worksitem_market.fee_hours', 48)),
            ]);
        });
        $f = $offer->firm->refresh();
        $offer->refresh();
        $url = route('market.panel.job', $job);
        $h = (int) config('worksitem_market.fee_hours', 48);
        $this->push($f, 'Teklifiniz kabul edildi', "{$job->title} · $h saat içinde panelden tamamlayın; müşteri bilgileri açılsın.", $url);
        if ((float) $offer->fee_net <= 0) {
            $this->sms($f->phone, "Worksitem: Müşteri {$job->public_id} işinde teklifinizi KABUL ETTİ. $h saat içinde panelden eşleşmeyi onaylayın (kurucu firma programı: ücretsiz), müşteri iletişim bilgileri açılsın: $url");
            $this->mail($f->email, 'Teklifiniz kabul edildi · '.$job->title,
                "Müşteri {$job->public_id} işinde teklifinizi kabul etti.\n\nKurucu firma programı kapsamında eşleşme bedeli alınmaz. "
                ."$h saat içinde panelden eşleşmeyi onaylayın; müşterinin adı ve telefonu açılır.\nSon onay: ".$offer->fee_due_at->timezone('Europe/Istanbul')->format('d.m.Y H:i')."\n\nOnayla: $url");

            return;
        }
        $this->sms($f->phone, "Worksitem: Müşteri {$job->public_id} işinde teklifinizi KABUL ETTİ. $h saat içinde eşleşme bedelini ödeyin, müşteri iletişim bilgileri açılsın: $url");
        $this->mail($f->email, 'Teklifiniz kabul edildi · '.$job->title,
            "Müşteri {$job->public_id} işinde teklifinizi kabul etti.\n\nEşleşme bedeli: ".number_format($offer->fee_net, 2, ',', '.').' TL + KDV = '
            .number_format($offer->feeGross(), 2, ',', '.')." TL\nSon ödeme: ".$offer->fee_due_at->timezone('Europe/Istanbul')->format('d.m.Y H:i')
            ."\n\nÖdeme sonrası müşterinin adı ve telefonu panelinizde açılır.\nÖde: $url");
    }

    /** Eşleşme bedeli siparişi */
    public function feeOrder(JobOffer $offer): Order
    {
        $f = $offer->firm;
        $existing = Order::query()->where('kind', 'match_fee')->where('ref', 'offer:'.$offer->id)->where('status', 'pending')->latest()->first();
        if ($existing) {
            $existing->update(['iyzico_token' => null]);

            return $existing;
        }

        return Order::query()->create([
            'kind' => 'match_fee', 'ref' => 'offer:'.$offer->id, 'firm_id' => $f->id,
            'title' => 'Worksitem eşleşme bedeli · '.$offer->job->public_id.' · '.mb_substr($offer->job->title, 0, 60),
            'amount' => $offer->feeGross(), 'name' => $f->invoiceTitle(), 'phone' => $f->phone, 'email' => $f->email,
            'return_url' => route('market.panel.job', $offer->job),
        ]);
    }

    /** Ödeme doğrulandı (OnlinePayment::complete içinden): eşleştir ve iletişimleri aç */
    public function matched(Order $order): void
    {
        $offer = JobOffer::query()->find((int) substr((string) $order->ref, 6));
        if ($offer) {
            $this->match($offer, $order);
        }
    }

    /** Ücretsiz dönemde firma eşleşmeyi onaylar (ödeme yok) */
    public function confirmFree(JobOffer $offer): bool
    {
        if ($offer->status !== 'accepted' || (float) $offer->fee_net > 0 || ($offer->fee_due_at && $offer->fee_due_at->isPast())) {
            return false;
        }
        $this->match($offer, null);

        return true;
    }

    /** Eşleştir: teklif + talep kapanır, iletişim bilgileri iki tarafa SMS ile gider, diğer teklifler "seçilmedi" olur */
    private function match(JobOffer $offer, ?Order $order): void
    {
        if ($offer->status === 'matched') {
            return;
        }
        $job = $offer->job;
        $offer->update(['status' => 'matched', 'matched_at' => now(), 'order_id' => $order?->id]);
        $job->update(['status' => 'matched', 'matched_offer_id' => $offer->id]);
        $others = JobOffer::query()->where('job_request_id', $job->id)->where('id', '!=', $offer->id)->whereIn('status', ['submitted', 'accepted'])->with('firm')->get();
        foreach ($others as $o) {
            $o->update(['status' => 'not_selected']);
            $this->mail($o->firm?->email, 'Teklif sonucu · '.$job->title, "Müşteri {$job->public_id} işinde başka bir teklifi seçti. Teklif verdiğiniz için teşekkür ederiz.\n\nWorksitem");
        }
        $f = $offer->firm;
        $f->increment('matches_count');
        $this->sms($job->phone, "Worksitem: {$f->name} ile eşleştiniz ({$job->public_id}). Firma telefonu: 0{$f->phone}. Firma sizi arayacak; iş öncesi yazılı sözleşme ve fatura isteyin.");
        $this->sms($f->phone, "Worksitem: Eşleşme tamam ({$job->public_id}). Müşteri: {$job->name}, 0{$job->phone}, {$job->location()}. Lütfen 24 saat içinde arayın.");
    }

    /** Zamanlayıcı: ödenmeyen kabuller ve süresi dolan talepler */
    public function tick(): array
    {
        $n = ['fee_expired' => 0, 'requests_expired' => 0];
        foreach (JobOffer::query()->where('status', 'accepted')->where('fee_due_at', '<', now())->with('job')->get() as $o) {
            $o->update(['status' => 'expired']);
            $n['fee_expired']++;
            if ($o->job && $o->job->status === 'open') {
                $this->sms($o->job->phone, "Worksitem: Seçtiğiniz firma süresi içinde onay vermedi ({$o->job->public_id}). Diğer teklifleri seçebilir veya yeni teklif bekleyebilirsiniz: ".$o->job->customerUrl());
            }
        }
        $n['requests_expired'] = JobRequest::query()->where('status', 'open')->where('expires_at', '<', now())->update(['status' => 'expired']);

        return $n;
    }

    private function push(\App\Models\Firm $firm, string $title, string $body, string $url): void
    {
        try {
            app(\App\Services\Push\WebPush::class)->toFirm($firm, $title, $body, $url);
        } catch (\Throwable $e) {
            Log::warning('Pazaryeri web bildirimi gönderilemedi', ['error' => $e->getMessage()]);
        }
    }

    private function sms(?string $phone, string $msg): void
    {
        if (! $phone) {
            return;
        }
        try {
            $this->sms->send($phone, $msg);
        } catch (\Throwable $e) {
            Log::warning('Pazaryeri SMS gönderilemedi', ['error' => $e->getMessage()]);
        }
    }

    private function mail(?string $to, string $subject, string $body): void
    {
        if (! $to) {
            return;
        }
        try {
            Mail::raw($body, fn ($m) => $m->to($to)->subject('WORKSITEM | '.$subject));
        } catch (\Throwable $e) {
            Log::warning('Pazaryeri e-postası gönderilemedi', ['error' => $e->getMessage()]);
        }
    }
}
