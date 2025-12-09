# 📸 Profile Photo Migration Guide - localStorage ke Laravel Storage

## ✅ Backend Setup (COMPLETED)

### 1. Database Migration
- ✅ Kolom `profile_photo_path` ditambahkan ke tabel `users`
- ✅ Migration berhasil dijalankan

### 2. Storage Setup
- ✅ Symbolic link sudah dibuat: `public/storage` → `storage/app/public`
- ✅ Foto akan disimpan di: `storage/app/public/profiles/`
- ✅ Akses via URL: `http://domain.com/storage/profiles/filename.jpg`

### 3. API Endpoints (COMPLETED)
```
POST   /profile/photo/upload  - Upload foto profil baru
GET    /profile/photo         - Get URL foto profil saat ini
DELETE /profile/photo          - Hapus foto profil
```

---

## 🔨 Frontend Implementation

### A. Customer Profile (uprofil.js & eprofil.js)

#### BEFORE (localStorage):
```javascript
// OLD CODE - HAPUS INI
function changeProfilePhoto() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    
    input.onchange = function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result;
                localStorage.setItem('userProfilePhoto', e.target.result); // ❌ HAPUS
            };
            reader.readAsDataURL(file);
        }
    };
    
    input.click();
}
```

#### AFTER (Laravel Storage):
```javascript
// NEW CODE - GANTI DENGAN INI
async function changeProfilePhoto() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    
    input.onchange = async function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validasi ukuran file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB!');
                return;
            }
            
            // Show loading
            showLoadingOverlay('Mengupload foto...');
            
            try {
                // Upload ke server
                const formData = new FormData();
                formData.append('photo', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                const response = await fetch('/profile/photo/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update gambar di halaman
                    document.getElementById('profileImage').src = result.photo_url;
                    
                    // Update semua avatar di navbar jika ada
                    document.querySelectorAll('.avatar__image').forEach(img => {
                        img.src = result.photo_url;
                    });
                    
                    alert('Foto profil berhasil diupdate!');
                } else {
                    alert('Gagal upload foto: ' + result.message);
                }
            } catch (error) {
                console.error('Error uploading photo:', error);
                alert('Terjadi kesalahan saat upload foto');
            } finally {
                hideLoadingOverlay();
            }
        }
    };
    
    input.click();
}

// Load foto profil saat halaman dimuat
async function loadProfilePhoto() {
    try {
        const response = await fetch('/profile/photo');
        const result = await response.json();
        
        if (result.success && result.photo_url) {
            // Update gambar profil
            const profileImg = document.getElementById('profileImage');
            if (profileImg) {
                profileImg.src = result.photo_url;
            }
            
            // Update avatar di navbar
            document.querySelectorAll('.avatar__image').forEach(img => {
                img.src = result.photo_url;
            });
        }
    } catch (error) {
        console.error('Error loading profile photo:', error);
    }
}

// Panggil saat page load
document.addEventListener('DOMContentLoaded', () => {
    loadProfilePhoto();
});
```

---

### B. Mitra Profile (profil.blade.php)

#### BEFORE (localStorage):
```javascript
// OLD CODE - HAPUS INI
function confirmAvatarChange() {
    if (!pendingAvatarFile) return;
    
    document.getElementById('avatarConfirmModal').classList.remove('active');
    
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profileImage').src = e.target.result;
        localStorage.setItem('mitraProfilePhoto', e.target.result); // ❌ HAPUS
        pendingAvatarFile = null;
    };
    reader.readAsDataURL(pendingAvatarFile);
}
```

#### AFTER (Laravel Storage):
```javascript
// NEW CODE - GANTI DENGAN INI
async function confirmAvatarChange() {
    if (!pendingAvatarFile) return;
    
    document.getElementById('avatarConfirmModal').classList.remove('active');
    
    // Show loading
    showLoadingIndicator();
    
    try {
        // Upload ke server
        const formData = new FormData();
        formData.append('photo', pendingAvatarFile);
        formData.append('_token', '{{ csrf_token() }}');
        
        const response = await fetch('/profile/photo/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update gambar profil
            document.getElementById('profileImage').src = result.photo_url;
            
            console.log('✅ Profile photo updated and saved to server');
        } else {
            alert('Gagal upload foto: ' + result.message);
        }
    } catch (error) {
        console.error('Error uploading photo:', error);
        alert('Terjadi kesalahan saat upload foto');
    } finally {
        hideLoadingIndicator();
        pendingAvatarFile = null;
    }
}
```

---

### C. Load Foto di Semua Halaman

#### BEFORE (localStorage):
```javascript
// OLD CODE - HAPUS INI
function loadMitraProfilePhoto() {
    const savedPhoto = localStorage.getItem('mitraProfilePhoto'); // ❌ HAPUS
    if (savedPhoto) {
        const avatarImg = document.querySelector('.user-menu__toggle .avatar__image');
        if (avatarImg) {
            avatarImg.src = savedPhoto;
        }
    }
}
```

#### AFTER (Laravel Storage):
```javascript
// NEW CODE - GANTI DENGAN INI
async function loadProfilePhoto() {
    try {
        const response = await fetch('/profile/photo');
        const result = await response.json();
        
        if (result.success && result.photo_url) {
            // Update semua avatar di halaman
            document.querySelectorAll('.avatar__image').forEach(img => {
                img.src = result.photo_url;
            });
            
            // Update profile image jika ada
            const profileImg = document.getElementById('profileImage');
            if (profileImg) {
                profileImg.src = result.photo_url;
            }
        }
    } catch (error) {
        console.error('Error loading profile photo:', error);
        // Gunakan foto default jika error
    }
}

// Panggil di semua halaman
document.addEventListener('DOMContentLoaded', () => {
    loadProfilePhoto();
});
```

---

### D. Blade Template - Add CSRF Token

Tambahkan di semua file `.blade.php`:

```php
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>...</title>
</head>
```

---

### E. Helper Functions (Loading Indicator)

```javascript
// Helper untuk menampilkan loading
function showLoadingOverlay(message = 'Loading...') {
    // Cek apakah sudah ada loading overlay
    let overlay = document.getElementById('loadingOverlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>${message}</p>
            </div>
        `;
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        document.body.appendChild(overlay);
    }
    
    overlay.style.display = 'flex';
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}
```

---

## 🗑️ Cleanup - Hapus Kode localStorage

### Files yang perlu diupdate:

1. **Customer:**
   - `prismo/public/js/uprofil.js`
   - `prismo/public/js/eprofil.js`
   - `prismo/public/js/dashU.js`
   - `prismo/public/js/voucher.js`
   - `prismo/public/js/Rbooking.js`

2. **Mitra:**
   - `prismo/resources/views/mitra/profil/profil.blade.php`
   - `prismo/public/js/dashboard.js`
   - `prismo/public/js/antrian.js`
   - `prismo/public/js/review.js`
   - `prismo/public/js/saldo.js`

### Kode yang harus dihapus:
```javascript
// ❌ HAPUS SEMUA BARIS INI:
localStorage.getItem('userProfilePhoto')
localStorage.setItem('userProfilePhoto', ...)
localStorage.getItem('mitraProfilePhoto')
localStorage.setItem('mitraProfilePhoto', ...)
localStorage.removeItem('userProfilePhoto')
localStorage.removeItem('mitraProfilePhoto')
```

---

## 🧪 Testing Checklist

- [ ] Upload foto profil customer
- [ ] Upload foto profil mitra
- [ ] Foto tampil di semua halaman setelah upload
- [ ] Foto persist setelah logout/login
- [ ] Foto sync antar device (login di browser berbeda)
- [ ] Error handling: file terlalu besar (>5MB)
- [ ] Error handling: format file tidak valid
- [ ] Default foto tampil jika user belum upload
- [ ] Delete foto profil (kembali ke default)

---

## 📊 Benefits Migrasi

### BEFORE (localStorage):
- ❌ Foto hilang saat clear cache
- ❌ Tidak sync antar device
- ❌ Bisa dimanipulasi dari console
- ❌ Membebani browser (base64 besar)
- ❌ Tidak ada backup

### AFTER (Laravel Storage):
- ✅ Foto persisten di server
- ✅ Sync otomatis antar device
- ✅ Secure, tidak bisa dimanipulasi
- ✅ Tidak membebani browser
- ✅ Ada backup di storage
- ✅ Bisa implementasi image optimization
- ✅ Audit trail (siapa upload kapan)

---

## 🚀 Next Steps

1. **Implement frontend changes** (lihat contoh di atas)
2. **Test upload functionality**
3. **Update User model** (optional - add accessor for photo URL)
4. **Implement image optimization** (optional - resize/compress)
5. **Add validation rules** (file type, size)
6. **Clean up old localStorage code**

---

## 💡 Optional Enhancements

### 1. Image Optimization
```php
// Install Intervention Image
composer require intervention/image

// Di ProfilePhotoController:
use Intervention\Image\Facades\Image;

// Resize & optimize
$image = Image::make($photo);
$image->fit(400, 400); // Crop to square
$image->save(storage_path('app/public/profiles/' . $filename), 80); // 80% quality
```

### 2. Add User Model Accessor
```php
// app/Models/User.php
public function getProfilePhotoUrlAttribute()
{
    if ($this->profile_photo_path) {
        return asset('storage/' . $this->profile_photo_path);
    }
    return asset('images/profile.png'); // default
}

// Usage in blade:
<img src="{{ auth()->user()->profile_photo_url }}" alt="Profile">
```

### 3. Add Audit Trail
```php
// Track who uploaded when
$user->update([
    'profile_photo_path' => $path,
    'profile_photo_updated_at' => now()
]);
```

