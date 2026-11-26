<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class TotpController extends Controller
{
    public function setup(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_confirmed_at) {
            return redirect()->route('dashboard');
        }

        if (! $user->two_factor_secret) {
            $user->forceFill(['two_factor_secret' => Totp::generateSecret()])->save();
        }

        $issuer = parse_url((string) config('app.url'), PHP_URL_HOST) ?: config('app.name');
        $uri = Totp::uri($user->two_factor_secret, $user->email, $issuer);

        return view('auth.totp-setup', ['uri' => $uri, 'secret' => $user->two_factor_secret]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->validate(['code' => ['required', 'digits:6']]);

        $ts = Totp::verify($user->two_factor_secret, (string) $request->input('code'), $user->two_factor_last_ts);

        if ($ts === false) {
            return back()->withErrors(['code' => 'Código incorrecto o ya usado.']);
        }

        $user->forceFill(['two_factor_confirmed_at' => now(), 'two_factor_last_ts' => $ts])->save();
        $request->session()->put('totp_passed_for', $user->id);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->two_factor_confirmed_at) {
            return redirect()->route('totp.setup');
        }

        return view('auth.totp-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->validate(['code' => ['required', 'digits:6']]);

        $ts = Totp::verify($user->two_factor_secret, (string) $request->input('code'), $user->two_factor_last_ts);

        if ($ts === false) {
            return back()->withErrors(['code' => 'Código incorrecto o ya usado.']);
        }

        $user->forceFill(['two_factor_last_ts' => $ts])->save();
        $request->session()->put('totp_passed_for', $user->id);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
