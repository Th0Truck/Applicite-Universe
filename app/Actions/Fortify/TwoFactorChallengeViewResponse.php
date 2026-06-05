<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse as TwoFactorChallengeViewResponseContract;

class TwoFactorChallengeViewResponse implements TwoFactorChallengeViewResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return view('auth.two-factor-challenge');
    }
}
