<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Determine redirect URL based on requested path
            $intendedUrl = $request->url();
            
            if (str_contains($intendedUrl, '/admin')) {
                return redirect()->route('login')->with('error', 'Silakan login sebagai Admin untuk mengakses halaman ini.');
            } elseif (str_contains($intendedUrl, '/mitra')) {
                return redirect()->route('login')->with('error', 'Silakan login sebagai Mitra untuk mengakses halaman ini.');
            } elseif (str_contains($intendedUrl, '/customer')) {
                return redirect()->route('login')->with('error', 'Silakan login sebagai Customer untuk mengakses halaman ini.');
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
