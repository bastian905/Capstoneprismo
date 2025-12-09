# Session & Cookie Management

## Overview
Sistem ini mengimplementasikan pengelolaan session dan cookies yang aman dengan fitur-fitur berikut:

## Fitur-fitur

### 1. Session Timeout
- **Middleware**: `SessionTimeout`
- Otomatis logout user setelah tidak aktif selama waktu tertentu (default: 120 menit)
- Melindungi dari session hijacking
- Redirect ke login page dengan pesan error

### 2. Secure Cookies
- **Middleware**: `SecureCookies`
- Semua cookies menggunakan flag `Secure` (HTTPS only)
- Flag `HttpOnly` mencegah akses JavaScript (XSS protection)
- `SameSite=Strict` mencegah CSRF attacks

### 3. Cookie Consent
- **Middleware**: `CookieConsent`
- Banner otomatis muncul untuk user yang belum memberikan consent
- Opsi untuk menerima atau menolak cookies
- Otomatis hapus non-essential cookies jika ditolak

### 4. Remember Me
- **Middleware**: `RememberMe`
- User bisa tetap login setelah browser ditutup
- Token di-hash dengan SHA256 untuk keamanan
- Token expire otomatis setelah 30 hari

### 5. Session Cleanup
- **Command**: `php artisan session:cleanup`
- Hapus expired sessions dari database
- Bisa dijadwalkan dengan Laravel Scheduler

## Konfigurasi

### Environment Variables (.env)
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120              # Durasi session (menit)
SESSION_ENCRYPT=true              # Enkripsi session data
SESSION_SECURE_COOKIE=true        # HTTPS only
SESSION_HTTP_ONLY=true            # Prevent XSS
SESSION_SAME_SITE=strict          # CSRF protection
SESSION_EXPIRE_ON_CLOSE=true      # Session habis saat browser ditutup
SESSION_TABLE=sessions            # Tabel database untuk session
```

### Middleware Registration
Middleware sudah otomatis terdaftar di `bootstrap/app.php`:
```php
$middleware->web(append: [
    \App\Http\Middleware\SessionTimeout::class,
    \App\Http\Middleware\SecureCookies::class,
    \App\Http\Middleware\RememberMe::class,
    \App\Http\Middleware\CookieConsent::class,
]);
```

## Helper Functions

### session_cleanup()
Bersihkan expired sessions secara manual:
```php
session_cleanup();
```

### invalidate_all_sessions($userId)
Hapus semua session untuk user tertentu:
```php
invalidate_all_sessions(auth()->id());
```

### get_active_sessions($userId)
Dapatkan semua session aktif user:
```php
$sessions = get_active_sessions(auth()->id());
```

### set_remember_me($user, $remember)
Set remember me cookie:
```php
set_remember_me($user, true);
```

### clear_remember_me($user)
Hapus remember me cookie:
```php
clear_remember_me($user);
```

### regenerate_session()
Regenerate session ID untuk keamanan:
```php
regenerate_session();
```

## Penggunaan

### Login dengan Remember Me
```php
// Di LoginController
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    $remember = $request->boolean('remember');
    
    if (Auth::attempt($credentials, $remember)) {
        regenerate_session();
        
        if ($remember) {
            set_remember_me(auth()->user(), true);
        }
        
        return redirect()->intended('dashboard');
    }
    
    return back()->withErrors(['email' => 'Invalid credentials']);
}
```

### Logout
```php
public function logout(Request $request)
{
    clear_remember_me(auth()->user());
    Auth::logout();
    
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');
}
```

### Force Logout All Sessions
```php
public function logoutAllDevices()
{
    invalidate_all_sessions(auth()->id());
    clear_remember_me(auth()->user());
    Auth::logout();
    
    return redirect()->route('login')
        ->with('success', 'Logged out from all devices');
}
```

## Scheduled Tasks

Tambahkan di `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Cleanup expired sessions setiap hari
    $schedule->command('session:cleanup')->daily();
}
```

## Security Best Practices

1. **HTTPS Required**: Set `SESSION_SECURE_COOKIE=true` di production
2. **Session Encryption**: Set `SESSION_ENCRYPT=true` untuk enkripsi data
3. **Short Lifetime**: Gunakan lifetime yang reasonable (60-120 menit)
4. **Regenerate Session**: Regenerate session ID setelah login/privilege changes
5. **Logout All**: Berikan opsi user untuk logout dari semua device

## Testing

### Test Session Timeout
```bash
# Login, tunggu SESSION_LIFETIME menit, akses halaman
# Harus redirect ke login dengan pesan timeout
```

### Test Remember Me
```bash
# Login dengan remember me checked
# Tutup browser, buka lagi
# Harus tetap login
```

### Test Cookie Consent
```bash
# Akses aplikasi pertama kali
# Banner harus muncul
# Setelah accept/decline, banner hilang
```

## Troubleshooting

### Session tidak tersimpan
- Pastikan tabel `sessions` ada di database
- Run: `php artisan migrate`

### Remember Me tidak bekerja
- Pastikan tabel `users` punya kolom `remember_token`
- Cookie harus diset sebelum response dikirim

### Cookie Consent banner tidak muncul
- Check apakah sudah ada cookie `cookie_consent`
- Clear browser cookies

## Database Migration

Pastikan tabel sessions ada:
```bash
php artisan session:table
php artisan migrate
```

## Notes

- Session timeout menggunakan timestamp, bukan Laravel session lifetime
- Remember me token di-hash untuk keamanan
- Cookie consent hanya untuk web routes, tidak untuk API
- Secure cookies memerlukan HTTPS di production
