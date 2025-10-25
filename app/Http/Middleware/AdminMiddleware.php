<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Ellenőrizzük, van-e bejelentkezett felhasználó és admin-e
        if (!Auth::check() || !Auth::user()->is_admin) {
            // Ha nem admin, visszadob a home-ra vagy 403
            return redirect('/')->with('error', 'Nincs jogosultságod az oldal megtekintéséhez.');
            // Vagy használhatod:
            // abort(403, 'Nincs jogosultságod.');
        }

        // Ha admin, engedélyezve a kérés
        return $next($request);
    }
}
