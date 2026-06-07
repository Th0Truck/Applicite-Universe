<?php

namespace App\Actions\Fortify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract, TwoFactorLoginResponseContract
{
    /**
     * Create an HTTP response after successful authentication.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect()->intended($this->redirectPath($request));
    }

    /**
     * Resolve the user's post-login redirect path.
     *
     * @param  Request  $request
     */
    private function redirectPath($request): string
    {
        $user = $request->user();

        if ($user?->hasAnyRole(['admin', 'super_admin'])) {
            return route('dashboard');
        }

        return url('/');
    }
}
