<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PetugasMiddleware
{
    /**
     * Handle an incoming request.
    *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$petugas): Response
    {
        if (auth()->check()) {
            if (in_array(auth()->user()->petugas, $petugas)) {
                if (str_contains($request->url(), route('admin.dashboard.kendaraan.show')) && auth()->user()->role == 'Read Only' && auth()->user()->petugas == 'Umum') {
                    return redirect(route('admin.dashboard.index'));
                }

                return $next($request);
            }

            if (auth()->user()->petugas == 'Driver' && auth()->user()->role == 'Read Only') {
                return redirect(route('admin.dashboard.kendaraan.show'));
            } else {
                return redirect(route('admin.dashboard.index'));
            }
        }

        return redirect(route('home'));
    }
}
