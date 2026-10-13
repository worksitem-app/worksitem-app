<?php

namespace App\Http\Controllers;

use App\Services\AI\AiCredits;
use App\Services\Sms\NetgsmSms;
use App\Services\Sms\PhoneOtp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Telefonla giriş (SMS kodu) ve AI kullanım hakkı durumu */
class AiAccountController extends Controller
{
    public function __construct(private AiCredits $credits) {}

    public function status(Request $request): JsonResponse
    {
        return response()->json($this->credits->status($this->credits->current($request)) + [
            'packages' => config('worksitem_ai_credits.packages'),
            'whatsapp' => config('worksitem_inspection.whatsapp'),
        ]);
    }

    public function sendCode(Request $request, PhoneOtp $otp): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:30']]);
        if (! NetgsmSms::normalize((string) $request->input('phone'))) {
            return response()->json(['ok' => false, 'message' => 'Lütfen 05XX XXX XX XX biçiminde bir cep telefonu yazın.'], 422);
        }
        $r = $otp->send((string) $request->input('phone'), 'ai_giris');

        return response()->json($r, $r['ok'] ? 200 : 429);
    }

    public function login(Request $request, PhoneOtp $otp): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'digits:6'],
            'kvkk' => ['accepted'],
        ], [
            'code.required' => 'Telefonunuza gelen 6 haneli kodu yazın.',
            'code.digits' => 'Kod 6 haneli olmalıdır.',
            'kvkk.accepted' => 'Devam etmek için aydınlatma metnini okuduğunuzu onaylayın.',
        ]);

        if (! $otp->verify($data['phone'], $data['code'], 'ai_giris')) {
            return response()->json(['ok' => false, 'message' => 'Kod hatalı veya süresi doldu. Yeni kod isteyebilirsiniz.', 'errors' => ['code' => ['Kod hatalı veya süresi doldu.']]], 422);
        }

        $wallet = $this->credits->login($request, (string) NetgsmSms::normalize($data['phone']));

        return response()->json(['ok' => true, 'message' => 'Giriş yapıldı.', 'csrf' => csrf_token()] + $this->credits->status($wallet));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->credits->logout($request);

        return response()->json(['ok' => true]);
    }
}
