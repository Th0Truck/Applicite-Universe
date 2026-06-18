<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse as TwoFactorChallengeViewResponseContract;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorChallengeViewResponse implements TwoFactorChallengeViewResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): Response
    {
        return response()->view('auth.two-factor-challenge');
    }
}
