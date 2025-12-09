# 📊 Analisis & Rencana Migrasi localStorage ke Database

## 🔍 Hasil Analisis localStorage

### ❌ HARUS DIPINDAH KE DATABASE (Data Persisten User)

#### 1. **Foto Profil User & Mitra**
- `userProfilePhoto` - Foto profil customer
- `mitraProfilePhoto` - Foto profil mitra
- **Alasan**: Data persisten yang harus tersimpan di server, diakses multi-device
- **Solusi**: Upload ke `storage/app/public/profiles/` + simpan path di database
- **Tabel**: `users` → kolom `profile_photo_path`

#### 2. **Data User Profile**
- `currentUser` - Data lengkap user (nama, email, telp, dll)
- `userName` - Nama user
- `totalBooking` - Total booking user
- `totalPoints` - Total poin user
- **Alasan**: Data master yang sudah ada di database, tidak perlu duplikasi
- **Solusi**: Fetch dari database via API, gunakan session Laravel

#### 3. **Data Login/Auth**
- `isLoggedIn` - Status login
- **Alasan**: Laravel sudah punya session/auth mechanism
- **Solusi**: Gunakan `Auth::check()` dan Laravel session

#### 4. **Data Voucher**
- `allVouchers` - Semua voucher
- `claimedVouchers` - Voucher yang sudah diklaim
- `usedVouchers` - Voucher yang sudah dipakai
- **Alasan**: Data transaksional yang harus tersinkron antar device
- **Solusi**: Fetch dari database via API
- **Tabel**: `vouchers`, `user_vouchers` (pivot table)

#### 5. **Data Booking**
- `prismo_today_bookings` - Booking hari ini
- `prismoTodayBookings` - Booking hari ini (antrian)
- `prismoOtherBookings` - Booking lainnya
- **Alasan**: Data transaksional realtime
- **Solusi**: Fetch dari database via API
- **Tabel**: `bookings`

#### 6. **Data Saldo & Penarikan**
- `prismoLastWithdrawalDate` - Tanggal penarikan terakhir
- `prismoHasWithdrawnToday` - Flag sudah tarik dana hari ini
- **Alasan**: Data finansial yang harus akurat dan tidak bisa dimanipulasi
- **Solusi**: Simpan di database
- **Tabel**: `withdrawals`

#### 7. **Notifikasi**
- `notifications` - Daftar notifikasi user
- **Alasan**: Data yang harus tersinkron antar device
- **Solusi**: Fetch dari database via API
- **Tabel**: `notifications`

#### 8. **History Download (Admin)**
- `paymentProofDownloads` - History download bukti pembayaran
- **Alasan**: Audit trail yang penting
- **Solusi**: Simpan di database
- **Tabel**: `download_logs`

### ✅ BOLEH TETAP DI LOCALSTORAGE (Preferensi UI/Temporary)

#### 1. **UI Preferences**
- `prismo_whatsapp_position` - Posisi floating WhatsApp button
- **Alasan**: Preferensi UI personal, tidak kritikal

#### 2. **Notification Permission**
- `notificationPermission` - Status izin notifikasi browser
- **Alasan**: Setting browser, bukan data aplikasi

#### 3. **Data Version Control**
- `prismoAntrianVersion` - Versi data untuk cache invalidation
- **Alasan**: Mekanisme cache busting, temporary

#### 4. **Temporary State**
- `activeVoucher` - Voucher yang sedang dipilih untuk booking
- **Alasan**: State sementara dalam flow booking

---

## 🏗️ Struktur Database yang Diperlukan

### 1. Migration: Update Users Table
```sql
ALTER TABLE users ADD COLUMN profile_photo_path VARCHAR(255) NULL;
```

### 2. Migration: Create Withdrawals Table
```sql
CREATE TABLE withdrawals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    account_name VARCHAR(100) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'rejected') DEFAULT 'pending',
    withdrawal_date DATE NOT NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, withdrawal_date),
    INDEX idx_status (status)
);
```

### 3. Migration: Create User Vouchers Table
```sql
CREATE TABLE user_vouchers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    voucher_id BIGINT UNSIGNED NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    booking_id BIGINT UNSIGNED NULL,
    status ENUM('claimed', 'used', 'expired') DEFAULT 'claimed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_voucher (user_id, voucher_id),
    INDEX idx_status (status)
);
```

### 4. Migration: Create Notifications Table
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at),
    INDEX idx_created (created_at)
);
```

### 5. Migration: Create Download Logs Table
```sql
CREATE TABLE download_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    booking_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);
```

---

## 📝 Rencana Implementasi

### Phase 1: Profile Photo Migration (PRIORITY 1)
1. Buat migration untuk kolom `profile_photo_path` di tabel `users`
2. Buat API endpoint untuk upload foto profil
3. Update blade views untuk fetch foto dari database
4. Hapus semua kode localStorage untuk foto profil
5. Implementasi storage link Laravel

### Phase 2: Auth & User Data (PRIORITY 2)
1. Hapus localStorage untuk `isLoggedIn`, gunakan Laravel Auth
2. Fetch user data dari database via API/session
3. Remove localStorage untuk `currentUser`, `userName`

### Phase 3: Voucher System (PRIORITY 3)
1. Buat migration untuk `user_vouchers` table
2. Buat API untuk claim/use voucher
3. Update frontend untuk fetch dari database
4. Remove localStorage voucher data

### Phase 4: Notifications (PRIORITY 4)
1. Buat migration untuk `notifications` table
2. Buat notification service
3. Update frontend untuk fetch dari database
4. Implement real-time notification (optional: Pusher/WebSocket)

### Phase 5: Withdrawals (PRIORITY 5)
1. Buat migration untuk `withdrawals` table
2. Update logic penarikan dana
3. Remove localStorage untuk withdrawal tracking

### Phase 6: Audit Logs (PRIORITY 6)
1. Buat migration untuk `download_logs` table
2. Implement logging mechanism
3. Remove localStorage untuk download history

---

## ⚠️ Breaking Changes & Considerations

1. **User Experience**: Foto profil akan hilang saat clear cache (expected behavior)
2. **Performance**: Fetch dari database lebih lambat dari localStorage, perlu caching strategy
3. **Multi-device**: Data akan tersinkron antar device (keuntungan!)
4. **Security**: Data tidak bisa dimanipulasi dari browser console
5. **Storage**: Perlu setup `php artisan storage:link`

---

## 🚀 Quick Start Commands

```bash
# 1. Buat migrations
php artisan make:migration add_profile_photo_to_users_table
php artisan make:migration create_withdrawals_table
php artisan make:migration create_user_vouchers_table
php artisan make:migration create_notifications_table
php artisan make:migration create_download_logs_table

# 2. Run migrations
php artisan migrate

# 3. Setup storage link
php artisan storage:link

# 4. Create controllers
php artisan make:controller Api/ProfilePhotoController
php artisan make:controller Api/VoucherController
php artisan make:controller Api/NotificationController
php artisan make:controller Api/WithdrawalController
```

