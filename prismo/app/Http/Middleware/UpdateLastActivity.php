<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update last_activity_at hanya jika sudah lebih dari 5 menit
            if (!$user->last_activity_at || now()->diffInMinutes($user->last_activity_at) >= 5) {
                $user->last_activity_at = now();
                $user->save();
            }
        }
        
        return $next($request);
    }
}
