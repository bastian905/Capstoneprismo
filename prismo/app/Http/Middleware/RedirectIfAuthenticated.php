<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Use replace() to prevent back button from working
                // Redirect based on user role
                switch ($user->role) {
                    case 'admin':
                        return redirect('/admin/dashboard')->with('_no_back', true);
                    case 'mitra':
                        return redirect('/mitra/dashboard/dashboard')->with('_no_back', true);
                    case 'customer':
                        return redirect('/customer/dashboard/dashU')->with('_no_back', true);
                    default:
                        return redirect('/')->with('_no_back', true);
                }
            }
        }

        return $next($request);
    }
}
