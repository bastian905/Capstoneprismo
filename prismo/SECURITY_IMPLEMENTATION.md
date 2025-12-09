# Security Implementation Guide - Frontend & Backend

## Overview
Comprehensive security measures untuk melindungi aplikasi dari berbagai attack vectors.

## 1. CSRF Protection (Cross-Site Request Forgery)

### Backend (Already Implemented)
Laravel sudah memiliki CSRF protection by default via `VerifyCsrfToken` middleware.

### Frontend Implementation
Pastikan semua form dan AJAX request menyertakan CSRF token:

```html
<!-- In Blade templates -->
<form method="POST">
    @csrf
    <!-- form fields -->
</form>
```

```javascript
// For AJAX requests
fetch('/api/endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
});
```

## 2. XSS Protection (Cross-Site Scripting)

### Backend - Output Escaping
Laravel Blade automatically escapes output dengan `{{ }}`:

```php
// Safe - automatically escaped
{{ $user->name }}

// Unsafe - raw output (only use for trusted content)
{!! $user->bio !!}
```

### Input Validation & Sanitization
```php
// In Controller
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'description' => 'required|string|max:1000',
    ]);
    
    // Use strip_tags for user-generated content
    $validated['description'] = strip_tags($validated['description']);
    
    return response()->json(['success' => true]);
}
```

### Frontend - DOMPurify (Optional)
For rich text content:
```bash
npm install dompurify
```

```javascript
import DOMPurify from 'dompurify';

// Sanitize HTML before inserting
const clean = DOMPurify.sanitize(dirtyHTML);
element.innerHTML = clean;
```

## 3. SQL Injection Protection

### Use Eloquent ORM (Recommended)
```php
// Safe - Eloquent handles escaping
$users = User::where('email', $email)->get();
$booking = Booking::find($id);
```

### Parameterized Queries
```php
// Safe - using parameter binding
$users = DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// Safe - named bindings
$users = DB::select('SELECT * FROM users WHERE email = :email', ['email' => $email]);

// UNSAFE - NEVER DO THIS
$users = DB::select("SELECT * FROM users WHERE email = '$email'"); // ❌
```

## 4. Rate Limiting

### 4.1 Create Rate Limit Middleware
File: `app/Http/Middleware/RateLimitMiddleware.php`
```php
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
            return response()->json([
                'error' => 'Too many requests. Please try again later.'
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
            return sha1($user->id);
        }

        return sha1($request->ip());
    }

    protected function calculateRemainingAttempts($key, $maxAttempts)
    {
        return $this->limiter->retriesLeft($key, $maxAttempts);
    }

    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts)
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);

        return $response;
    }
}
```

### 4.2 Register Middleware
Update `bootstrap/app.php`:
```php
$middleware->alias([
    'role' => \App\Http\Middleware\CheckRole::class,
    'auth' => \App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => \App\Http\Middleware\RateLimitMiddleware::class, // Add this
]);
```

### 4.3 Apply to Routes
Update `routes/api.php` and `routes/web.php`:
```php
// Login attempts - 5 per minute
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');

// API endpoints - 60 per minute
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/booking', [BookingController::class, 'store']);
});

// Critical operations - 10 per minute
Route::post('/withdrawal', [WithdrawalController::class, 'store'])
    ->middleware('throttle:10,1');
```

## 5. Authentication Security

### 5.1 Password Hashing (Already Implemented)
```php
// Always use bcrypt or Hash::make()
$user->password = bcrypt($request->password);
// or
$user->password = Hash::make($request->password);
```

### 5.2 Secure Session Configuration
Update `config/session.php`:
```php
return [
    'lifetime' => 120, // 2 hours
    'expire_on_close' => false,
    'encrypt' => true,
    'http_only' => true, // Prevent JS access to cookies
    'same_site' => 'lax', // CSRF protection
    'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only in production
];
```

### 5.3 Sanctum Configuration
Update `config/sanctum.php`:
```php
return [
    'expiration' => 525600, // 1 year in minutes
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),
    'middleware' => [
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    ],
];
```

## 6. Input Validation Best Practices

### 6.1 Create Form Requests
```php
// app/Http/Requests/CreateBookingRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'mitra_id' => 'required|exists:users,id',
            'service_type' => 'required|string|in:cuci_setrika,setrika,cuci_sepatu',
            'booking_date' => 'required|date|after:now',
            'pickup_address' => 'required|string|max:500',
            'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'mitra_id.exists' => 'Mitra tidak ditemukan',
            'booking_date.after' => 'Tanggal booking harus setelah sekarang',
            'phone_number.regex' => 'Format nomor telepon tidak valid',
        ];
    }
}
```

### 6.2 Use in Controller
```php
public function store(CreateBookingRequest $request)
{
    // $request is already validated
    $booking = Booking::create($request->validated());
    return response()->json($booking);
}
```

## 7. Authorization Policies

### 7.1 Create Policy
```bash
php artisan make:policy BookingPolicy --model=Booking
```

File: `app/Policies/BookingPolicy.php`
```php
<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking)
    {
        return $user->id === $booking->customer_id || 
               $user->id === $booking->mitra_id || 
               $user->role === 'admin';
    }

    public function update(User $user, Booking $booking)
    {
        return $user->id === $booking->mitra_id || $user->role === 'admin';
    }

    public function cancel(User $user, Booking $booking)
    {
        return $user->id === $booking->customer_id && 
               in_array($booking->status, ['pending', 'confirmed']);
    }
}
```

### 7.2 Register Policy
Update `app/Providers/AppServiceProvider.php`:
```php
use App\Models\Booking;
use App\Policies\BookingPolicy;
use Illuminate\Support\Facades\Gate;

public function boot()
{
    Gate::policy(Booking::class, BookingPolicy::class);
}
```

### 7.3 Use in Controller
```php
public function show(Booking $booking)
{
    $this->authorize('view', $booking);
    return response()->json($booking);
}

public function updateStatus(Request $request, Booking $booking)
{
    $this->authorize('update', $booking);
    // ... update logic
}
```

## 8. Headers Security

### 8.1 Create Security Headers Middleware
File: `app/Http/Middleware/SecurityHeaders.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
```

### 8.2 Register in bootstrap/app.php
```php
$middleware->web(append: [
    \App\Http\Middleware\SecurityHeaders::class,
    // ... other middlewares
]);
```

## 9. File Upload Security

### 9.1 Validate File Uploads
```php
public function uploadPhoto(Request $request)
{
    $request->validate([
        'photo' => [
            'required',
            'image',
            'mimes:jpeg,jpg,png',
            'max:2048', // 2MB
            'dimensions:max_width=2000,max_height=2000'
        ]
    ]);

    $path = $request->file('photo')->store('photos', 'public');
    
    return response()->json(['path' => $path]);
}
```

### 9.2 Secure File Storage
Update `.env`:
```env
FILESYSTEM_DISK=public
```

Update `config/filesystems.php`:
```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
],
```

## 10. Environment Security

### 10.1 Production .env Settings
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_LEVEL=error

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### 10.2 Hide Sensitive Information
Update `config/app.php`:
```php
'debug_blacklist' => [
    '_ENV' => [
        'APP_KEY',
        'DB_PASSWORD',
        'PUSHER_APP_SECRET',
        'MAIL_PASSWORD',
    ],
    '_SERVER' => [
        'APP_KEY',
        'DB_PASSWORD',
    ],
    '_POST' => [
        'password',
        'password_confirmation',
    ],
],
```

## 11. Logging & Monitoring

### 11.1 Log Security Events
```php
use Illuminate\Support\Facades\Log;

// Log failed login attempts
Log::warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent()
]);

// Log suspicious activity
Log::error('Suspicious activity detected', [
    'user_id' => auth()->id(),
    'action' => 'unauthorized_access_attempt',
    'resource' => $request->path()
]);
```

## 12. Database Security

### 12.1 Use Transactions for Critical Operations
```php
use Illuminate\Support\Facades\DB;

public function processWithdrawal(Request $request)
{
    DB::beginTransaction();
    
    try {
        $withdrawal = Withdrawal::create([...]);
        $mitra->decrement('balance', $amount);
        
        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Withdrawal failed', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Transaction failed'], 500);
    }
}
```

### 12.2 Sensitive Data Encryption
```php
use Illuminate\Support\Facades\Crypt;

// Encrypt sensitive data
$user->bank_account = Crypt::encryptString($request->bank_account);
$user->save();

// Decrypt when needed
$bankAccount = Crypt::decryptString($user->bank_account);
```

## Security Checklist

### Backend
- [x] CSRF protection enabled
- [x] SQL injection prevention (Eloquent ORM)
- [x] Password hashing (bcrypt)
- [x] Input validation
- [x] Rate limiting
- [x] Authentication & authorization
- [x] Secure session configuration
- [x] Security headers
- [x] File upload validation
- [x] Environment security
- [x] Error logging
- [x] Database transactions

### Frontend
- [x] CSRF token in all requests
- [x] XSS prevention (output escaping)
- [x] Input sanitization
- [x] Secure cookies (httpOnly, secure, sameSite)
- [x] HTTPS enforcement
- [x] Content Security Policy

### Deployment
- [ ] HTTPS/SSL certificate
- [ ] Firewall configuration
- [ ] Regular backups
- [ ] Security updates
- [ ] Monitoring & alerting
- [ ] DDoS protection

## Testing Security

```bash
# Run security audit
composer audit

# Check for vulnerabilities
npm audit

# Run tests
php artisan test
```

## Additional Resources
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
