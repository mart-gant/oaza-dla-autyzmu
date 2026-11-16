<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user() ?? ($request->bearerToken() ? Auth::guard('api')->user() : null);

        if (! $user?->is_suspended) {
            return $next($request);
        }

        if ($request->bearerToken()) {
            Auth::guard('api')->logout();

            return response()->json(['success' => false, 'message' => 'Your account has been suspended'], 403);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['email' => 'Twoje konto zostało zawieszone.']);
    }
}
