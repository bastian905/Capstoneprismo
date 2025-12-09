# Frontend Integration Guide - Mengganti Mock Data dengan Real API

## 🎯 Overview

Dokumentasi ini menjelaskan cara mengganti mock data di file JavaScript dengan real API calls ke Laravel backend.

## 📁 File yang Sudah Dibuat

1. **`fe/js/api.js`** - Helper functions untuk API calls
2. **`fe/js/api-examples.js`** - Contoh implementasi untuk berbagai fitur

## 🔧 Setup Awal

### 1. Include API Helper di HTML

Tambahkan di `<head>` atau sebelum `</body>` di setiap halaman:

```html
<script src="/fe/js/api.js"></script>
```

### 2. Check Authentication di Setiap Halaman

Tambahkan di awal file JavaScript:

```javascript
// Cek apakah user sudah login
if (!isLoggedIn()) {
    window.location.href = '/fe/landingpage/lp.html';
}

// Cek role user
const user = getUserData();
if (user.role !== 'customer') { // atau 'mitra', 'admin'
    alert('Akses ditolak');
    window.location.href = '/fe/landingpage/lp.html';
}
```

## 📝 Daftar File yang Perlu Diupdate

### Priority 1: Booking System

#### 1. `fe/customer/atur-booking/booking.js`

**BEFORE (Mock Data):**
```javascript
const mockData = {
    bookingId: 'BK001',
    status: 'pending'
};

function submitBooking() {
    // Mock submission
    console.log('Booking created');
}
```

**AFTER (Real API):**
```javascript
// Include api.js di HTML dulu
async function submitBooking() {
    const bookingData = {
        mitraId: document.getElementById('mitraId').value,
        serviceType: document.getElementById('serviceType').value,
        vehicleType: document.getElementById('vehicleType').value,
        vehiclePlate: document.getElementById('vehiclePlate').value,
        bookingDate: document.getElementById('bookingDate').value,
        bookingTime: document.getElementById('bookingTime').value,
        basePrice: parseFloat(document.getElementById('basePrice').value),
        finalPrice: parseFloat(document.getElementById('finalPrice').value),
        paymentMethod: document.getElementById('paymentMethod').value,
        voucherCode: document.getElementById('voucherCode')?.value || null
    };

    try {
        const booking = await apiRequest('/bookings', {
            method: 'POST',
            body: JSON.stringify(bookingData)
        });
        
        showSuccess('Booking berhasil dibuat!');
        window.location.href = '/fe/customer/booking/Rbooking.html';
    } catch (error) {
        showError(error.message);
    }
}

// Upload bukti pembayaran
async function uploadPaymentProof(bookingId) {
    const fileInput = document.getElementById('paymentProofFile');
    const file = fileInput.files[0];
    
    if (!file) {
        showError('Pilih file terlebih dahulu');
        return;
    }
    
    const formData = new FormData();
    formData.append('payment_proof', file);
    
    try {
        await apiRequest(`/bookings/${bookingId}/payment-proof`, {
            method: 'POST',
            body: formData
        });
        
        showSuccess('Bukti pembayaran berhasil diupload!');
    } catch (error) {
        showError(error.message);
    }
}
```

#### 2. `fe/customer/booking/Rbooking.js`

**BEFORE:**
```javascript
const MOCK_DATA = {
    currentBooking: { id: 1, status: 'menunggu' },
    bookingHistory: []
};

function loadBookings() {
    // Load mock data
}
```

**AFTER:**
```javascript
async function loadCurrentBooking() {
    try {
        const bookings = await apiRequest('/bookings?status=menunggu');
        
        if (bookings.length > 0) {
            displayCurrentBooking(bookings[0]);
        } else {
            document.getElementById('currentBooking').innerHTML = '<p>Tidak ada booking aktif</p>';
        }
    } catch (error) {
        showError(error.message);
    }
}

async function loadBookingHistory() {
    try {
        const bookings = await apiRequest('/bookings');
        displayBookingHistory(bookings);
    } catch (error) {
        showError(error.message);
    }
}

function displayCurrentBooking(booking) {
    const html = `
        <div class="booking-card">
            <h3>Booking Code: ${booking.booking_code}</h3>
            <p>Status: ${booking.status}</p>
            <p>Tanggal: ${formatDate(booking.booking_date)}</p>
            <p>Total: ${formatRupiah(booking.final_price)}</p>
            ${booking.status === 'menunggu' ? `
                <button onclick="cancelBooking(${booking.id})">Batalkan</button>
            ` : ''}
        </div>
    `;
    document.getElementById('currentBooking').innerHTML = html;
}

async function cancelBooking(bookingId) {
    const reason = prompt('Alasan pembatalan:');
    if (!reason) return;
    
    try {
        await apiRequest(`/bookings/${bookingId}`, {
            method: 'PUT',
            body: JSON.stringify({
                status: 'dibatalkan',
                cancellation_reason: reason
            })
        });
        
        showSuccess('Booking berhasil dibatalkan');
        loadCurrentBooking();
        loadBookingHistory();
    } catch (error) {
        showError(error.message);
    }
}

// Load saat halaman dimuat
window.addEventListener('DOMContentLoaded', () => {
    loadCurrentBooking();
    loadBookingHistory();
});
```

#### 3. `fe/mitra/antrian/antrian.js`

**BEFORE:**
```javascript
const mockBookings = [
    { id: 1, customerName: 'John', status: 'menunggu' }
];
```

**AFTER:**
```javascript
async function loadQueue() {
    try {
        showLoading('Memuat antrian...');
        const bookings = await apiRequest('/bookings?status=menunggu');
        displayQueue(bookings);
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

function displayQueue(bookings) {
    const container = document.getElementById('queueContainer');
    
    if (bookings.length === 0) {
        container.innerHTML = '<p>Tidak ada antrian</p>';
        return;
    }
    
    const html = bookings.map(booking => `
        <div class="queue-item" data-id="${booking.id}">
            <div class="customer-info">
                <h4>${booking.customer.name}</h4>
                <p>${booking.vehicle_type} - ${booking.vehicle_plate}</p>
                <p>${booking.service_type}</p>
            </div>
            <div class="booking-info">
                <p>Tanggal: ${formatDate(booking.booking_date)}</p>
                <p>Waktu: ${booking.booking_time}</p>
                <p>Harga: ${formatRupiah(booking.final_price)}</p>
            </div>
            <div class="actions">
                <button onclick="startBooking(${booking.id})" class="btn-primary">
                    Mulai Proses
                </button>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

async function startBooking(bookingId) {
    try {
        await apiRequest(`/bookings/${bookingId}`, {
            method: 'PUT',
            body: JSON.stringify({ status: 'proses' })
        });
        
        showSuccess('Booking dimulai!');
        loadQueue();
    } catch (error) {
        showError(error.message);
    }
}

async function completeBooking(bookingId) {
    try {
        await apiRequest(`/bookings/${bookingId}`, {
            method: 'PUT',
            body: JSON.stringify({ status: 'selesai' })
        });
        
        showSuccess('Booking selesai!');
        loadQueue();
    } catch (error) {
        showError(error.message);
    }
}

// Auto refresh setiap 30 detik
setInterval(loadQueue, 30000);

window.addEventListener('DOMContentLoaded', loadQueue);
```

### Priority 2: Mitra Management

#### 4. `fe/customer/detail-mitra/minipro.js`

**BEFORE:**
```javascript
const mockData = {
    business: { name: 'Bengkel A', rating: 4.5 },
    galleryImages: []
};
```

**AFTER:**
```javascript
async function loadMitraDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const mitraId = urlParams.get('id');
    
    if (!mitraId) {
        showError('ID mitra tidak ditemukan');
        return;
    }
    
    try {
        showLoading('Memuat detail mitra...');
        const mitra = await apiRequest(`/mitra/${mitraId}`);
        displayMitraDetail(mitra);
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

function displayMitraDetail(mitra) {
    const profile = mitra.mitra_profile;
    
    // Business Info
    document.getElementById('businessName').textContent = profile.business_name;
    document.getElementById('rating').textContent = profile.rating;
    document.getElementById('reviewCount').textContent = `(${profile.review_count} reviews)`;
    document.getElementById('address').textContent = profile.address;
    document.getElementById('phone').textContent = profile.phone;
    document.getElementById('description').textContent = profile.description || '-';
    
    // Gallery
    const galleryContainer = document.getElementById('gallery');
    if (profile.facility_photos && profile.facility_photos.length > 0) {
        const html = profile.facility_photos.map(photo => `
            <img src="http://localhost:8000/storage/${photo}" alt="Facility" class="gallery-img">
        `).join('');
        galleryContainer.innerHTML = html;
    }
    
    // Recent Reviews
    if (mitra.mitra_reviews && mitra.mitra_reviews.length > 0) {
        displayReviews(mitra.mitra_reviews);
    }
}

function displayReviews(reviews) {
    const container = document.getElementById('reviewsContainer');
    const html = reviews.map(review => `
        <div class="review-card">
            <div class="review-header">
                <strong>${review.customer.name}</strong>
                <span class="rating">${'⭐'.repeat(review.rating)}</span>
            </div>
            <p>${review.comment}</p>
            <small>${formatDate(review.created_at)}</small>
            ${review.mitra_reply ? `
                <div class="mitra-reply">
                    <strong>Balasan Mitra:</strong>
                    <p>${review.mitra_reply}</p>
                </div>
            ` : ''}
        </div>
    `).join('');
    container.innerHTML = html;
}

window.addEventListener('DOMContentLoaded', loadMitraDetail);
```

#### 5. `fe/mitra/profil/edit-profile.js`

**AFTER:**
```javascript
async function loadProfile() {
    try {
        const user = await apiRequest('/user');
        const profile = user.mitra_profile || {};
        
        // Fill form
        document.getElementById('businessName').value = profile.business_name || '';
        document.getElementById('contactPerson').value = profile.contact_person || '';
        document.getElementById('phone').value = profile.phone || '';
        document.getElementById('address').value = profile.address || '';
        document.getElementById('city').value = profile.city || '';
        document.getElementById('description').value = profile.description || '';
        
        // Display current documents
        if (profile.ktp_photo) {
            displayDocument('ktp', profile.ktp_photo);
        }
        if (profile.qris_photo) {
            displayDocument('qris', profile.qris_photo);
        }
    } catch (error) {
        showError(error.message);
    }
}

async function saveProfile() {
    const profileData = {
        business_name: document.getElementById('businessName').value,
        contact_person: document.getElementById('contactPerson').value,
        phone: document.getElementById('phone').value,
        address: document.getElementById('address').value,
        city: document.getElementById('city').value,
        description: document.getElementById('description').value
    };
    
    try {
        showLoading('Menyimpan profil...');
        await apiRequest('/mitra/profile', {
            method: 'PUT',
            body: JSON.stringify(profileData)
        });
        
        hideLoading();
        showSuccess('Profil berhasil diperbarui!');
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

async function uploadDocuments() {
    const ktpFile = document.getElementById('ktpFile').files[0];
    const qrisFile = document.getElementById('qrisFile').files[0];
    const legalFile = document.getElementById('legalFile').files[0];
    
    if (!ktpFile && !qrisFile && !legalFile) {
        showError('Pilih minimal 1 file untuk diupload');
        return;
    }
    
    const formData = new FormData();
    if (ktpFile) formData.append('ktp_photo', ktpFile);
    if (qrisFile) formData.append('qris_photo', qrisFile);
    if (legalFile) formData.append('legal_doc', legalFile);
    
    try {
        showLoading('Mengupload dokumen...');
        await apiRequest('/mitra/documents', {
            method: 'POST',
            body: formData
        });
        
        hideLoading();
        showSuccess('Dokumen berhasil diupload!');
        loadProfile(); // Reload to show new documents
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

window.addEventListener('DOMContentLoaded', loadProfile);
```

### Priority 3: Vouchers & Reviews

#### 6. `fe/customer/voucher/voucher.js`

**AFTER:**
```javascript
async function loadAvailableVouchers() {
    try {
        showLoading('Memuat voucher...');
        const vouchers = await apiRequest('/vouchers/available');
        displayVouchers(vouchers, 'available');
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

async function loadMyVouchers() {
    try {
        showLoading('Memuat voucher saya...');
        const vouchers = await apiRequest('/vouchers/my-vouchers');
        displayVouchers(vouchers, 'my');
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

function displayVouchers(vouchers, type) {
    const container = document.getElementById(type === 'available' ? 'availableVouchers' : 'myVouchers');
    
    if (vouchers.length === 0) {
        container.innerHTML = '<p>Tidak ada voucher</p>';
        return;
    }
    
    const html = vouchers.map(voucher => {
        const isPivot = voucher.pivot !== undefined;
        const isUsed = isPivot && voucher.pivot.used_at;
        
        return `
            <div class="voucher-card ${isUsed ? 'used' : ''}">
                <h3>${voucher.title}</h3>
                <p class="voucher-code">${voucher.code}</p>
                <p>${voucher.description}</p>
                <p class="discount">
                    ${voucher.discount_percent ? voucher.discount_percent + '%' : ''}
                    ${voucher.discount_fixed ? formatRupiah(voucher.discount_fixed) : ''}
                </p>
                <p class="expiry">Berlaku sampai: ${formatDate(voucher.end_date)}</p>
                
                ${type === 'available' ? `
                    <button onclick="claimVoucher(${voucher.id})" class="btn-claim">
                        Klaim Voucher
                    </button>
                ` : ''}
                
                ${isPivot && !isUsed ? `
                    <button onclick="useVoucher('${voucher.code}')" class="btn-use">
                        Gunakan
                    </button>
                ` : ''}
                
                ${isUsed ? '<span class="used-badge">Sudah Digunakan</span>' : ''}
            </div>
        `;
    }).join('');
    
    container.innerHTML = html;
}

async function claimVoucher(voucherId) {
    try {
        await apiRequest(`/vouchers/${voucherId}/claim`, {
            method: 'POST'
        });
        
        showSuccess('Voucher berhasil diklaim!');
        loadAvailableVouchers();
        loadMyVouchers();
    } catch (error) {
        showError(error.message);
    }
}

function useVoucher(code) {
    // Copy ke clipboard atau redirect ke booking page
    navigator.clipboard.writeText(code);
    showSuccess('Kode voucher disalin! Gunakan saat booking.');
}

window.addEventListener('DOMContentLoaded', () => {
    loadAvailableVouchers();
    loadMyVouchers();
});
```

#### 7. `fe/mitra/review/review.js`

**AFTER:**
```javascript
async function loadReviews() {
    try {
        showLoading('Memuat review...');
        const user = getUserData();
        const reviews = await apiRequest(`/reviews?mitra_id=${user.id}`);
        displayReviews(reviews);
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

function displayReviews(reviews) {
    const container = document.getElementById('reviewsContainer');
    
    if (reviews.length === 0) {
        container.innerHTML = '<p>Belum ada review</p>';
        return;
    }
    
    const html = reviews.map(review => `
        <div class="review-card">
            <div class="review-header">
                <div>
                    <strong>${review.customer.name}</strong>
                    <span class="rating">${'⭐'.repeat(review.rating)}</span>
                </div>
                <small>${formatDateTime(review.created_at)}</small>
            </div>
            <p class="review-comment">${review.comment}</p>
            
            ${review.review_photos && review.review_photos.length > 0 ? `
                <div class="review-photos">
                    ${review.review_photos.map(photo => `
                        <img src="http://localhost:8000/storage/${photo}" alt="Review" class="review-photo">
                    `).join('')}
                </div>
            ` : ''}
            
            ${review.mitra_reply ? `
                <div class="mitra-reply">
                    <strong>Balasan Anda:</strong>
                    <p>${review.mitra_reply}</p>
                    <small>${formatDateTime(review.replied_at)}</small>
                </div>
            ` : `
                <button onclick="showReplyForm(${review.id})" class="btn-reply">
                    Balas Review
                </button>
                <div id="replyForm${review.id}" style="display: none;">
                    <textarea id="reply${review.id}" placeholder="Tulis balasan..."></textarea>
                    <button onclick="submitReply(${review.id})">Kirim</button>
                </div>
            `}
        </div>
    `).join('');
    
    container.innerHTML = html;
}

function showReplyForm(reviewId) {
    document.getElementById(`replyForm${reviewId}`).style.display = 'block';
}

async function submitReply(reviewId) {
    const reply = document.getElementById(`reply${reviewId}`).value;
    
    if (!reply.trim()) {
        showError('Tulis balasan terlebih dahulu');
        return;
    }
    
    try {
        await apiRequest(`/reviews/${reviewId}/reply`, {
            method: 'POST',
            body: JSON.stringify({ mitra_reply: reply })
        });
        
        showSuccess('Balasan berhasil dikirim!');
        loadReviews();
    } catch (error) {
        showError(error.message);
    }
}

window.addEventListener('DOMContentLoaded', loadReviews);
```

### Priority 4: Admin & Withdrawals

#### 8. `fe/admin/kelolabooking/kelolabooking.js`

**AFTER:**
```javascript
async function loadAllBookings(status = null) {
    try {
        showLoading('Memuat data booking...');
        
        let endpoint = '/bookings';
        if (status) {
            endpoint += `?status=${status}`;
        }
        
        const bookings = await apiRequest(endpoint);
        displayBookings(bookings);
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

// Filter by status
document.getElementById('statusFilter')?.addEventListener('change', (e) => {
    loadAllBookings(e.target.value || null);
});

window.addEventListener('DOMContentLoaded', () => loadAllBookings());
```

#### 9. `fe/admin/dashboard/penarikan.js`

**AFTER:**
```javascript
async function loadWithdrawals(status = null) {
    try {
        showLoading('Memuat data penarikan...');
        
        let endpoint = '/withdrawals';
        if (status) {
            endpoint += `?status=${status}`;
        }
        
        const withdrawals = await apiRequest(endpoint);
        displayWithdrawals(withdrawals);
        hideLoading();
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

function displayWithdrawals(withdrawals) {
    const container = document.getElementById('withdrawalsContainer');
    
    const html = withdrawals.map(withdrawal => `
        <div class="withdrawal-card status-${withdrawal.status}">
            <div class="withdrawal-header">
                <h4>${withdrawal.mitra.name}</h4>
                <span class="status">${withdrawal.status}</span>
            </div>
            <p>Jumlah: ${formatRupiah(withdrawal.amount)}</p>
            <p>Bank: ${withdrawal.bank_name} - ${withdrawal.account_number}</p>
            <p>Atas Nama: ${withdrawal.account_name}</p>
            <small>Diajukan: ${formatDateTime(withdrawal.created_at)}</small>
            
            ${withdrawal.status === 'pending' ? `
                <div class="actions">
                    <button onclick="approveWithdrawal(${withdrawal.id})" class="btn-approve">
                        Setujui
                    </button>
                    <button onclick="rejectWithdrawal(${withdrawal.id})" class="btn-reject">
                        Tolak
                    </button>
                </div>
            ` : ''}
            
            ${withdrawal.status === 'approved' ? `
                <button onclick="completeWithdrawal(${withdrawal.id})" class="btn-complete">
                    Tandai Selesai
                </button>
            ` : ''}
        </div>
    `).join('');
    
    container.innerHTML = html;
}

async function approveWithdrawal(withdrawalId) {
    const note = prompt('Catatan admin (opsional):');
    
    try {
        await apiRequest(`/withdrawals/${withdrawalId}/approve`, {
            method: 'PUT',
            body: JSON.stringify({ admin_note: note || '' })
        });
        
        showSuccess('Penarikan disetujui!');
        loadWithdrawals();
    } catch (error) {
        showError(error.message);
    }
}

async function rejectWithdrawal(withdrawalId) {
    const note = prompt('Alasan penolakan (wajib):');
    if (!note) {
        showError('Alasan penolakan harus diisi');
        return;
    }
    
    try {
        await apiRequest(`/withdrawals/${withdrawalId}/reject`, {
            method: 'PUT',
            body: JSON.stringify({ admin_note: note })
        });
        
        showSuccess('Penarikan ditolak');
        loadWithdrawals();
    } catch (error) {
        showError(error.message);
    }
}

async function completeWithdrawal(withdrawalId) {
    if (!confirm('Tandai penarikan sebagai selesai?')) return;
    
    try {
        await apiRequest(`/withdrawals/${withdrawalId}/complete`, {
            method: 'PUT'
        });
        
        showSuccess('Penarikan selesai!');
        loadWithdrawals();
    } catch (error) {
        showError(error.message);
    }
}

window.addEventListener('DOMContentLoaded', () => loadWithdrawals());
```

## 🔒 Authentication Flow

### Login Page Implementation

```javascript
// login.js
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    try {
        showLoading('Logging in...');
        
        const response = await login(email, password); // dari api.js
        
        hideLoading();
        
        // Redirect based on role
        switch (response.user.role) {
            case 'admin':
                window.location.href = '/fe/admin/dashboard/dashboard.html';
                break;
            case 'mitra':
                window.location.href = '/fe/mitra/dashboard/dashboard.html';
                break;
            case 'customer':
                window.location.href = '/fe/customer/dashboard/dashU.html';
                break;
        }
    } catch (error) {
        hideLoading();
        showError(error.message);
    }
}

document.getElementById('loginForm').addEventListener('submit', handleLogin);
```

### Logout Implementation

```javascript
// Di setiap halaman, tambahkan tombol logout
document.getElementById('logoutBtn')?.addEventListener('click', async () => {
    if (confirm('Yakin ingin logout?')) {
        await logout(); // dari api.js - otomatis redirect ke landing page
    }
});
```

## 🧪 Testing

### Test API dengan Console Browser

```javascript
// Test login
login('admin@example.com', 'password')
    .then(data => console.log('Login success:', data));

// Test get bookings
apiRequest('/bookings')
    .then(data => console.log('Bookings:', data));

// Test create booking
apiRequest('/bookings', {
    method: 'POST',
    body: JSON.stringify({
        mitra_id: 1,
        service_type: 'Cuci Mobil',
        vehicle_type: 'Sedan',
        vehicle_plate: 'B1234XYZ',
        booking_date: '2025-12-10',
        booking_time: '10:00',
        base_price: 50000,
        final_price: 50000,
        payment_method: 'Transfer'
    })
}).then(data => console.log('Booking created:', data));
```

## ✅ Checklist Migrasi

- [ ] Update `booking.js` - Customer create booking
- [ ] Update `Rbooking.js` - Customer view bookings
- [ ] Update `antrian.js` - Mitra queue management
- [ ] Update `minipro.js` - Mitra detail page
- [ ] Update `edit-profile.js` - Mitra profile edit
- [ ] Update `voucher.js` - Customer voucher page
- [ ] Update `review.js` - Mitra review management
- [ ] Update `kelolabooking.js` - Admin booking management
- [ ] Update `penarikan.js` - Admin withdrawal management
- [ ] Update `kelolavoucher.js` - Admin voucher CRUD
- [ ] Update `kelolamitra.js` - Admin mitra management
- [ ] Update `kelolacustomer.js` - Admin customer management
- [ ] Update `saldo.js` - Mitra balance & withdrawal
- [ ] Implement login page
- [ ] Implement logout functionality
- [ ] Test all CRUD operations
- [ ] Test file uploads
- [ ] Test authentication & authorization

## 📞 Support

Jika ada error atau pertanyaan, cek:
1. Browser console untuk error JavaScript
2. Network tab untuk melihat request/response API
3. Laravel logs di `storage/logs/laravel.log`
4. Pastikan Laravel server running: `php artisan serve`
