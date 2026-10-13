<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Sponsor;
use App\Services\Leads\NewLeadEmailNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SponsorController extends Controller
{
    /** Sponsor tıklaması: sayılır ve markanın sitesine yönlendirilir */
    public function go(Sponsor $sponsor): RedirectResponse
    {
        if (! Sponsor::query()->live()->whereKey($sponsor->id)->exists()) {
            return redirect('/araclar');
        }
        $sponsor->increment('clicks');

        return redirect()->away($sponsor->trackedUrl());
    }

    /** Markalar için: sponsorluk paketleri ve başvuru formu */
    public function page(): View
    {
        return view('markalar-icin');
    }

    public function apply(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company' => ['required', 'string', 'max:150'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'email' => ['required', 'email', 'max:150'],
            'interest' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:2000'],
            'contact_consent' => ['accepted'],
        ], [
            'company.required' => 'Firma adını yazın.',
            'name.required' => 'Yetkili adını yazın.',
            'phone.required' => 'Telefon numarasını yazın.',
            'email.required' => 'E-posta adresini yazın.',
            'contact_consent.accepted' => 'Devam etmek için KVKK onayını işaretleyin.',
        ]);

        $lead = Lead::query()->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'source' => 'sponsor_basvurusu',
            'project_type' => 'Marka sponsorluğu: '.($data['interest'] ?: 'genel'),
            'notes' => 'Firma: '.$data['company']."\n".($data['message'] ?? ''),
            'contact_consent' => true,
            'consent_at' => now(),
            'status' => 'yeni',
        ]);

        try {
            app(NewLeadEmailNotifier::class)->notify($lead);
        } catch (\Throwable $e) {
            Log::error('Sponsor başvuru e-postası', ['error' => $e->getMessage()]);
        }

        return redirect()->route('sponsor.page')->with('sponsor_ok', true);
    }
}
