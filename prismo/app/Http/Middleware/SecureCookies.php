<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class SecureCookies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set secure cookie attributes for all cookies
        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->setCookie(
                new Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    $cookie->getExpiresTime(),
                    $cookie->getPath(),
                    $cookie->getDomain(),
                    config('session.secure', true), // Secure flag (HTTPS only)
                    true, // HttpOnly flag (prevent XSS)
                    false, // Raw
                    $cookie->getSameSite() ?? 'Lax' // SameSite attribute
                )
            );
        }

        return $response;
    }
}
