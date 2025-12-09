<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieConsent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if user has accepted cookies
        if (!$request->hasCookie('cookie_consent')) {
            // Add consent banner script to response
            $content = $response->getContent();
            
            if ($content && strpos($content, '</body>') !== false) {
                $consentBanner = $this->getConsentBanner();
                $content = str_replace('</body>', $consentBanner . '</body>', $content);
                $response->setContent($content);
            }
        }

        return $response;
    }

    /**
     * Get the cookie consent banner HTML
     */
    private function getConsentBanner(): string
    {
        return <<<HTML
        <div id="cookieConsent" style="position: fixed; bottom: 0; left: 0; right: 0; background: #2c3e50; color: white; padding: 20px; z-index: 9999; box-shadow: 0 -2px 10px rgba(0,0,0,0.2); display: none;">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 300px;">
                    <p style="margin: 0; font-size: 14px;">
                        Kami menggunakan cookies untuk meningkatkan pengalaman Anda. Dengan melanjutkan, Anda menyetujui penggunaan cookies kami.
                        <a href="/cookie-policy" target="_blank" style="color: #3498db; text-decoration: underline;">Pelajari lebih lanjut</a>
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button onclick="acceptCookies()" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600;">
                        Terima
                    </button>
                    <button onclick="declineCookies()" style="background: #7f8c8d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
        <script>
            // Show banner if consent not given
            if (!document.cookie.includes('cookie_consent=')) {
                document.getElementById('cookieConsent').style.display = 'block';
            }

            function acceptCookies() {
                setCookie('cookie_consent', 'accepted', 365);
                document.getElementById('cookieConsent').style.display = 'none';
            }

            function declineCookies() {
                setCookie('cookie_consent', 'declined', 365);
                document.getElementById('cookieConsent').style.display = 'none';
                // Optionally disable non-essential cookies
                clearNonEssentialCookies();
            }

            function setCookie(name, value, days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                const expires = "expires=" + date.toUTCString();
                document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
            }

            function clearNonEssentialCookies() {
                // Clear analytics and marketing cookies
                const cookies = document.cookie.split(";");
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].trim();
                    const cookieName = cookie.split("=")[0];
                    // Keep essential cookies only
                    if (!['XSRF-TOKEN', 'laravel_session', 'cookie_consent'].includes(cookieName)) {
                        document.cookie = cookieName + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
                    }
                }
            }
        </script>
HTML;
    }
}
