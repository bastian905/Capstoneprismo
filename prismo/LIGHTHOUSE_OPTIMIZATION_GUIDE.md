# Panduan Optimasi Lighthouse Performance

## 1. Image Optimization

### Implementasi Lazy Loading
Sudah diimplementasikan di beberapa file (loading="lazy"):
- antrian.js (avatar images)
- review.js (review images)

**Action Required**: Tambahkan lazy loading ke semua gambar di seluruh project

### Compress & Resize Images
```bash
# Install image optimization tool
npm install --save-dev imagemin imagemin-mozjpeg imagemin-pngquant

# Atau gunakan online tools untuk existing images:
# - TinyPNG (https://tinypng.com)
# - Squoosh (https://squoosh.app)
```

**Recommended**: Convert ke WebP format untuk size lebih kecil
```php
// Laravel Image Intervention
composer require intervention/image
```

## 2. CSS Optimization

### Minify CSS
```bash
# Install Laravel Mix atau Vite untuk minification
npm run build
```

### Critical CSS
Extract CSS yang dibutuhkan untuk above-the-fold content

### Remove Unused CSS
```bash
npm install -D purgecss
```

## 3. JavaScript Optimization

### Defer Non-Critical Scripts
```html
<!-- Change from: -->
<script src="script.js"></script>

<!-- To: -->
<script src="script.js" defer></script>
```

### Async for Third-Party Scripts
```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" async></script>
```

## 4. Font Optimization

### Preconnect to Font Resources
Already implemented:
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
```

### Font Display Swap
```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
```

## 5. Caching Strategy

### Browser Caching (.htaccess)
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
  ExpiresByType application/x-javascript "access plus 1 month"
</IfModule>

<IfModule mod_headers.c>
  <FilesMatch "\.(jpg|jpeg|png|gif|webp|svg)$">
    Header set Cache-Control "max-age=31536000, public"
  </FilesMatch>
  <FilesMatch "\.(css|js)$">
    Header set Cache-Control "max-age=2592000, public"
  </FilesMatch>
</IfModule>
```

### Laravel Response Caching
```php
// config/cache.php - already configured
Route::middleware('cache.headers:public;max_age=2628000')->group(function () {
    Route::get('/images/{path}', function ($path) {
        return response()->file(public_path("images/{$path}"));
    })->where('path', '.*');
});
```

## 6. Compression

### Enable Gzip (.htaccess)
```apache
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/json
</IfModule>
```

## 7. Database Query Optimization

### Laravel Query Optimization
```php
// Use eager loading
$users = User::with('bookings')->get(); // Good
$users = User::all(); // Bad (N+1 problem)

// Use select specific columns
User::select('id', 'name', 'email')->get(); // Good
User::all(); // Bad (loads all columns)

// Use pagination
User::paginate(20); // Good
User::all(); // Bad (loads all data)
```

### Index Database Columns
```php
// In migration
Schema::table('users', function (Blueprint $table) {
    $table->index('email');
    $table->index('last_activity_at');
});
```

## 8. CDN Integration (Optional)

### Use CDN for Static Assets
```html
<!-- Instead of local fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter" rel="stylesheet">

<!-- Use CDN for libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

## 9. Reduce Third-Party Scripts

### Self-host Critical Resources
```bash
# Download Font Awesome locally instead of CDN
npm install @fortawesome/fontawesome-free
```

## 10. Performance Monitoring

### Laravel Debugbar (Development Only)
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Chrome DevTools Lighthouse
1. Open Chrome DevTools (F12)
2. Go to Lighthouse tab
3. Generate report
4. Follow recommendations

## Implementation Checklist

### High Priority (Do First)
- [ ] Add lazy loading to all images
- [ ] Minify CSS and JS files
- [ ] Enable Gzip compression
- [ ] Configure browser caching
- [ ] Optimize database queries
- [ ] Add database indexes
- [ ] Defer non-critical JavaScript

### Medium Priority
- [ ] Convert images to WebP
- [ ] Implement critical CSS
- [ ] Remove unused CSS with PurgeCSS
- [ ] Optimize font loading
- [ ] Add service worker for offline support

### Low Priority (Nice to Have)
- [ ] Implement CDN
- [ ] Add HTTP/2 server push
- [ ] Implement code splitting
- [ ] Add progressive web app (PWA) features
- [ ] Implement server-side rendering (SSR)

## Expected Results

### Before Optimization
- Performance Score: ~50-60
- First Contentful Paint: 3-4s
- Largest Contentful Paint: 4-6s
- Total Blocking Time: 500-1000ms
- Cumulative Layout Shift: 0.1-0.3

### After Optimization (Target)
- Performance Score: 90+
- First Contentful Paint: <1.8s
- Largest Contentful Paint: <2.5s
- Total Blocking Time: <200ms
- Cumulative Layout Shift: <0.1
