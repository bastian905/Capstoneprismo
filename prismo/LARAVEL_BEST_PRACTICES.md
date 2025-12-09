# Laravel Best Practices - PRISMO Project

## 1. Controller Best Practices

### Single Responsibility
✅ **Good**: Each controller handles one resource
```php
class BookingController extends Controller {
    public function index() { /* list bookings */ }
    public function store() { /* create booking */ }
    public function show($id) { /* show one booking */ }
    public function update($id) { /* update booking */ }
    public function destroy($id) { /* delete booking */ }
}
```

❌ **Bad**: Controller handles multiple resources
```php
class AdminController extends Controller {
    public function bookings() { }
    public function customers() { }
    public function mitra() { }
    public function vouchers() { }
}
```

### Use Form Requests
✅ **Already Implemented**:
- `RegisterRequest.php` - with password space removal
- `ChangePasswordRequest.php` - with validation
- `ResetPasswordRequest.php` - with validation

### Use Resource Controllers
```php
Route::resource('bookings', BookingController::class);
// Instead of:
Route::get('/bookings', [BookingController::class, 'index']);
Route::post('/bookings', [BookingController::class, 'store']);
// etc...
```

## 2. Model Best Practices

### Use Eloquent Relationships
```php
class User extends Model {
    public function bookings() {
        return $this->hasMany(Booking::class);
    }
    
    public function mitra() {
        return $this->hasOne(Mitra::class);
    }
}

class Booking extends Model {
    public function user() {
        return $this->belongsTo(User::class);
    }
}
```

### Use Accessors and Mutators
```php
class User extends Model {
    // Accessor
    protected function photoUrl(): Attribute {
        return Attribute::make(
            get: fn () => $this->avatar 
                ? Storage::url($this->avatar) 
                : asset('images/profile.png')
        );
    }
    
    // Mutator
    protected function password(): Attribute {
        return Attribute::make(
            set: fn ($value) => bcrypt($value)
        );
    }
}
```

### Use Query Scopes
```php
class User extends Model {
    public function scopeActive($query) {
        return $query->where('status', 'active');
    }
    
    public function scopeMitra($query) {
        return $query->where('role', 'mitra');
    }
}

// Usage:
User::active()->mitra()->get();
```

## 3. Validation Best Practices

### Centralize Validation Rules
```php
// app/Rules/NoSpaces.php
class NoSpaces implements Rule {
    public function passes($attribute, $value) {
        return !preg_match('/\s/', $value);
    }
    
    public function message() {
        return ':attribute tidak boleh mengandung spasi.';
    }
}

// Usage:
'password' => ['required', new NoSpaces, 'min:8']
```

### Use Custom Rule Objects
```php
php artisan make:rule ValidQRIS
```

## 4. Database Best Practices

### Use Migrations Properly
✅ **Good**:
```php
// Create table
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('service_type');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

### Add Database Indexes
```php
Schema::table('users', function (Blueprint $table) {
    $table->index('email');
    $table->index('last_activity_at');
    $table->index(['role', 'approval_status']);
});
```

### Use Database Transactions
```php
DB::transaction(function () {
    $booking = Booking::create([...]);
    $payment = Payment::create([...]);
    Notification::create([...]);
});
```

## 5. Security Best Practices

### CSRF Protection
✅ **Already Used**: `@csrf` in forms

### Mass Assignment Protection
```php
class User extends Model {
    protected $fillable = ['name', 'email', 'password'];
    // Or use $guarded
    protected $guarded = ['id', 'is_admin'];
}
```

### SQL Injection Prevention
✅ **Use Eloquent or Query Builder**:
```php
User::where('email', $email)->first(); // Good
DB::raw("SELECT * FROM users WHERE email = '$email'"); // Bad!
```

### XSS Prevention
```php
// Blade automatically escapes
{{ $user->name }} // Escaped
{!! $user->name !!} // NOT escaped (dangerous!)
```

## 6. Error Handling Best Practices

### Use Try-Catch Blocks
```php
public function changePassword(ChangePasswordRequest $request) {
    try {
        $user = Auth::user();
        $user->password = $request->new_password;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    } catch (\Exception $e) {
        Log::error('Password change failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem.'
        ], 500);
    }
}
```

### Custom Exception Handler
```php
// app/Exceptions/Handler.php
public function render($request, Throwable $exception) {
    if ($exception instanceof ModelNotFoundException) {
        return response()->json([
            'error' => 'Data tidak ditemukan'
        ], 404);
    }
    
    return parent::render($request, $exception);
}
```

## 7. Route Organization

### Group Routes by Middleware
```php
Route::middleware(['auth'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard']);
        Route::get('/booking', [CustomerController::class, 'booking']);
    });
    
    Route::prefix('mitra')->group(function () {
        Route::get('/dashboard', [MitraController::class, 'dashboard']);
        Route::get('/antrian', [MitraController::class, 'antrian']);
    });
});
```

### Use Route Names
```php
Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])
    ->name('customer.dashboard');

// Usage in Blade:
<a href="{{ route('customer.dashboard') }}">Dashboard</a>

// Usage in Controller:
return redirect()->route('customer.dashboard');
```

## 8. Service Layer Pattern

### Create Service Classes
```php
// app/Services/BookingService.php
class BookingService {
    public function createBooking(array $data) {
        DB::beginTransaction();
        try {
            $booking = Booking::create($data);
            $this->sendNotification($booking);
            $this->createInvoice($booking);
            
            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function sendNotification($booking) { }
    private function createInvoice($booking) { }
}

// In Controller:
public function store(Request $request, BookingService $bookingService) {
    $booking = $bookingService->createBooking($request->validated());
    return response()->json($booking);
}
```

## 9. Logging Best Practices

### Use Laravel Logging
```php
use Illuminate\Support\Facades\Log;

// Different log levels
Log::info('User logged in', ['user_id' => $user->id]);
Log::warning('Failed login attempt', ['email' => $email]);
Log::error('Payment failed', ['booking_id' => $id, 'error' => $e->getMessage()]);
Log::critical('Database connection lost');
```

### Create Custom Log Channels
```php
// config/logging.php
'channels' => [
    'booking' => [
        'driver' => 'daily',
        'path' => storage_path('logs/booking.log'),
        'level' => 'info',
        'days' => 14,
    ],
]

// Usage:
Log::channel('booking')->info('New booking created');
```

## 10. Testing Best Practices

### Write Feature Tests
```php
php artisan make:test BookingTest

public function test_user_can_create_booking() {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/api/bookings', [
            'service_type' => 'Steam Mobil',
            'date' => '2025-12-10',
        ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'service_type' => 'Steam Mobil',
    ]);
}
```

## 11. Performance Best Practices

### Eager Loading
```php
// Bad (N+1 problem)
$users = User::all();
foreach ($users as $user) {
    echo $user->bookings->count();
}

// Good
$users = User::with('bookings')->get();
foreach ($users as $user) {
    echo $user->bookings->count();
}
```

### Query Optimization
```php
// Select only needed columns
User::select('id', 'name', 'email')->get();

// Use pagination
User::paginate(20);

// Use cursor for large datasets
User::cursor()->each(function ($user) {
    // Process $user
});
```

### Cache Frequently Used Data
```php
// Cache for 1 hour
$users = Cache::remember('active_users', 3600, function () {
    return User::active()->get();
});

// Clear cache when data changes
Cache::forget('active_users');
```

## 12. Code Organization

### Use Repository Pattern (Optional)
```php
// app/Repositories/UserRepository.php
class UserRepository {
    public function findInactive($years = 3) {
        return User::where('last_activity_at', '<', now()->subYears($years))
            ->get();
    }
}
```

### Use Events and Listeners
```php
// app/Events/BookingCreated.php
class BookingCreated {
    public function __construct(public Booking $booking) {}
}

// app/Listeners/SendBookingNotification.php
class SendBookingNotification {
    public function handle(BookingCreated $event) {
        Mail::to($event->booking->user)->send(...);
    }
}

// In Controller:
event(new BookingCreated($booking));
```

## Implementation Priority

### High Priority (Do Now)
1. ✅ Use Form Requests for validation (Already done for password forms)
2. Add database indexes for frequently queried columns
3. Implement eager loading to avoid N+1 queries
4. Add try-catch blocks in critical operations
5. Use named routes consistently

### Medium Priority
1. Create service layer for complex business logic
2. Implement query scopes for common filters
3. Add comprehensive logging
4. Write feature tests for critical flows
5. Use accessors/mutators for data transformation

### Low Priority (Nice to Have)
1. Implement repository pattern
2. Use events and listeners
3. Add custom rule objects
4. Implement caching strategy
5. Create custom exception handlers

## Current Implementation Status

✅ **Implemented**:
- Form Requests (RegisterRequest, ChangePasswordRequest, ResetPasswordRequest)
- Model Events (User::boot() for cascade delete)
- Middleware (UpdateLastActivity)
- Artisan Commands (CleanupInactiveAccounts, WarnInactiveAccounts)
- Mail System (AccountDeletionWarning)
- Error Pages (404, 403, 500)

⏳ **Needs Improvement**:
- Add database indexes
- Implement eager loading
- Add comprehensive error handling
- Create service layer for booking logic
- Write tests
- Implement caching
