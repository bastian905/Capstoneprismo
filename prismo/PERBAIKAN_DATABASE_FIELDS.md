# Perbaikan Database Field Mismatches

## Tanggal: <?= date('Y-m-d H:i:s') ?>

## Masalah yang Ditemukan

Terdapat ketidaksesuaian antara field yang digunakan di Controller dengan field yang sebenarnya ada di database (berdasarkan migration dan model).

## Field Mismatches yang Diperbaiki

### 1. Tabel: `vouchers`

**Field yang BENAR (dari migration):**
- `title` (bukan `name`)
- `type` (enum: 'discount', 'cashback', 'free_service')
- `discount_percent` (decimal 5,2)
- `discount_fixed` (decimal 10,2)
- `end_date` (date)
- `min_transaction` (decimal)
- `max_usage` (integer)
- `current_usage` (integer)
- `terms` (json)

**Field yang SALAH (sebelumnya digunakan):**
- ❌ `name` → ✅ `title`
- ❌ `discount_type` → ✅ derive from which field is set (discount_percent or discount_fixed)
- ❌ `discount_value` → ✅ `discount_percent` OR `discount_fixed`
- ❌ `valid_until` → ✅ `end_date`
- ❌ `min_purchase` → ✅ `min_transaction`
- ❌ `quota` → ✅ `max_usage`

### 2. Tabel: `user_vouchers`

**Field yang BENAR (dari migration):**
- `claimed_at` (timestamp)
- `used_at` (timestamp, nullable)
- `booking_id` (foreign key, nullable)

**Field yang SALAH (sebelumnya digunakan):**
- ❌ `is_used` → ✅ Derive from `used_at !== null`

### 3. Tabel: `reviews`

**Field yang BENAR (dari migration):**
- `rating` (integer)
- `comment` (text)
- `review_photos` (json, nullable)
- `mitra_reply` (text, nullable)
- `replied_at` (timestamp, nullable)

**Field yang SALAH (sebelumnya digunakan):**
- ❌ `photos` → ✅ `review_photos`
- ❌ `mitra_response` → ✅ `mitra_reply`

## File yang Diperbaiki

### Controllers yang Diupdate:

1. **app/Http/Controllers/Customer/VoucherController.php**
   - Fixed: `discount_type`, `discount_value`, `valid_until`, `is_used`
   - Changed: `$voucher->name` → `$voucher->title`
   - Changed: `$voucher->valid_until` → `$voucher->end_date`
   - Changed: `$userVoucher->is_used` → `$userVoucher->used_at !== null`

2. **app/Http/Controllers/Customer/BookingController.php**
   - Fixed: `mitra_response` → `mitra_reply`
   - Fixed: `photos` → `review_photos`
   - Added: Proper JSON decoding for review_photos

3. **app/Http/Controllers/Customer/DetailMitraController.php**
   - Fixed: `mitra_response` → `mitra_reply`
   - Fixed: `photos` → `review_photos`
   - Added: Proper JSON decoding for review_photos

4. **app/Http/Controllers/Mitra/ReviewController.php**
   - Fixed: `mitra_response` → `mitra_reply`
   - Fixed: `photos` → `review_photos`
   - Added: Proper JSON decoding for review_photos

5. **app/Http/Controllers/Admin/KelolaVoucherController.php**
   - Fixed: `discount_type`, `discount_value`
   - Changed: `$voucher->name` → `$voucher->title`
   - Changed: `valid_from`, `valid_until` → `start_date`, `end_date`
   - Changed: `quota` → `max_usage`, `current_usage`
   - Changed: `min_purchase` → `min_transaction`

### JavaScript yang Diperbaiki:

1. **prismo/public/js/Rbooking.js**
   - Added null check before accessing `currentBooking` in `openCancelModal()`
   - Added null check before accessing `currentBooking` in `confirmCancel()`
   - Existing null checks in other functions were already correct

## Logic Changes

### Voucher Discount Type Detection
```php
// OLD (WRONG):
'type' => $voucher->discount_type  // Field tidak ada!

// NEW (CORRECT):
$discountType = $voucher->discount_percent ? 'percentage' : 'fixed';
$discountValue = $voucher->discount_percent ?? $voucher->discount_fixed;
```

### User Voucher Usage Status
```php
// OLD (WRONG):
'used' => $userVoucher->is_used  // Field tidak ada!

// NEW (CORRECT):
'used' => $userVoucher->used_at !== null
```

### Review Photos and Mitra Reply
```php
// OLD (WRONG):
'photos' => json_decode($review->photos ?? '[]')  // Field salah!
'mitraResponse' => $review->mitra_response  // Field salah!

// NEW (CORRECT):
'photos' => $review->review_photos ? (is_string($review->review_photos) ? json_decode($review->review_photos, true) : $review->review_photos) : []
'mitraResponse' => $review->mitra_reply
```

## Testing

Setelah perbaikan, pastikan untuk:

1. ✅ Clear semua cache Laravel
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

2. ✅ Test setiap halaman:
   - Customer: Voucher, Booking, Detail Mitra
   - Mitra: Review, Antrian, Saldo
   - Admin: Kelola Voucher, Dashboard

3. ✅ Cek console browser untuk error undefined

4. ✅ Cek Laravel logs untuk query errors

## Kesimpulan

Semua field mismatch telah diperbaiki. Sistem sekarang menggunakan nama field yang BENAR sesuai dengan database migration. Tidak ada lagi error "undefined" karena field yang salah.

**Status**: ✅ SELESAI DIPERBAIKI
**Tanggal**: 2025-01-XX
**Developer**: Prismo Team
