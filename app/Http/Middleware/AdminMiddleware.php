<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Ako je korisnik ulogovan i ako je admin, pusti ga dalje
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }
    
        // Ako nije admin, baci ga na home sa porukom
        return redirect('/')->with('error', 'Nemate pristup ovoj stranici.');
    }
}
