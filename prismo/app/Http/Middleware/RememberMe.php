<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RememberMe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if remember me cookie exists and user is not authenticated
        if (!Auth::check() && $request->hasCookie('remember_me')) {
            $rememberToken = $request->cookie('remember_me');
            
            // Try to authenticate user with remember token
            $user = \App\Models\User::where('remember_token', $rememberToken)->first();
            
            if ($user) {
                Auth::login($user, true);
                session(['last_activity_time' => time()]);
            } else {
                // Invalid token, remove cookie
                cookie()->queue(cookie()->forget('remember_me'));
            }
        }

        return $next($request);
    }
}
