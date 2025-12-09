# 🚀 Quick Start - API Integration

## Server Status
✅ **Backend API is LIVE and READY**
- Server: http://localhost:8000
- API Base: http://localhost:8000/api
- Total Endpoints: 48

## 📝 Quick Test Commands

Open browser console dan test API:

### 1. Test Login (akan error karena user belum ada, normal)
```javascript
fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'test@example.com',
        password: 'password123'
    })
})
.then(r => r.json())
.then(console.log)
```

### 2. Create Test User via Laravel Tinker
```bash
php artisan tinker

# Create admin user
$admin = new App\Models\User();
$admin->name = 'Admin Test';
$admin->email = 'admin@test.com';
$admin->password = bcrypt('password123');
$admin->role = 'admin';
$admin->email_verified_at = now();
$admin->approval_status = 'approved';
$admin->profile_completed = true;
$admin->save();

# Create customer user  
$customer = new App\Models\User();
$customer->name = 'Customer Test';
$customer->email = 'customer@test.com';
$customer->password = bcrypt('password123');
$customer->role = 'customer';
$customer->email_verified_at = now();
$customer->approval_status = 'approved';
$customer->profile_completed = true;
$customer->save();

# Create mitra user
$mitra = new App\Models\User();
$mitra->name = 'Mitra Test';
$mitra->email = 'mitra@test.com';
$mitra->password = bcrypt('password123');
$mitra->role = 'mitra';
$mitra->email_verified_at = now();
$mitra->approval_status = 'approved';
$mitra->profile_completed = true;
$mitra->save();

# Create mitra profile
$profile = new App\Models\MitraProfile();
$profile->user_id = $mitra->id;
$profile->business_name = 'Bengkel Test';
$profile->contact_person = 'Budi';
$profile->phone = '081234567890';
$profile->address = 'Jl. Test No. 123';
$profile->city = 'Jakarta';
$profile->rating = 4.5;
$profile->balance = 1000000;
$profile->save();
```

### 3. Test Login with Created User
```javascript
fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'admin@test.com',
        password: 'password123'
    })
})
.then(r => r.json())
.then(data => {
    console.log('Login success:', data);
    localStorage.setItem('api_token', data.token);
    localStorage.setItem('user_data', JSON.stringify(data.user));
})
```

### 4. Test Get User Info (setelah login)
```javascript
const token = localStorage.getItem('api_token');

fetch('http://localhost:8000/api/user', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
})
.then(r => r.json())
.then(console.log)
```

### 5. Test Create Booking
```javascript
const token = localStorage.getItem('api_token');

fetch('http://localhost:8000/api/bookings', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        mitra_id: 3, // ID mitra dari database
        service_type: 'Cuci Mobil',
        vehicle_type: 'Sedan',
        vehicle_plate: 'B1234XYZ',
        booking_date: '2025-12-10',
        booking_time: '10:00',
        base_price: 50000,
        discount_amount: 0,
        final_price: 50000,
        payment_method: 'Transfer'
    })
})
.then(r => r.json())
.then(console.log)
```

### 6. Test Get Bookings
```javascript
const token = localStorage.getItem('api_token');

fetch('http://localhost:8000/api/bookings', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
})
.then(r => r.json())
.then(console.log)
```

### 7. Test Get Mitra List
```javascript
const token = localStorage.getItem('api_token');

fetch('http://localhost:8000/api/mitra', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
})
.then(r => r.json())
.then(console.log)
```

## 🎯 Integration Steps

### Step 1: Include API Helper
Di setiap halaman HTML, tambahkan:
```html
<script src="/fe/js/api.js"></script>
```

### Step 2: Check Authentication
Di awal setiap JavaScript file:
```javascript
if (!isLoggedIn()) {
    window.location.href = '/fe/landingpage/lp.html';
}
```

### Step 3: Replace Mock Data
Contoh untuk booking list:

**BEFORE:**
```javascript
const mockBookings = [
    { id: 1, customer: 'John', status: 'menunggu' }
];
displayBookings(mockBookings);
```

**AFTER:**
```javascript
async function loadBookings() {
    try {
        const bookings = await apiRequest('/bookings');
        displayBookings(bookings);
    } catch (error) {
        showError(error.message);
    }
}

loadBookings();
```

## 📚 Documentation

- **Complete Guide**: `FRONTEND_INTEGRATION_GUIDE.md`
- **API Examples**: `fe/js/api-examples.js`
- **Helper Functions**: `fe/js/api.js`
- **Progress Tracker**: `DATABASE_IMPLEMENTATION_PROGRESS.md`

## 🔐 Test Credentials

After creating users via tinker:

| Role     | Email              | Password     |
|----------|-------------------|--------------|
| Admin    | admin@test.com    | password123  |
| Customer | customer@test.com | password123  |
| Mitra    | mitra@test.com    | password123  |

## 🎨 API Endpoints Summary

### Authentication
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/user` - Get current user

### Bookings
- `GET /api/bookings` - List bookings
- `POST /api/bookings` - Create booking
- `GET /api/bookings/{id}` - Show booking
- `PUT /api/bookings/{id}` - Update booking
- `DELETE /api/bookings/{id}` - Delete booking
- `POST /api/bookings/{id}/payment-proof` - Upload payment

### Mitra
- `GET /api/mitra` - List all mitra
- `GET /api/mitra/{id}` - Show mitra detail
- `PUT /api/mitra/profile` - Update profile
- `POST /api/mitra/documents` - Upload documents
- `POST /api/mitra/gallery` - Upload gallery

### Reviews
- `GET /api/reviews` - List reviews
- `POST /api/reviews` - Create review
- `GET /api/reviews/{id}` - Show review
- `DELETE /api/reviews/{id}` - Delete review
- `POST /api/reviews/{id}/reply` - Mitra reply

### Vouchers
- `GET /api/vouchers` - List all (admin)
- `GET /api/vouchers/available` - Available vouchers
- `GET /api/vouchers/my-vouchers` - My claimed vouchers
- `POST /api/vouchers/{id}/claim` - Claim voucher
- `POST /api/vouchers` - Create (admin)
- `PUT /api/vouchers/{id}` - Update (admin)
- `DELETE /api/vouchers/{id}` - Delete (admin)

### Withdrawals
- `GET /api/withdrawals` - List withdrawals
- `POST /api/withdrawals` - Create request
- `GET /api/withdrawals/{id}` - Show withdrawal
- `PUT /api/withdrawals/{id}` - Update withdrawal
- `DELETE /api/withdrawals/{id}` - Delete withdrawal
- `PUT /api/withdrawals/{id}/approve` - Approve (admin)
- `PUT /api/withdrawals/{id}/reject` - Reject (admin)
- `PUT /api/withdrawals/{id}/complete` - Complete (admin)

## ✅ Ready to Use!

Backend API sudah 100% siap. Tinggal:
1. Create test users via tinker
2. Include `api.js` di halaman HTML
3. Replace mock data dengan API calls
4. Test di browser

**Happy coding! 🎉**
