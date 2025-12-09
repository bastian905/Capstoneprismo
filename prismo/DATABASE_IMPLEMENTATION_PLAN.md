# Database Implementation Plan - Mengganti Mock Data dengan Real Data

## 📋 Overview
Dokumen ini berisi rencana lengkap untuk mengganti semua mock data/localStorage dengan data real dari database MySQL menggunakan Laravel backend.

## 🗄️ Database Tables Yang Diperlukan

### 1. **users** (Already exists)
Tabel ini sudah ada dengan kolom:
- id, name, email, password, role, google_id, avatar
- email_verified_at, profile_completed, approval_status
- last_activity_at, deletion_warning_sent
- remember_token, created_at, updated_at

### 2. **mitra_profiles**
Detail profil untuk user dengan role='mitra'

```php
Schema::create('mitra_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('business_name');
    $table->string('contact_person');
    $table->string('phone');
    $table->text('address');
    $table->string('city');
    $table->string('province');
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->text('description')->nullable();
    
    // Jam operasional (JSON: {senin: "08:00-17:00", ...})
    $table->json('operational_hours')->nullable();
    $table->json('break_times')->nullable(); // {start: "12:00", end: "13:00"}
    
    // Dokumen (paths to storage)
    $table->string('ktp_photo')->nullable();
    $table->string('qris_photo')->nullable();
    $table->string('legal_doc')->nullable();
    
    // Galeri (JSON array of paths)
    $table->json('facility_photos')->nullable();
    
    // Rating & Stats
    $table->decimal('rating', 2, 1)->default(0);
    $table->integer('review_count')->default(0);
    $table->integer('total_bookings')->default(0);
    $table->decimal('balance', 12, 2)->default(0);
    
    $table->timestamps();
});
```

### 3. **bookings**
Semua transaksi booking dari customer

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->string('booking_code')->unique(); // BOOK001, BOOK002, etc
    $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
    
    // Detail booking
    $table->string('service_type'); // Cuci Mobil, Steam, dll
    $table->string('vehicle_type'); // SUV, Sedan, dll
    $table->string('vehicle_plate'); // B 1234 XX
    $table->date('booking_date');
    $table->time('booking_time');
    
    // Pricing
    $table->decimal('base_price', 10, 2);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('final_price', 10, 2);
    $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null');
    
    // Payment
    $table->string('payment_method'); // QRIS, E-Wallet
    $table->string('payment_proof')->nullable(); // Path to uploaded image
    $table->enum('payment_status', ['pending', 'confirmed', 'failed'])->default('pending');
    
    // Status workflow
    $table->enum('status', ['menunggu', 'proses', 'selesai', 'dibatalkan'])->default('menunggu');
    $table->timestamp('confirmed_at')->nullable();
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->text('cancellation_reason')->nullable();
    
    $table->timestamps();
    
    // Indexes
    $table->index('booking_code');
    $table->index(['customer_id', 'status']);
    $table->index(['mitra_id', 'booking_date']);
    $table->index('status');
});
```

### 4. **vouchers**
Voucher yang dibuat admin

```php
Schema::create('vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // NEWUSER10, CASHBACK15
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('type', ['discount', 'cashback', 'free_service'])->default('discount');
    
    // Discount details
    $table->decimal('discount_percent', 5, 2)->nullable(); // 10.00 = 10%
    $table->decimal('discount_fixed', 10, 2)->nullable(); // Rp 15000
    $table->decimal('max_discount', 10, 2)->nullable(); // Max Rp 25000
    $table->decimal('min_transaction', 10, 2)->default(0);
    
    // Validity
    $table->date('start_date')->nullable();
    $table->date('end_date');
    $table->integer('max_usage')->nullable(); // null = unlimited
    $table->integer('current_usage')->default(0);
    $table->integer('max_usage_per_user')->default(1);
    
    // Terms (JSON array)
    $table->json('terms')->nullable();
    
    // Status
    $table->boolean('is_active')->default(true);
    
    $table->timestamps();
});
```

### 5. **user_vouchers**
Tracking voucher yang di-claim user

```php
Schema::create('user_vouchers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
    
    $table->timestamp('claimed_at');
    $table->timestamp('used_at')->nullable();
    $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
    
    $table->timestamps();
    
    // Unique: user can only claim each voucher once
    $table->unique(['user_id', 'voucher_id']);
});
```

### 6. **reviews**
Review dari customer untuk mitra

```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
    
    // Review content
    $table->integer('rating'); // 1-5
    $table->text('comment');
    $table->json('review_photos')->nullable(); // Array of photo paths
    
    // Mitra response
    $table->text('mitra_reply')->nullable();
    $table->timestamp('replied_at')->nullable();
    
    $table->timestamps();
    
    // Indexes
    $table->index('mitra_id');
    $table->index('rating');
    $table->unique('booking_id'); // One review per booking
});
```

### 7. **withdrawals**
Penarikan saldo mitra

```php
Schema::create('withdrawals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
    
    $table->decimal('amount', 12, 2);
    $table->string('bank_name')->nullable();
    $table->string('account_number')->nullable();
    $table->string('account_name')->nullable();
    $table->string('qris_image')->nullable(); // For QRIS withdrawal
    
    $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
    $table->text('admin_note')->nullable();
    $table->timestamp('processed_at')->nullable();
    $table->foreignId('processed_by')->nullable()->constrained('users');
    
    $table->timestamps();
    
    $table->index(['mitra_id', 'status']);
});
```

### 8. **customer_profiles** (Optional)
Detail profil customer

```php
Schema::create('customer_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    
    // Stats
    $table->integer('total_bookings')->default(0);
    $table->integer('loyalty_points')->default(0);
    
    $table->timestamps();
});
```

## 🎯 Implementation Priority

### Phase 1: Core Tables (CRITICAL - Week 1)
1. ✅ users (already exists)
2. ⏳ mitra_profiles
3. ⏳ bookings
4. ⏳ reviews

### Phase 2: Features (HIGH - Week 2)
5. ⏳ vouchers
6. ⏳ user_vouchers
7. ⏳ withdrawals

### Phase 3: Enhancement (MEDIUM - Week 3)
8. ⏳ customer_profiles
9. ⏳ notifications table
10. ⏳ settings/configurations table

## 📝 API Endpoints Yang Diperlukan

### Auth & User Management
```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/user
PUT    /api/auth/user/profile
POST   /api/auth/user/avatar
```

### Mitra Profile
```
GET    /api/mitra/profile
POST   /api/mitra/profile
PUT    /api/mitra/profile
POST   /api/mitra/documents/upload
POST   /api/mitra/gallery/upload
DELETE /api/mitra/gallery/{id}
GET    /api/mitra/stats
GET    /api/mitra/balance
```

### Booking Management
```
// Customer
GET    /api/bookings (list customer bookings)
POST   /api/bookings (create booking)
GET    /api/bookings/{id}
PUT    /api/bookings/{id}/cancel
POST   /api/bookings/{id}/payment-proof

// Mitra
GET    /api/mitra/bookings (list mitra bookings)
PUT    /api/mitra/bookings/{id}/status (update: proses/selesai)
GET    /api/mitra/bookings/queue (antrian hari ini)
```

### Admin - Booking
```
GET    /api/admin/bookings
GET    /api/admin/bookings/{id}
PUT    /api/admin/bookings/{id}/confirm-payment
PUT    /api/admin/bookings/{id}/reject-payment
```

### Admin - Mitra Approval
```
GET    /api/admin/mitra/pending
PUT    /api/admin/mitra/{id}/approve
PUT    /api/admin/mitra/{id}/reject
GET    /api/admin/mitra (all mitra)
PUT    /api/admin/mitra/{id}/ban
```

### Admin - Customer Management
```
GET    /api/admin/customers
GET    /api/admin/customers/{id}
PUT    /api/admin/customers/{id}/ban
```

### Voucher System
```
// Admin
GET    /api/admin/vouchers
POST   /api/admin/vouchers
PUT    /api/admin/vouchers/{id}
DELETE /api/admin/vouchers/{id}

// Customer
GET    /api/vouchers (available vouchers)
POST   /api/vouchers/{id}/claim
GET    /api/user/vouchers (my vouchers)
POST   /api/vouchers/validate (check if voucher can be used)
```

### Review System
```
GET    /api/mitra/{id}/reviews
POST   /api/bookings/{id}/review
PUT    /api/reviews/{id}/reply (mitra reply)
GET    /api/user/reviews (my reviews)
```

### Withdrawal
```
// Mitra
GET    /api/mitra/withdrawals
POST   /api/mitra/withdrawals
GET    /api/mitra/balance-history

// Admin
GET    /api/admin/withdrawals
PUT    /api/admin/withdrawals/{id}/approve
PUT    /api/admin/withdrawals/{id}/reject
```

## 🔨 Implementation Steps

### Step 1: Create All Migrations
```bash
# Already created
php artisan make:migration create_mitra_profiles_table
php artisan make:migration create_bookings_table
php artisan make:migration create_vouchers_table
php artisan make:migration create_user_vouchers_table
php artisan make:migration create_reviews_table
php artisan make:migration create_withdrawals_table
php artisan make:migration create_customer_profiles_table
```

### Step 2: Create Models
```bash
php artisan make:model MitraProfile
php artisan make:model Booking
php artisan make:model Voucher
php artisan make:model UserVoucher
php artisan make:model Review
php artisan make:model Withdrawal
php artisan make:model CustomerProfile
```

### Step 3: Create API Controllers
```bash
php artisan make:controller Api/AuthController
php artisan make:controller Api/BookingController
php artisan make:controller Api/MitraController
php artisan make:controller Api/VoucherController
php artisan make:controller Api/ReviewController
php artisan make:controller Api/WithdrawalController
php artisan make:controller Api/Admin/AdminBookingController
php artisan make:controller Api/Admin/AdminMitraController
php artisan make:controller Api/Admin/AdminCustomerController
php artisan make:controller Api/Admin/AdminVoucherController
php artisan make:controller Api/Admin/AdminWithdrawalController
```

### Step 4: Setup API Routes
File: `routes/api.php`

```php
// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Customer routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/{id}', [BookingController::class, 'show']);
        Route::put('/{id}/cancel', [BookingController::class, 'cancel']);
        Route::post('/{id}/payment-proof', [BookingController::class, 'uploadPaymentProof']);
        Route::post('/{id}/review', [ReviewController::class, 'store']);
    });
    
    // Mitra routes
    Route::middleware('role:mitra')->prefix('mitra')->group(function () {
        Route::get('/profile', [MitraController::class, 'getProfile']);
        Route::post('/profile', [MitraController::class, 'updateProfile']);
        Route::get('/bookings', [MitraController::class, 'getBookings']);
        Route::put('/bookings/{id}/status', [MitraController::class, 'updateBookingStatus']);
        Route::get('/withdrawals', [WithdrawalController::class, 'index']);
        Route::post('/withdrawals', [WithdrawalController::class, 'request']);
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/bookings', [AdminBookingController::class, 'index']);
        Route::put('/bookings/{id}/confirm', [AdminBookingController::class, 'confirmPayment']);
        Route::get('/mitra', [AdminMitraController::class, 'index']);
        Route::put('/mitra/{id}/approve', [AdminMitraController::class, 'approve']);
        Route::get('/customers', [AdminCustomerController::class, 'index']);
        Route::resource('/vouchers', AdminVoucherController::class);
        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index']);
        Route::put('/withdrawals/{id}/process', [AdminWithdrawalController::class, 'process']);
    });
});
```

### Step 5: Update Frontend JavaScript
Untuk setiap file JavaScript yang menggunakan mock data, ganti dengan fetch API:

**Before (Mock Data)**:
```javascript
const mockData = {
    bookings: [...]
};
function loadMockData() {
    renderTableData();
}
```

**After (Real API)**:
```javascript
async function loadBookings() {
    try {
        const response = await fetch('/api/bookings', {
            headers: {
                'Authorization': `Bearer ${getAuthToken()}`,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        renderTableData(data.bookings);
    } catch (error) {
        console.error('Error loading bookings:', error);
        showNotification('Gagal memuat data booking', 'error');
    }
}
```

## 📊 Data Migration Strategy

### Option 1: Fresh Start (Recommended for Development)
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Option 2: Keep Existing Users
```bash
php artisan migrate
# Manual data import if needed
```

## 🧪 Testing Checklist

- [ ] User registration & login
- [ ] Mitra profile creation & update
- [ ] Customer booking creation
- [ ] Mitra booking management (queue)
- [ ] Admin booking approval
- [ ] Admin mitra approval
- [ ] Voucher claim & usage
- [ ] Review submission & reply
- [ ] Withdrawal request & approval
- [ ] File upload (payment proof, documents, photos)
- [ ] Email notifications
- [ ] Real-time updates (if implemented)

## 🚀 Deployment Checklist

- [ ] Run migrations on production database
- [ ] Setup storage link: `php artisan storage:link`
- [ ] Configure email settings (.env)
- [ ] Setup scheduler cron job
- [ ] Update frontend URLs to production API
- [ ] Test all API endpoints
- [ ] Load test critical endpoints
- [ ] Setup database backups
- [ ] Configure CORS properly
- [ ] Enable rate limiting on API routes
- [ ] Setup monitoring (Laravel Telescope in development)

## 📝 Notes

1. **Authentication**: Gunakan Laravel Sanctum untuk API authentication
2. **File Storage**: Semua upload file menggunakan `Storage::disk('public')`
3. **Validation**: Gunakan Form Requests untuk semua input validation
4. **Authorization**: Gunakan Policy untuk resource authorization
5. **Pagination**: Semua list endpoint harus support pagination
6. **Error Handling**: Consistent error response format across all API
7. **Logging**: Log semua critical operations (booking, payment, withdrawal)
8. **Caching**: Cache mitra profile, reviews untuk performance

## ⚠️ Breaking Changes

Setelah implementasi database:
1. localStorage akan dihapus untuk user data
2. All mock data di JavaScript akan dihapus
3. Frontend akan fully depend on API responses
4. Session-based auth akan diganti dengan token-based (Sanctum)
