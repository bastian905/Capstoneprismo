# Prismo - Laravel Authentication Project

A Laravel application with Google OAuth and Magic Link (passwordless email) authentication.

## Features

- ✅ **Google OAuth Login** - Sign in with Google account
- ✅ **Magic Link Login** - Passwordless email authentication
- ✅ **Secure Sessions** - Session-based authentication
- ✅ **User Dashboard** - Protected dashboard after login
- ✅ **Modern UI** - Clean and responsive design

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Gmail account (for magic link emails)

### Setup Steps

1. **Navigate to the project directory**
   ```bash
   cd c:\Users\Pongo\Utama\capstoneprismo\prismo
   ```

2. **Create MySQL Database**
   - Open MySQL and create a database named `data_prismo`
   ```sql
   CREATE DATABASE data_prismo;
   ```

3. **Install Dependencies** (Already done)
   ```bash
   composer install
   ```

4. **Environment Configuration** (Already configured)
   - The `.env` file is already set up with your credentials
   - Database: `data_prismo`
   - Mail: Gmail SMTP configured
   - Google OAuth credentials configured

5. **Run Migrations** (Already done)
   ```bash
   php artisan migrate:fresh
   ```

6. **Start the Development Server**
   ```bash
   php artisan serve
   ```

   The application will be available at: `http://127.0.0.1:8000`

## Usage

### Login Methods

#### 1. Magic Link (Email)
1. Visit `http://127.0.0.1:8000/login`
2. Enter your email address
3. Click "Send Magic Link"
4. Check your email inbox
5. Click the magic link in the email
6. You'll be automatically logged in

#### 2. Google OAuth
1. Visit `http://127.0.0.1:8000/login`
2. Click "Continue with Google"
3. Select your Google account
4. Authorize the application
5. You'll be redirected to the dashboard

### Routes

- `GET /login` - Login page
- `GET /auth/google` - Redirect to Google OAuth
- `GET /auth-google-callback` - Google OAuth callback
- `POST /auth/magic-link` - Send magic link email
- `GET /auth/magic-link/verify` - Verify magic link token
- `GET /dashboard` - Protected dashboard (requires authentication)
- `POST /logout` - Logout

## Project Structure

```
prismo/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── AuthController.php    # Authentication logic
│   └── Models/
│       └── User.php                   # User model
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 2025_12_05_150721_add_google_fields_to_users_table.php
│       └── 2025_12_05_150744_create_magic_link_tokens_table.php
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php        # Login page
│       └── dashboard.blade.php        # Dashboard page
├── routes/
│   └── web.php                        # Web routes
├── .env                               # Environment configuration
└── README.md                          # This file
```

## Database Schema

### Users Table
- `id` - Primary key
- `google_id` - Google account ID (nullable)
- `name` - User's name
- `email` - User's email (unique)
- `password` - Password hash (nullable for OAuth users)
- `avatar` - Profile picture URL (nullable)
- `email_verified_at` - Email verification timestamp
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp

### Magic Link Tokens Table
- `id` - Primary key
- `email` - User's email
- `token` - Unique verification token
- `expires_at` - Token expiration timestamp
- `created_at` - Token creation timestamp

## Configuration

### Google OAuth Setup
Configure your Google OAuth credentials in `.env`:
- Client ID: `[Your Google OAuth Client ID]`
- Client Secret: `[Your Google OAuth Client Secret]`
- Redirect URI: `http://127.0.0.1:8000/auth-google-callback`

### Email Configuration
Gmail SMTP is configured for sending magic links:
- Host: `smtp.gmail.com`
- Port: `587`
- Encryption: `tls`
- Username: `rachmadtaufikdeniarto@gmail.com`
- App Password: Configured in `.env`

## Security Notes

1. **Magic Link Expiration**: Magic links expire after 15 minutes
2. **One-Time Use**: Magic link tokens are deleted after use
3. **Session Security**: Sessions are configured with secure settings
4. **Password Nullable**: Users who sign up via Google OAuth don't need passwords
5. **CSRF Protection**: All forms include CSRF tokens

## Troubleshooting

### Migration Issues
If you encounter migration errors:
```bash
php artisan migrate:fresh
```

### Session Issues
Clear config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Email Not Sending
- Verify Gmail SMTP credentials in `.env`
- Ensure "Less secure app access" is enabled or use App Password
- Check spam folder for magic link emails

## Development

### Testing Different Login Methods

1. **Test Magic Link**:
   - Use any valid email address
   - Check your email for the magic link
   - Link expires in 15 minutes

2. **Test Google OAuth**:
   - Click "Continue with Google"
   - First-time users will create a new account
   - Returning users will be logged in directly

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects.

For more information, visit [Laravel Documentation](https://laravel.com/docs).

