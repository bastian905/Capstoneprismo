<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);
            
            return response()->json([
                'error' => 'Terlalu banyak percobaan. Silakan coba lagi dalam ' . $retryAfter . ' detik.',
                'retry_after' => $retryAfter
            ], 429);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature(Request $request)
    {
        if ($user = $request->user()) {
            return 'rate_limit_user_' . sha1($user->id . '|' . $request->path());
        }

        return 'rate_limit_ip_' . sha1($request->ip() . '|' . $request->path());
    }

    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        return $this->limiter->retriesLeft($key, $maxAttempts);
    }

    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts)
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remainingAttempts),
        ]);

        return $response;
    }
}
