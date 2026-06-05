<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;

class TwoFactorAuthenticationSetupController extends Controller
{
    /**
     * Show the authenticated user's two-factor authentication setup page.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $secretKey = null;
        $recoveryCodes = [];

        if ($user->two_factor_secret !== null) {
            $secretKey = Fortify::currentEncrypter()->decrypt($user->two_factor_secret);
        }

        if ($user->two_factor_confirmed_at !== null && $user->two_factor_recovery_codes !== null) {
            $recoveryCodes = $user->recoveryCodes();
        }

        return view('auth.two-factor-authentication', [
            'recoveryCodes' => $recoveryCodes,
            'secretKey' => $secretKey,
            'user' => $user,
        ]);
    }
}
