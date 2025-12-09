<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Check if user is authenticated and has status field
        if ($user && isset($user->status)) {
            // If account is disabled/inactive (status = Nonaktif)
            if ($user->status === 'Nonaktif') {
                // Allow logout route
                if ($request->routeIs('logout') || $request->routeIs('account.disabled')) {
                    return $next($request);
                }
                
                // For AJAX requests, return JSON response
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.',
                        'redirect' => route('account.disabled')
                    ], 403);
                }
                
                // Redirect to account disabled page with message
                return redirect()->route('account.disabled')->with('message', 'Akun Anda telah dinonaktifkan.');
            }
        }
        
        return $next($request);
    }
}
