# Two-Factor Authentication Setup

## Overview
This Laravel project is configured with Laravel Fortify for robust authentication including mandatory 2FA (Two-Factor Authentication).

## Features Implemented

### 1. **User Registration with 2FA**
- Users register with email, name, and password
- After successful registration, they are immediately redirected to set up 2FA
- Registration endpoint: `POST /register`

### 2. **Login with 2FA**
- Standard email/password login
- After login, users must confirm 2FA via authenticator app
- Login endpoint: `POST /login`

### 3. **Two-Factor Authentication**
- **TOTP (Time-based One-Time Password)**: Uses authenticator apps like Google Authenticator, Authy, Microsoft Authenticator
- **Recovery Codes**: Users receive 8 recovery codes for backup access
- 2FA Setup page: `/two-factor-challenge`

## Database
- MySQL is configured (credentials in `.env`)
- Two 2FA-related columns added to users table:
  - `two_factor_secret`: Stores the TOTP secret
  - `two_factor_recovery_codes`: Backup recovery codes
  - `two_factor_confirmed_at`: Timestamp when 2FA was confirmed

## Configuration Files
- `config/fortify.php`: Main authentication configuration
  - `Features::registration()` - User registration enabled
  - `Features::twoFactorAuthentication()` - 2FA required with password confirmation
  - Email verification is disabled (2FA replaces it)

## Key Routes (Provided by Fortify)
```
POST   /register                           - Register new user
POST   /login                              - Login
POST   /logout                             - Logout
GET    /two-factor-challenge               - 2FA entry page
POST   /two-factor-challenge               - Submit 2FA code
POST   /user/two-factor-authentication     - Enable 2FA
DELETE /user/two-factor-authentication     - Disable 2FA (requires confirmation)
GET    /user/two-factor-recovery-codes     - Get recovery codes
```

## User Flow

### Registration Flow
1. User visits `/register`
2. Submits name, email, password
3. Account created
4. Automatically redirected to 2FA setup page
5. Scans QR code or enters secret manually
6. Confirms 2FA with code from authenticator app
7. Receives recovery codes
8. Access granted to application

### Login Flow
1. User visits `/login`
2. Enters email and password
3. If 2FA is confirmed, prompted for 2FA code
4. Enters code from authenticator app
5. Access granted

## Recovery Codes
- 8 recovery codes provided after 2FA setup
- Each code can be used once to bypass 2FA
- Useful for backup access if authenticator is lost
- Can be regenerated at any time

## Customization

### Middleware
The `EnsureTwoFactorEnabled` middleware can be applied to routes that require 2FA to be confirmed:
```php
Route::middleware(['auth', 'ensure.two-factor'])->group(function () {
    // Protected routes
});
```

### Rate Limiting
- Login attempts: 5 per minute per email/IP
- 2FA attempts: 5 per minute per session

## Security Notes
- Passwords are hashed using bcrypt (12 rounds)
- TOTP secrets are securely stored
- Recovery codes are encrypted
- Session-based authentication with CSRF protection

## Testing 2FA Locally
1. Use an authenticator app:
   - Google Authenticator (iOS/Android)
   - Authy (iOS/Android)
   - Microsoft Authenticator
   - Any compatible TOTP app

2. Or use `php artisan tinker` to generate codes for testing:
```php
$user = User::first();
app(Google2FA::class)->getQRCodeInline(
    config('app.name'),
    $user->email,
    $user->two_factor_secret
);
```

## Next Steps
1. Start the development server: `php artisan serve`
2. Visit `http://localhost:8000/register` to test signup
3. Create a test account and confirm 2FA setup works
4. Test login and 2FA verification
