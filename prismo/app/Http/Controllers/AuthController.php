<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Login with email and password
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            // Check if email is verified
            if (!Auth::user()->email_verified_at) {
                return redirect()->route('verification.notice');
            }
            
            // Redirect based on role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }
            
            if (Auth::user()->role === 'mitra') {
                // Jika mitra belum complete profile, redirect ke form
                if (!Auth::user()->profile_completed) {
                    return redirect()->intended('/mitra/form-mitra');
                }
                
                // Jika status pending atau rejected, redirect ke form untuk mengisi ulang
                if (Auth::user()->approval_status === 'pending' || Auth::user()->approval_status === 'rejected') {
                    return redirect()->intended('/mitra/form-mitra');
                }
                
                return redirect()->intended('/dashboard-mitra');
            }
            
            return redirect()->intended('/dashboard');
        }

        return back()->with('error', 'Email atau password salah');
    }

    // Register new user
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
            'terms' => 'accepted',
            'role' => 'required|in:customer,mitra',
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.',
        ]);

        $user = User::create([
            'name' => explode('@', $request->email)[0],
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'avatar' => '/images/profile.png',
        ]);

        Auth::login($user);
        
        // Send verification email
        $this->sendVerificationEmail($user->email);
        
        // Store expiry time in cache (persists across sessions)
        cache()->put('verification_expiry_' . $user->id, now()->addMinutes(5)->timestamp, now()->addMinutes(10));

        return redirect()->route('verification.notice');
    }

    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Google OAuth: Redirect to Google
    public function redirectToGoogle(Request $request)
    {
        // Save role and action (login/register) to session before redirecting
        if ($request->has('role')) {
            session(['oauth_role' => $request->role]);
        }
        
        if ($request->has('action')) {
            session(['oauth_action' => $request->action]);
        }
        
        return Socialite::driver('google')->redirect();
    }

    // Google OAuth: Handle callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $action = session('oauth_action', 'login'); // Default login jika tidak ada parameter
            
            // Check if email already exists
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            // CASE 1: LOGIN ACTION
            if ($action === 'login') {
                if ($existingUser) {
                    // If user exists but doesn't have google_id (registered via email/password)
                    if (!$existingUser->google_id) {
                        // Update google_id untuk enable OAuth login next time
                        $existingUser->google_id = $googleUser->getId();
                        // Set Google avatar if user doesn't have one or has default
                        if (!$existingUser->avatar || $existingUser->avatar === '/images/profile.png') {
                            $existingUser->avatar = $googleUser->getAvatar();
                        }
                        $existingUser->save();
                    } else {
                        // User already has google_id, update avatar to latest Google avatar
                        $existingUser->avatar = $googleUser->getAvatar();
                        $existingUser->save();
                    }
                    
                    // Update email_verified_at if not set (Google auto-verifies)
                    if (!$existingUser->email_verified_at) {
                        $existingUser->email_verified_at = now();
                        $existingUser->save();
                    }
                    
                    // If user exists, login
                    Auth::login($existingUser);
                    
                    // Clear session
                    session()->forget(['oauth_role', 'oauth_action']);
                    
                    // Redirect based on role
                    if ($existingUser->role === 'admin') {
                        return redirect()->intended('/admin/dashboard');
                    }
                    
                    if ($existingUser->role === 'mitra') {
                        // Jika mitra belum complete profile, redirect ke form
                        if (!$existingUser->profile_completed) {
                            return redirect()->intended('/mitra/form-mitra');
                        }
                        
                        // Jika status pending atau rejected, redirect ke form untuk mengisi ulang
                        if ($existingUser->approval_status === 'pending' || $existingUser->approval_status === 'rejected') {
                            return redirect()->intended('/mitra/form-mitra');
                        }
                        
                        return redirect()->intended('/dashboard-mitra');
                    }
                    
                    return redirect()->intended('/dashboard');
                } else {
                    // Email tidak terdaftar saat LOGIN
                    session()->forget(['oauth_role', 'oauth_action']);
                    return redirect('/login')->with('error', 'Email belum terdaftar. Silakan daftar terlebih dahulu.');
                }
            }
            
            // CASE 2: REGISTER ACTION
            if ($action === 'register') {
                if ($existingUser) {
                    // Email sudah terdaftar saat REGISTER
                    session()->forget(['oauth_role', 'oauth_action']);
                    return redirect('/register')->with('error', 'Email sudah terdaftar. Silakan login.');
                } else {
                    // Create new user
                    $role = session('oauth_role', 'customer');
                    
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar() ?: '/images/profile.png',
                        'role' => $role,
                        'email_verified_at' => now(), // Google OAuth auto-verifies email
                    ]);

                    Auth::login($user);
                    
                    // Clear session
                    session()->forget(['oauth_role', 'oauth_action']);

                    // Redirect based on role
                    if ($user->role === 'mitra') {
                        // Mitra baru harus lengkapi profil
                        return redirect('/mitra/form-mitra');
                    }
                    
                    return redirect()->intended('/dashboard');
                }
            }
            
            // Fallback
            session()->forget(['oauth_role', 'oauth_action']);
            return redirect('/login')->with('error', 'Invalid OAuth request');
        } catch (\Exception $e) {
            session()->forget(['oauth_role', 'oauth_action']);
            return redirect('/login')->with('error', 'Google authentication failed');
        }
    }

    // Magic Link: Send email
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(15);

        // Store token in database
        DB::table('magic_link_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send email with magic link
        $magicLink = url('/auth/magic-link/verify?token=' . $token);

        Mail::raw("Click here to login: {$magicLink}\n\nThis link will expire in 15 minutes.", function ($message) use ($email) {
            $message->to($email)
                    ->subject('Your Magic Login Link');
        });

        return back()->with('success', 'Magic link sent! Check your email.');
    }

    // Magic Link: Verify and login
    public function verifyMagicLink(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect('/login')->with('error', 'Invalid magic link');
        }

        $magicLinkToken = DB::table('magic_link_tokens')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$magicLinkToken) {
            return redirect('/login')->with('error', 'Magic link expired or invalid');
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $magicLinkToken->email],
            ['name' => explode('@', $magicLinkToken->email)[0]]
        );

        // Delete used token
        DB::table('magic_link_tokens')->where('token', $token)->delete();

        Auth::login($user);

        return redirect('/dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Dashboard (protected route)
    public function dashboard()
    {
        $user = Auth::user();
        
        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        
        if ($user->role === 'mitra') {
            // Cek apakah mitra sudah melengkapi profil
            if (!$user->profile_completed) {
                return redirect('/mitra/form-mitra')->with('info', 'Silakan lengkapi profil Anda terlebih dahulu.');
            }
            
            // Cek status approval
            if ($user->approval_status === 'pending' || $user->approval_status === 'rejected') {
                return redirect('/mitra/form-mitra-pending');
            }
            
            if ($user->approval_status === 'approved') {
                return redirect('/dashboard-mitra');
            }
        }
        
        // Default: customer
        // Load approved mitras with their profiles
        $mitras = \App\Models\User::with('mitraProfile')
            ->where('role', 'mitra')
            ->where('approval_status', 'approved')
            ->whereHas('mitraProfile', function($query) {
                // Hanya mitra yang sudah input minimal 1 paket layanan
                $query->where(function($q) {
                    $q->where('basic_price', '>', 0)
                      ->orWhere('premium_price', '>', 0)
                      ->orWhere('complete_price', '>', 0);
                });
            })
            ->get()
            ->map(function($mitra) {
                $facilityPhotos = $mitra->mitraProfile->facility_photos ?? [];
                if (is_string($facilityPhotos)) {
                    $facilityPhotos = json_decode($facilityPhotos, true) ?? [];
                }
                
                return [
                    'id' => $mitra->id,
                    'name' => $mitra->mitraProfile->business_name ?? $mitra->name,
                    'image' => !empty($facilityPhotos) ? asset('storage/' . $facilityPhotos[0]) : asset('images/gambar2.png'),
                    'rating' => $mitra->mitraProfile->rating ?? 0,
                    'reviews' => $mitra->mitraProfile->total_reviews ?? 0,
                    'location' => $mitra->mitraProfile->address ?? '',
                    'provinsi' => $mitra->mitraProfile->province ?? '',
                    'kota' => $mitra->mitraProfile->city ?? '',
                    'distance' => '0 km',
                    'prices' => [
                        'basic' => $mitra->mitraProfile->basic_price ?? 0,
                        'premium' => $mitra->mitraProfile->premium_price ?? 0,
                        'complete' => $mitra->mitraProfile->complete_price ?? 0
                    ],
                    'status' => ($mitra->mitraProfile->is_open ?? false) ? 'open' : 'closed',
                    'closingTime' => $mitra->mitraProfile->closing_time ?? '22:00'
                ];
            });
            
        return view('customer.dashboard.dashU', compact('mitras'));
    }
    
    // Send verification email
    protected function sendVerificationEmail($email)
    {
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(5);

        // Delete old tokens
        DB::table('email_verification_tokens')
            ->where('email', $email)
            ->delete();

        // Store new token
        DB::table('email_verification_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get user name
        $user = User::where('email', $email)->first();
        $userName = $user ? $user->name : 'User';
        
        // Send email with HTML template
        $verificationLink = url('/email/verify?token=' . $token);

        Mail::send('emails.verify-email', [
            'verificationLink' => $verificationLink,
            'userName' => $userName
        ], function ($message) use ($email) {
            $message->to($email)
                    ->subject('Verifikasi Email - Prismo');
        });
    }
    
    // Show verification notice page
    public function verificationNotice()
    {
        if (Auth::user()->email_verified_at) {
            // Redirect based on role
            if (Auth::user()->role === 'mitra') {
                return redirect('/dashboard-mitra');
            }
            return redirect('/dashboard');
        }
        
        // Get expiry time from cache (persists across sessions/logout)
        $cacheKey = 'verification_expiry_' . Auth::id();
        $expiryTime = cache()->get($cacheKey);
        
        // If no expiry time exists, create one
        if (!$expiryTime) {
            $expiryTime = now()->addMinutes(5)->timestamp;
            cache()->put($cacheKey, $expiryTime, now()->addMinutes(10));
        }
        
        return view('auth.verifemail', ['expiryTime' => $expiryTime]);
    }
    
    // Resend verification email
    public function resendVerification(Request $request)
    {
        if (Auth::user()->email_verified_at) {
            return redirect('/dashboard');
        }
        
        $this->sendVerificationEmail(Auth::user()->email);
        
        // Reset expiry time in cache (5 minutes from now)
        $cacheKey = 'verification_expiry_' . Auth::id();
        $expiryTime = now()->addMinutes(5)->timestamp;
        cache()->put($cacheKey, $expiryTime, now()->addMinutes(10));
        
        return back()->with('success', 'Email verifikasi telah dikirim ulang!');
    }
    
    // Verify email
    public function verifyEmail(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('verification.notice')->with('error', 'Token verifikasi tidak valid');
        }

        $verificationToken = DB::table('email_verification_tokens')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationToken) {
            return redirect()->route('verification.notice')->with('error', 'Link verifikasi kadaluarsa atau tidak valid');
        }

        // Update user email_verified_at
        $user = User::where('email', $verificationToken->email)->first();
        
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
            
            // Delete used token
            DB::table('email_verification_tokens')->where('token', $token)->delete();
            
            Auth::login($user);
            
            // Redirect based on role
            if ($user->role === 'mitra') {
                // Cek apakah mitra sudah mengisi form profil
                if (!$user->profile_completed) {
                    return redirect('/mitra/form-mitra')->with('success', 'Email berhasil diverifikasi! Silakan lengkapi profil Anda.');
                }
                
                // Cek status approval
                if ($user->approval_status === 'pending' || $user->approval_status === 'rejected') {
                    return redirect('/mitra/form-mitra-pending');
                }
                
                return redirect('/dashboard-mitra')->with('success', 'Email berhasil diverifikasi!');
            }
            
            return redirect('/dashboard')->with('success', 'Email berhasil diverifikasi!');
        }

        return redirect()->route('verification.notice')->with('error', 'Terjadi kesalahan');
    }
}

