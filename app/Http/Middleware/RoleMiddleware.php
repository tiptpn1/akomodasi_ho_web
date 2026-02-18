<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (auth()->check()) {
            if (in_array(auth()->user()->hakAkses->hak_akses_nama, $roles)) {
                return $next($request);
            } else {
                return redirect()->route('admin.agenda.index');
            }
        }

        return redirect()->route('home');
    }
}
