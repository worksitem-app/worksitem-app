<?php

namespace App\Exceptions;

use Exception;

class SubmissionConflictException extends Exception
{
    protected $message = 'Bu gönderim zaten işlenmiş veya içerik değişmiş.';

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'duplicate' => true,
            ], 409);
        }
        return back()->withErrors(['form' => $this->getMessage()]);
    }
}
