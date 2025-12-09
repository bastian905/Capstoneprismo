# 🔍 QUICK REFERENCE - DATABASE FIELDS

## ⚠️ JANGAN GUNAKAN FIELD INI (TIDAK ADA DI DATABASE!)

### ❌ Voucher
```php
// SALAH:
$voucher->name              // ❌ TIDAK ADA
$voucher->discount_type     // ❌ TIDAK ADA
$voucher->discount_value    // ❌ TIDAK ADA
$voucher->valid_until       // ❌ TIDAK ADA
$voucher->min_purchase      // ❌ TIDAK ADA
$voucher->quota             // ❌ TIDAK ADA
```

### ❌ UserVoucher
```php
// SALAH:
$userVoucher->is_used       // ❌ TIDAK ADA
```

### ❌ Review
```php
// SALAH:
$review->photos             // ❌ TIDAK ADA
$review->mitra_response     // ❌ TIDAK ADA
```

---

## ✅ GUNAKAN FIELD INI (BENAR!)

### ✅ Voucher Model
```php
// Model: App\Models\Voucher
// Table: vouchers

$voucher->id                        // Primary key
$voucher->code                      // string, unique
$voucher->title                     // ✅ string (bukan 'name'!)
$voucher->description               // text, nullable
$voucher->type                      // ✅ enum: 'discount', 'cashback', 'free_service'

// Discount fields:
$voucher->discount_percent          // ✅ decimal(5,2), nullable
$voucher->discount_fixed            // ✅ decimal(10,2), nullable
$voucher->max_discount              // decimal(10,2), nullable
$voucher->min_transaction           // ✅ decimal(10,2) (bukan 'min_purchase'!)

// Date fields:
$voucher->start_date                // ✅ date, nullable
$voucher->end_date                  // ✅ date (bukan 'valid_until'!)

// Usage limits:
$voucher->max_usage                 // ✅ integer, nullable (bukan 'quota'!)
$voucher->current_usage             // ✅ integer, default 0
$voucher->max_usage_per_user        // integer, default 1

// Other:
$voucher->terms                     // json, nullable (auto cast to array)
$voucher->is_active                 // boolean, default true
$voucher->created_at                // timestamp
$voucher->updated_at                // timestamp

// Relationships:
$voucher->userVouchers              // hasMany(UserVoucher)
$voucher->users                     // belongsToMany(User)
```

### ✅ UserVoucher Model
```php
// Model: App\Models\UserVoucher
// Table: user_vouchers

$userVoucher->id                    // Primary key
$userVoucher->user_id               // Foreign key to users
$userVoucher->voucher_id            // Foreign key to vouchers
$userVoucher->claimed_at            // ✅ timestamp
$userVoucher->used_at               // ✅ timestamp, nullable
$userVoucher->booking_id            // Foreign key to bookings, nullable
$userVoucher->created_at            // timestamp
$userVoucher->updated_at            // timestamp

// Relationships:
$userVoucher->user                  // belongsTo(User)
$userVoucher->voucher               // belongsTo(Voucher)
$userVoucher->booking               // belongsTo(Booking)

// Check if used:
$isUsed = $userVoucher->used_at !== null;  // ✅ CORRECT WAY
// NOT: $userVoucher->is_used  ❌ FIELD TIDAK ADA!
```

### ✅ Review Model
```php
// Model: App\Models\Review
// Table: reviews

$review->id                         // Primary key
$review->booking_id                 // Foreign key to bookings
$review->customer_id                // Foreign key to users (customer)
$review->mitra_id                   // Foreign key to users (mitra)

// Review content:
$review->rating                     // integer
$review->comment                    // text
$review->review_photos              // ✅ json, nullable (bukan 'photos'!)

// Mitra response:
$review->mitra_reply                // ✅ text, nullable (bukan 'mitra_response'!)
$review->replied_at                 // timestamp, nullable

$review->created_at                 // timestamp
$review->updated_at                 // timestamp

// Relationships:
$review->booking                    // belongsTo(Booking)
$review->customer                   // belongsTo(User, 'customer_id')
$review->mitra                      // belongsTo(User, 'mitra_id')
```

---

## 📝 CONTOH USAGE YANG BENAR

### Voucher - Determine Discount Type
```php
// ✅ CORRECT:
$discountType = $voucher->discount_percent ? 'percentage' : 'fixed';
$discountValue = $voucher->discount_percent ?? $voucher->discount_fixed;

$display = $discountType === 'percentage'
    ? $discountValue . '%'
    : 'Rp ' . number_format($discountValue, 0, ',', '.');

// ❌ WRONG:
$type = $voucher->discount_type;  // Field tidak ada!
```

### Voucher - Display Info
```php
// ✅ CORRECT:
echo $voucher->title;              // "Diskon 20%"
echo $voucher->end_date;           // "2025-12-31"
echo $voucher->min_transaction;    // 50000
echo $voucher->max_usage;          // 100

// ❌ WRONG:
echo $voucher->name;               // Undefined property!
echo $voucher->valid_until;        // Undefined property!
echo $voucher->min_purchase;       // Undefined property!
echo $voucher->quota;              // Undefined property!
```

### UserVoucher - Check if Used
```php
// ✅ CORRECT:
$isUsed = $userVoucher->used_at !== null;
$status = $userVoucher->used_at ? 'used' : 'claimed';

// ❌ WRONG:
$isUsed = $userVoucher->is_used;  // Field tidak ada!
```

### Review - Get Photos and Reply
```php
// ✅ CORRECT:
$photos = $review->review_photos;  // Already cast to array by model
$reply = $review->mitra_reply;

// If JSON string (from controller mapping):
$photos = is_string($review->review_photos)
    ? json_decode($review->review_photos, true)
    : $review->review_photos;

// ❌ WRONG:
$photos = $review->photos;         // Undefined property!
$reply = $review->mitra_response;  // Undefined property!
```

### Review - In Controller Mapping
```php
// ✅ CORRECT:
return [
    'photos' => $review->review_photos 
        ? (is_string($review->review_photos) 
            ? json_decode($review->review_photos, true) 
            : $review->review_photos) 
        : [],
    'mitraResponse' => $review->mitra_reply,
];

// ❌ WRONG:
return [
    'photos' => json_decode($review->photos ?? '[]'),  // Field salah!
    'mitraResponse' => $review->mitra_response,  // Field salah!
];
```

---

## 🎯 MIGRATION REFERENCE

### Vouchers Migration
```php
Schema::create('vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('title');  // ✅ NOT 'name'
    $table->text('description')->nullable();
    $table->enum('type', ['discount', 'cashback', 'free_service'])->default('discount');
    
    $table->decimal('discount_percent', 5, 2)->nullable();  // ✅
    $table->decimal('discount_fixed', 10, 2)->nullable();   // ✅
    $table->decimal('max_discount', 10, 2)->nullable();
    $table->decimal('min_transaction', 10, 2)->default(0);  // ✅ NOT 'min_purchase'
    
    $table->date('start_date')->nullable();
    $table->date('end_date');  // ✅ NOT 'valid_until'
    $table->integer('max_usage')->nullable();  // ✅ NOT 'quota'
    $table->integer('current_usage')->default(0);
    $table->integer('max_usage_per_user')->default(1);
    
    $table->json('terms')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### UserVouchers Migration
```php
Schema::create('user_vouchers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
    
    $table->timestamp('claimed_at');
    $table->timestamp('used_at')->nullable();  // ✅ NOT boolean 'is_used'
    $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
    
    $table->timestamps();
    $table->unique(['user_id', 'voucher_id']);
});
```

### Reviews Migration
```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained()->onDelete('cascade');
    $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('mitra_id')->constrained('users')->onDelete('cascade');
    
    $table->integer('rating');
    $table->text('comment');
    $table->json('review_photos')->nullable();  // ✅ NOT 'photos'
    
    $table->text('mitra_reply')->nullable();    // ✅ NOT 'mitra_response'
    $table->timestamp('replied_at')->nullable();
    
    $table->timestamps();
    $table->index('mitra_id');
    $table->index('rating');
    $table->unique('booking_id');
});
```

---

## 🚨 COMMON MISTAKES

1. **Using `$voucher->name` instead of `$voucher->title`**
   ```php
   ❌ $voucher->name
   ✅ $voucher->title
   ```

2. **Checking `$userVoucher->is_used`**
   ```php
   ❌ if ($userVoucher->is_used)
   ✅ if ($userVoucher->used_at !== null)
   ```

3. **Using `$review->photos`**
   ```php
   ❌ $review->photos
   ✅ $review->review_photos
   ```

4. **Using `$review->mitra_response`**
   ```php
   ❌ $review->mitra_response
   ✅ $review->mitra_reply
   ```

5. **Using hardcoded discount type**
   ```php
   ❌ $voucher->discount_type
   ✅ $voucher->discount_percent ? 'percentage' : 'fixed'
   ```

---

**📌 REMEMBER**: Selalu cek migration file untuk field names yang benar!

*Updated: <?= date('Y-m-d H:i:s') ?>*
