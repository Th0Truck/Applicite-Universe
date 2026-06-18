<?php

namespace App\Actions\Fortify;

class RedirectIfTwoFactorNotConfirmed
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $next
     * @return mixed
     */
    public function __invoke($request, $next)
    {
        if ($request->user() && ! $request->user()->two_factor_confirmed_at) {
            return redirect()->route('two-factor.show');
        }

        return $next($request);
    }
}
