<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginViewResponse implements LoginViewResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): Response
    {
        return response()->view('auth.login');
    }
}
