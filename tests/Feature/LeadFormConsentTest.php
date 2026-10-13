<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * LeadController contact_consent alanını zorunlu tutar. Bu alanı göndermeyen
 * her form sessizce reddedilir ve lead kaybolur. Tüm lead formlarında
 * KVKK onay kutusu bulunmalı.
 */
class LeadFormConsentTest extends TestCase
{
    public function test_lead_store_a_post_eden_her_formda_kvkk_onayi_var(): void
    {
        $eksik = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        foreach ($it as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $s = file_get_contents($file->getPathname());
            preg_match_all('#<form[^>]*route\(\s*[\'"]lead\.store[\'"]\s*\)[^>]*>.*?</form>#s', $s, $m);
            foreach ($m[0] as $form) {
                if (! str_contains($form, 'contact_consent') && ! str_contains($form, 'lead-consent-fields') && ! str_contains($form, 'supplier-consent-fields')) {
                    $eksik[] = str_replace(resource_path('views').'/', '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $eksik, 'KVKK onayı olmayan lead formları: '.implode(', ', $eksik));
    }
}
