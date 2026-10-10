<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi admin.');
        }

        if (!empty($roles) && !in_array($request->user()->role, $roles)) {
            // Jika kasir mencoba akses menu admin, arahkan ke POS
            if ($request->user()->isCashier()) {
                return redirect()->route('pos.index')->with('warning', 'Anda tidak memiliki akses ke halaman manajemen admin.');
            }

            abort(403, 'Akses tidak diizinkan untuk peran akun Anda.');
        }

        return $next($request);
    }
}
