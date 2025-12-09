# Quick Start Guide - Prismo

## 🚀 Getting Started in 3 Steps

### Step 1: Start the Server
```bash
cd c:\Users\Pongo\Utama\capstoneprismo\prismo
php artisan serve
```

### Step 2: Open Your Browser
Visit: `http://127.0.0.1:8000`

### Step 3: Login
Choose one of two methods:

**Option A: Magic Link (Email)**
1. Enter your email
2. Click "Send Magic Link"
3. Check your email
4. Click the link

**Option B: Google OAuth**
1. Click "Continue with Google"
2. Select your Google account
3. Done!

---

## ✅ What's Already Configured

- ✅ Database: `data_prismo` (MySQL)
- ✅ Google OAuth: Ready to use
- ✅ Email SMTP: Gmail configured
- ✅ Migrations: Already run
- ✅ Routes: All set up
- ✅ Views: Login & Dashboard ready

---

## 📋 Important URLs

- **Login Page**: http://127.0.0.1:8000/login
- **Dashboard**: http://127.0.0.1:8000/dashboard (after login)
- **Google OAuth**: http://127.0.0.1:8000/auth/google

---

## 🔑 Credentials Reference

### Database
- **Host**: 127.0.0.1
- **Port**: 3306
- **Database**: data_prismo
- **Username**: root
- **Password**: (empty)

### Google OAuth
- **Client ID**: 524112360392-kdkkgnmsfied48lr60ceja06n4dljcbv.apps.googleusercontent.com
- **Redirect URI**: http://127.0.0.1:8000/auth-google-callback

### Email (Gmail SMTP)
- **Host**: smtp.gmail.com
- **Port**: 587
- **Email**: rachmadtaufikdeniarto@gmail.com

---

## 🛠️ Common Commands

### Start Server
```bash
php artisan serve
```

### Reset Database
```bash
php artisan migrate:fresh
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
```

### View Routes
```bash
php artisan route:list
```

---

## 🐛 Troubleshooting

**Can't connect to database?**
- Make sure MySQL is running
- Verify database `data_prismo` exists

**Magic link not arriving?**
- Check spam folder
- Verify Gmail credentials in `.env`

**Google OAuth not working?**
- Check redirect URI matches exactly
- Verify credentials in Google Console

---

## 📁 Project Files

### Main Files
- `app/Http/Controllers/AuthController.php` - Authentication logic
- `routes/web.php` - All routes
- `resources/views/auth/login.blade.php` - Login page
- `resources/views/dashboard.blade.php` - Dashboard
- `.env` - Configuration

### Database
- `database/migrations/` - Database structure
- `app/Models/User.php` - User model

---

## 🎯 Next Steps

1. Test both login methods
2. Customize the dashboard
3. Add more features as needed
4. Deploy to production

---

**Need help?** Check the full `README.md` for detailed documentation.
