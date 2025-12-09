<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Boot method untuk handle cascade delete
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($user) {
            // Hapus foto profil dari storage
            if ($user->avatar && $user->avatar !== '/images/profile.png') {
                $avatarPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }
            }
            
            // Jika mitra, hapus profil dan semua file terkait
            if ($user->role === 'mitra' && $user->mitraProfile) {
                $profile = $user->mitraProfile;
                
                // Hapus facility photos
                if ($profile->facility_photos) {
                    $photos = is_array($profile->facility_photos) 
                        ? $profile->facility_photos 
                        : json_decode((string)$profile->facility_photos, true);
                    if (is_array($photos)) {
                        foreach ($photos as $photo) {
                            Storage::disk('public')->delete($photo);
                        }
                    }
                }
                
                // Hapus legal doc
                if ($profile->legal_doc) {
                    Storage::disk('public')->delete($profile->legal_doc);
                }
                
                // Hapus KTP
                if ($profile->ktp_photo) {
                    Storage::disk('public')->delete($profile->ktp_photo);
                }
                
                // Hapus QRIS
                if ($profile->qris_photo) {
                    Storage::disk('public')->delete($profile->qris_photo);
                }
                
                // Hapus profil dari database (cascade delete akan handle yang lain)
                $profile->delete();
            }
            
            // Jika customer, hapus profil dan foto review
            if ($user->role === 'customer') {
                // Hapus customer profile
                if ($user->customerProfile) {
                    $user->customerProfile->delete();
                }
                
                // Hapus foto review
                $reviewPath = "review-photos";
                if (Storage::disk('public')->exists($reviewPath)) {
                    foreach (Storage::disk('public')->files($reviewPath) as $file) {
                        if (str_contains($file, (string)$user->id)) {
                            Storage::disk('public')->delete($file);
                        }
                    }
                }
            }
            
            // Hapus bookings (sebagai customer)
            $user->customerBookings()->delete();
            
            // Hapus bookings (sebagai mitra)
            $user->mitraBookings()->delete();
            
            // Hapus reviews (sebagai customer)
            $user->reviews()->delete();
            
            // Hapus reviews (sebagai mitra)
            $user->mitraReviews()->delete();
            
            // Hapus withdrawals
            $user->withdrawals()->delete();
            
            // Hapus voucher claims
            \DB::table('user_vouchers')->where('user_id', $user->id)->delete();
            
            // Hapus email verification tokens
            \DB::table('email_verification_tokens')->where('email', $user->email)->delete();
            
            // Hapus magic link tokens
            \DB::table('magic_link_tokens')->where('email', $user->email)->delete();
        });
    }

    // Relationships
    public function mitraProfile()
    {
        return $this->hasOne(MitraProfile::class);
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function customerBookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function mitraBookings()
    {
        return $this->hasMany(Booking::class, 'mitra_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function mitraReviews()
    {
        return $this->hasMany(Review::class, 'mitra_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'mitra_id');
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers')
            ->withPivot('claimed_at', 'used_at', 'booking_id')
            ->withTimestamps();
    }
}
