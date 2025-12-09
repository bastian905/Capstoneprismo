<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for API routes and login page
        if ($request->is('api/*') || $request->is('login') || $request->is('register')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            
            if ($lastActivity && (time() - $lastActivity) > $timeout) {
                // Session expired
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            
            // Update last activity time
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}
