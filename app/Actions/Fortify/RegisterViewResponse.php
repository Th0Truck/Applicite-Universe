<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RegisterViewResponse as RegisterViewResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterViewResponse implements RegisterViewResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): Response
    {
        return response()->view('auth.register');
    }
}
