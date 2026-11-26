<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige TOTP (Google Authenticator) a todo usuario autenticado:
 *  - sin TOTP confirmado  -> debe enrolarse (QR + código)
 *  - con TOTP confirmado  -> debe validar un código en cada sesión nueva
 */
class EnsureTotpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->two_factor_confirmed_at) {
            return redirect()->route('totp.setup');
        }

        if ($request->session()->get('totp_passed_for') !== $user->id) {
            return redirect()->route('totp.challenge');
        }

        return $next($request);
    }
}
