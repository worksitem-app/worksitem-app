<?php

namespace App\Http\Controllers;

use App\Support\Ab;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/** A/B testi ölçümü: tarayıcıdan görüntülenme / tıklama (sendBeacon). Botlar sayılmaz. */
class AbController extends Controller
{
    public function store(Request $request): Response
    {
        $ab = (string) $request->input('ab', '');
        $e = (string) $request->input('e', '');
        if (preg_match('/^([a-z0-9_]{1,40}):([a-z0-9]{1,10})$/', $ab, $m) && in_array($e, ['view', 'click'], true)
            && ! preg_match('/bot|crawl|spider|slurp|preview|headless|lighthouse/i', (string) $request->userAgent())) {
            Ab::count($m[1], $m[2], $e === 'view' ? 'views' : 'clicks');
        }

        return response()->noContent();
    }
}
