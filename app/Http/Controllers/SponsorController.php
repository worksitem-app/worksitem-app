<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Sponsor;
use App\Models\SponsorApplication;
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
    public function page(Request $request): View
    {
        $slug = (string) $request->query('arac', old('tool', ''));
        $cfg = $slug !== '' ? config("worksitem_sponsors.tools.$slug") : null;
        $live = 0;
        if ($cfg) {
            try {
                $live = Sponsor::query()->live()->where('tool_slug', $slug)->count();
            } catch (\Throwable) {
                $live = 0;
            }
        }

        return view('markalar-icin', [
            'toolSlug' => $cfg ? $slug : null,
            'toolCfg' => $cfg ?: null,
            'toolFree' => max(0, (int) config('worksitem_sponsors.max_cards', 3) - $live),
        ]);
    }

    public function apply(Request $request): RedirectResponse
    {
        if (filled($request->input('website_url'))) {   // bot tuzağı
            return redirect()->route('sponsor.page')->with('sponsor_ok', true);
        }
        $data = $request->validate([
            'company' => ['required', 'string', 'max:150'],
            'name' => ['required', 'string', 'max:120'],
            'contact_title' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'email' => ['required', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:200'],
            'legal_name' => ['nullable', 'string', 'max:200'],
            'tax_no' => ['nullable', 'regex:/^\d{10,11}$/'],
            'tax_office' => ['nullable', 'string', 'max:80'],
            'categories' => ['nullable', 'array', 'max:12'],
            'categories.*' => [\Illuminate\Validation\Rule::in(array_keys(SponsorApplication::categoryOptions()))],
            'tools' => ['nullable', 'array', 'max:20'],
            'tools.*' => [\Illuminate\Validation\Rule::in(array_keys((array) config('worksitem_sponsors.tools')))],
            'budget' => ['nullable', \Illuminate\Validation\Rule::in(array_keys(SponsorApplication::BUDGETS))],
            'start' => ['nullable', \Illuminate\Validation\Rule::in(array_keys(SponsorApplication::STARTS))],
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'interest' => ['nullable', 'string', 'max:150'],
            'tool' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:2000'],
            'contact_consent' => ['accepted'],
        ], [
            'company.required' => 'Firma / marka adını yazın.',
            'name.required' => 'Yetkili adını yazın.',
            'phone.required' => 'Telefon numarasını yazın.',
            'email.required' => 'E-posta adresini yazın.',
            'website.url' => 'Web sitesini https:// ile başlayarak yazın.',
            'tax_no.regex' => 'Vergi numarası 10 (TC kimlik no 11) rakam olmalıdır.',
            'logo.mimes' => 'Logo PNG, JPG, SVG veya WEBP olmalıdır.',
            'logo.max' => 'Logo en fazla 2 MB olabilir.',
            'contact_consent.accepted' => 'Devam etmek için KVKK onayını işaretleyin.',
        ]);
        if (! empty($data['tax_no']) && ! \App\Support\TaxId::valid($data['tax_no'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['tax_no' => 'Vergi numarası geçerli görünmüyor; emin değilseniz boş bırakabilirsiniz.']);
        }
        $tools = array_values(array_unique(array_merge((array) ($data['tools'] ?? []), ! empty($data['tool']) && config("worksitem_sponsors.tools.{$data['tool']}") ? [$data['tool']] : [])));
        $catLabels = array_map(fn ($k) => SponsorApplication::categoryOptions()[$k] ?? $k, (array) ($data['categories'] ?? []));

        $lead = Lead::query()->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'source' => 'sponsor_basvurusu',
            'project_type' => 'Marka sponsorluğu: '.($data['interest'] ?? ($catLabels ? implode(', ', array_slice($catLabels, 0, 3)) : 'genel')),
            'notes' => 'Firma: '.$data['company']."\n".(! empty($data['tool']) ? 'Araç: '.$data['tool']."\n" : '')
                .($tools ? 'Araçlar: '.implode(', ', $tools)."\n" : '').($catLabels ? 'Kategoriler: '.implode(', ', $catLabels)."\n" : '')
                .(! empty($data['budget']) ? 'Bütçe: '.SponsorApplication::BUDGETS[$data['budget']]."\n" : '').($data['message'] ?? ''),
            'contact_consent' => true,
            'consent_at' => now(),
            'status' => 'yeni',
        ]);

        $app = SponsorApplication::query()->create([
            'company' => trim($data['company']), 'legal_name' => $data['legal_name'] ?? null, 'tax_no' => $data['tax_no'] ?? null, 'tax_office' => $data['tax_office'] ?? null,
            'contact_name' => trim($data['name']), 'contact_title' => $data['contact_title'] ?? null, 'phone' => $data['phone'], 'email' => $data['email'],
            'website' => $data['website'] ?? null, 'categories' => $data['categories'] ?? null, 'tools' => $tools ?: null,
            'budget' => $data['budget'] ?? null, 'start' => $data['start'] ?? null, 'message' => isset($data['message']) ? strip_tags($data['message']) : null,
            'logo_path' => $request->hasFile('logo') ? $request->file('logo')->store('sponsor-logos', 'local') : null, 'lead_id' => $lead->id,
        ]);

        try {
            app(NewLeadEmailNotifier::class)->notify($lead);
        } catch (\Throwable $e) {
            Log::error('Sponsor başvuru e-postası', ['error' => $e->getMessage()]);
        }

        return redirect()->route('sponsor.page')->with('sponsor_ok', $app->public_id);
    }
}
