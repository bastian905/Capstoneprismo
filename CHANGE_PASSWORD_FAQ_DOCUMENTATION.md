# Fitur Ubah Password & Bantuan FAQ

## 1. Ubah Password

### Deskripsi
Fitur yang memungkinkan mitra untuk mengubah password akun mereka dengan aman. Untuk user yang login menggunakan Google OAuth, fitur ini akan dinonaktifkan.

### File yang Dibuat
- **View (Blade)**: `resources/views/mitra/profil/change-password.blade.php`
- **View (Static)**: `fe/mitra/profil/change-password.html`
- **Controller**: `app/Http/Controllers/ChangePasswordController.php`
- **CSS**: `public/css/change-password.css` & `fe/mitra/profil/change-password.css`
- **JavaScript**: `public/js/change-password.js` & `fe/mitra/profil/change-password.js`
- **Migration**: `database/migrations/2025_12_06_120207_add_google_id_to_users_table.php`

### Route
```php
// GET - Menampilkan form ubah password
Route::get('/profile/change-password', [ChangePasswordController::class, 'showChangePasswordForm'])
    ->name('profile.change-password');

// POST - Proses ubah password
Route::post('/profile/change-password', [ChangePasswordController::class, 'changePassword'])
    ->name('profile.change-password.submit');
```

### Fitur Utama
1. **Validasi Password Kuat**
   - Minimal 8 karakter
   - Minimal 1 huruf besar (A-Z)
   - Minimal 1 huruf kecil (a-z)
   - Minimal 1 angka (0-9)

2. **Password Strength Indicator**
   - Visual indicator dengan 4 bars
   - Real-time checking (Lemah, Sedang, Kuat)
   - Color-coded feedback

3. **Proteksi OAuth User**
   - Deteksi user login via Google
   - Menampilkan notice untuk OAuth user
   - Form disabled untuk OAuth user

4. **Toggle Password Visibility**
   - Eye icon untuk show/hide password
   - Tersedia di semua input password

5. **Real-time Validation**
   - Validasi password requirements secara real-time
   - Visual checkmark untuk requirement yang terpenuhi
   - Validasi kecocokan confirm password

6. **Loading State**
   - Button loading animation saat submit
   - Prevent double submission

### API Endpoint
**POST** `/profile/change-password`

Request Body:
```json
{
    "current_password": "OldPassword123",
    "new_password": "NewPassword123",
    "confirm_password": "NewPassword123"
}
```

Success Response (200):
```json
{
    "success": true,
    "message": "Password berhasil diubah."
}
```

Error Response (422):
```json
{
    "success": false,
    "errors": {
        "current_password": ["Password saat ini tidak sesuai."],
        "new_password": ["Password minimal 8 karakter."]
    }
}
```

OAuth User Response (403):
```json
{
    "success": false,
    "message": "Password tidak dapat diubah untuk akun OAuth."
}
```

---

## 2. Bantuan & FAQ

### Deskripsi
Halaman FAQ interaktif yang menyediakan jawaban untuk pertanyaan umum mitra tentang penggunaan platform PRISMO.

### File yang Dibuat
- **View (Blade)**: `resources/views/mitra/profil/help-faq.blade.php`
- **View (Static)**: `fe/mitra/profil/help-faq.html`
- **CSS**: `public/css/help-faq.css` & `fe/mitra/profil/help-faq.css`
- **JavaScript**: `public/js/help-faq.js` & `fe/mitra/profil/help-faq.js`

### Route
```php
Route::get('/help/faq', function () {
    return view('mitra.profil.help-faq');
})->name('help.faq');
```

### Fitur Utama
1. **Search Functionality**
   - Real-time search dalam FAQ
   - Highlight hasil pencarian
   - Clear search button
   - Keyboard shortcut: Ctrl/Cmd + K

2. **Category Filter**
   - 5 kategori: Semua, Akun, Booking, Pembayaran, Lainnya
   - Icon untuk setiap kategori
   - Active state indication

3. **Accordion FAQ**
   - Expandable/collapsible FAQ items
   - Smooth animation
   - Arrow icon rotation

4. **No Results State**
   - Pesan ketika tidak ada hasil
   - Suggestion untuk kata kunci lain

5. **Contact Support Section**
   - Call-to-action untuk hubungi support
   - Email: support@prismo.id
   - WhatsApp: +62 812-3456-7890

### Kategori FAQ

#### 1. Akun (3 items)
- Cara mendaftar sebagai mitra
- Cara mengubah password
- Cara memperbarui informasi profil

#### 2. Booking (3 items)
- Cara menerima pesanan booking
- Apa yang dilakukan jika customer tidak datang
- Cara membatalkan pesanan yang sudah diterima

#### 3. Pembayaran (3 items)
- Kapan bisa menarik saldo
- Cara menambahkan rekening bank
- Biaya admin atau komisi PRISMO

#### 4. Lainnya (3 items)
- Cara meningkatkan rating mitra
- Cara merespon review customer
- Cara menghubungi customer service

### JavaScript API
```javascript
// Open specific FAQ
window.FAQManager.openFAQ('faq-id');

// Search FAQ
window.FAQManager.searchFAQ('booking');

// Filter by category
window.FAQManager.filterByCategory('account');
```

### Keyboard Shortcuts
- `Ctrl/Cmd + K`: Focus search input
- `Escape`: Clear search atau blur input

---

## Migration

Jalankan migration untuk menambahkan kolom `google_id`:
```bash
php artisan migrate
```

Kolom yang ditambahkan:
- `google_id` (string, nullable) - untuk identifikasi user Google OAuth

---

## Update di Profil

Link menu di `profil.blade.php` telah diupdate:
```html
<a href="{{ url('/profile/change-password') }}" class="settings-item">
    <img src="{{ asset('images/password.png') }}" alt="Ubah Password">
    <span class="settings-label">Ubah password</span>
</a>
<a href="{{ url('/help/faq') }}" class="settings-item">
    <img src="{{ asset('images/tanya.png') }}" alt="Bantuan & FAQ">
    <span class="settings-label">Bantuan & FAQ</span>
</a>
```

---

## Testing

### Test Change Password
1. Login sebagai mitra
2. Klik "Ubah password" di menu profil
3. Isi form dengan:
   - Password saat ini
   - Password baru (minimal 8 karakter, huruf besar, kecil, angka)
   - Konfirmasi password baru
4. Klik "Simpan Password"
5. Verifikasi success modal muncul
6. Coba login dengan password baru

### Test OAuth Detection
1. Login menggunakan Google OAuth
2. Klik "Ubah password" di menu profil
3. Verifikasi notice muncul: "Login dengan Google"
4. Verifikasi form tidak ditampilkan

### Test FAQ
1. Klik "Bantuan & FAQ" di menu profil
2. Test search: ketik "booking"
3. Test filter kategori: klik "Booking"
4. Test accordion: klik pertanyaan untuk expand/collapse
5. Test "Hubungi Support" button

---

## Customization

### Menambah FAQ Baru
Edit file `help-faq.blade.php`, tambahkan item baru:
```html
<div class="faq-item" data-category="account">
    <button type="button" class="faq-question">
        <span>Pertanyaan baru?</span>
        <svg class="faq-icon">...</svg>
    </button>
    <div class="faq-answer">
        <p>Jawaban untuk pertanyaan baru.</p>
    </div>
</div>
```

### Mengubah Kontak Support
Edit fungsi `contactSupport()` di `help-faq.js`:
```javascript
function contactSupport() {
    // Option 1: Email
    window.location.href = 'mailto:support@prismo.id';
    
    // Option 2: WhatsApp
    window.open('https://wa.me/6281234567890', '_blank');
    
    // Option 3: Contact page
    window.location.href = '/contact';
}
```

---

## Security Notes

1. **Password Validation**: Menggunakan Laravel's `Password::min(8)->mixedCase()->numbers()`
2. **CSRF Protection**: Token CSRF otomatis di form
3. **Current Password Check**: Verifikasi password lama dengan `Hash::check()`
4. **OAuth Protection**: Mencegah ubah password untuk OAuth user
5. **Password Hashing**: Password di-hash dengan `Hash::make()` (bcrypt)

---

## Responsive Design

Kedua halaman fully responsive:
- Mobile-first approach
- Breakpoint: 768px dan 640px
- Touch-friendly buttons dan inputs
- Scrollable category tabs di mobile
- Optimized padding dan spacing

---

## Browser Compatibility

- Chrome/Edge (Latest)
- Firefox (Latest)
- Safari (Latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

CSS menggunakan modern features dengan fallback untuk browser lama.
