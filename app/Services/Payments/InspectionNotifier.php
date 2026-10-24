<?php

namespace App\Services\Payments;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use App\Support\InspectionOffer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Keşif siparişi e-postaları (ekip + müşteri sözleşme kopyası) ve sözleşme PDF'i.
 * v109: Kartla ödemede bu e-postalar ödeme ONAYLANDIKTAN sonra gider; havale/EFT'de sipariş anında.
 */
class InspectionNotifier
{
    public function pdf(?Lead $lead): \Barryvdh\DomPDF\PDF
    {
        $logo = public_path('brand/worksitem-logo.png');

        return Pdf::loadView('reports.kesif-sozlesme-pdf', [
            'lead' => $lead,
            'logo' => is_file($logo) ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logo)) : null,
        ])->setPaper('a4')->setOption(['isFontSubsettingEnabled' => true, 'isRemoteEnabled' => false]);
    }

    /**
     * Ekibe sipariş e-postası (sözleşme PDF ekli).
     *
     * @param  string  $state  'siparis' (havale / kart kapalı) | 'odendi' (kart ödemesi alındı) | 'odeme-acilamadi'
     */
    public function teamEmail(Lead $lead, string $state = 'siparis'): void
    {
        $to = trim((string) config('worksitem_leads.notification_email'));
        if ($to === '') {
            return;
        }

        try {
            $url = LeadResource::getUrl('edit', ['record' => $lead]);
        } catch (\Throwable) {
            $url = url('/admin');
        }

        $inv = (array) ($lead->inspection_data['invoice'] ?? []);
        $method = (string) $lead->inspection_payment_method;
        $early = (bool) ($lead->inspection_data['consents']['early_performance'] ?? false);
        $total = InspectionOffer::tl(InspectionOffer::amounts()['total']);

        $head = match ($state) {
            'odendi' => 'Keşif ödemesi kartla ALINDI — müşteriyi arayıp randevu verin.',
            'odeme-acilamadi' => 'Müşteri kartla ödemek istedi ama iyzico ödeme sayfası AÇILAMADI — müşteriyi arayın, ödeme bağlantısı gönderin.',
            default => 'Müşteri yerinde keşif SİPARİŞİ verdi — '.($method === 'havale' ? 'havale/EFT dekontunu bekleyin' : 'kart ödeme bağlantısı (iyzico link) gönderin').' ve randevu verin.',
        };
        $subject = match ($state) {
            'odendi' => 'KEŞİF ÖDENDİ',
            'odeme-acilamadi' => 'KEŞİF ÖDEMESİ AÇILAMADI',
            default => 'KEŞİF SİPARİŞİ',
        };

        $body = implode("\n", array_filter([
            $head,
            '',
            'Talep no: '.$lead->displayNo(),
            'Ad soyad: '.($lead->name ?: '-'),
            'Telefon: '.($lead->phone ?: '-'),
            'Şehir/ilçe: '.collect([$lead->city, $lead->district])->filter()->implode(' / '),
            'Proje: '.($lead->project_type ?: '-'),
            'Tutar: '.$total.' ('.InspectionOffer::feeLabel().')',
            'Ödeme yöntemi: '.config("worksitem_inspection.payment_methods.$method.label", $method),
            $state === 'odendi' ? 'iyzico ödeme no: '.($lead->inspection_data['iyzico']['payment_id'] ?? '-') : null,
            'Fatura: '.(($inv['type'] ?? '') === 'kurumsal' ? 'KURUMSAL — '.($inv['title'] ?? '').' · '.($inv['tax_office'] ?? '').' / '.($inv['tax_no'] ?? '') : 'Bireysel'),
            'Cayma süresi içinde keşif talebi: '.($early ? 'EVET (14 gün beklemeden randevu verilebilir)' : 'HAYIR — keşif 14 gün dolduktan sonra yapılmalı'),
            'Kabul zamanı: '.optional($lead->inspection_accepted_at)->format('d.m.Y H:i').' · Sözleşme sürümü '.$lead->inspection_terms_version,
            '',
            'Admin: '.$url,
            'Sözleşme (ÖBF + MHS) PDF ektedir.',
        ], fn ($l) => $l !== null));

        try {
            $pdf = $this->pdf($lead)->output();
            Mail::raw($body, function ($m) use ($to, $lead, $pdf, $subject) {
                $m->to($to)
                    ->subject('WORKSITEM | '.$subject.' | '.($lead->name ?: 'İsimsiz').' | '.$lead->displayNo())
                    ->attachData($pdf, 'kesif-sozlesmesi-'.$lead->public_id.'.pdf', ['mime' => 'application/pdf']);
            });
        } catch (\Throwable $e) {
            Log::error('Keşif ekip e-postası gönderilemedi', ['lead_id' => $lead->id, 'state' => $state, 'error' => $e->getMessage()]);
        }
    }

    /** Müşteriye sözleşme kopyası (kalıcı veri saklayıcısı) */
    public function customerCopy(Lead $lead, bool $paid = false): void
    {
        if (blank($lead->email)) {
            return;
        }

        try {
            $pdf = $this->pdf($lead)->output();
            $total = InspectionOffer::tl(InspectionOffer::amounts()['total']);
            $body = implode("\n", [
                'Merhaba '.$lead->name.',',
                '',
                ($paid ? 'Keşif ödemeniz alındı, siparişiniz kesinleşti.' : 'Yerinde keşif siparişiniz alındı.').' Talep no: '.$lead->displayNo(),
                'Tutar: '.$total.' ('.InspectionOffer::feeLabel().')',
                '',
                'Onayladığınız Ön Bilgilendirme Formu ve Mesafeli Hizmet Sözleşmesi ektedir; lütfen saklayın.',
                $paid
                    ? 'Kainta İnşaat Uzmanı randevu için en kısa sürede sizi arayacak.'
                    : 'Ödeme tamamlandıktan sonra Kainta İnşaat Uzmanı randevu için sizi arayacak.',
                '',
                'Cayma hakkı: keşif yapılmadan önce 14 gün içinde '.config('worksitem_inspection.cancel_email').' adresine yazabilirsiniz.',
                'Çağrı merkezi / WhatsApp: '.config('worksitem_inspection.phone'),
                '',
                config('worksitem_company.legal_name').' · worksitem.com',
            ]);
            Mail::raw($body, function ($m) use ($lead, $pdf, $paid) {
                $m->to($lead->email, $lead->name)
                    ->subject(($paid ? 'Ödemeniz alındı — keşif sözleşmeniz' : 'Keşif siparişiniz ve sözleşmeniz').' | '.$lead->displayNo())
                    ->attachData($pdf, 'kesif-sozlesmesi-'.$lead->public_id.'.pdf', ['mime' => 'application/pdf']);
            });
        } catch (\Throwable $e) {
            Log::error('Müşteri sözleşme e-postası gönderilemedi', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }
}
