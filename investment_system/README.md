# Investment Portal — PHP / MySQL / XAMPP

A starter/demo application with separate user and administrator pages:
- User registration and email OTP verification via Gmail SMTP / PHPMailer
- Admin approval of verified accounts
- Investment amount submissions and date-filtered Chart.js graph
- Admin user and investment tables
- Admin-published news on the user dashboard and separate news page

**Important:** this is an investment-recording demonstration. It does not accept payments, connect to a broker, calculate real returns, or verify that funds were transferred. The chart plots submitted amounts by date and keeps each currency separate.

## Requirements
- Windows with XAMPP (Apache, PHP 8.1+ recommended, MySQL/MariaDB)
- Composer
- A Gmail sender account with Google 2-Step Verification and an App Password
- Internet access for Composer, CDN assets, and Gmail SMTP

## 1. Put the project in XAMPP
1. Extract the `investment_system` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Select **Import**, choose `database.sql`, and run it. It creates the `investment_system` database and tables.

If your MySQL `root` account has a password, update `DB_PASS` in `config/config.php` or configure a local override.

## 2. Install PHPMailer
Open Command Prompt and run:

```bat
cd C:\xampp\htdocs\investment_system
composer require phpmailer/phpmailer
```

This creates `vendor/autoload.php`. Make sure the PHP CLI used by Composer is compatible with your XAMPP PHP.

## 3. Configure Gmail SMTP
1. Sign in to the Gmail account that will send the verification messages.
2. Enable 2-Step Verification in the Google Account.
3. Create a Google **App Password** for this application. Do not use your normal Gmail password.
4. Copy `config/local.example.php` to `config/local.php`.
5. Fill in the local settings:

```php
<?php
define('LOCAL_DB_PASS', '');
define('LOCAL_MAIL_USERNAME', 'your-sender@gmail.com');
define('LOCAL_MAIL_PASSWORD', 'your-16-character-app-password');
```

6. In `config/config.php`, set `MAIL_FROM` to the same Gmail address as `MAIL_USERNAME`. Change `APP_URL` if the folder name or host is different.
7. Never publish or share `config/local.php`. For a live site, use environment variables or a secrets manager and HTTPS.

## 4. Create the first administrator
Do **not** create an admin registration form accessible to visitors. Use the CLI-only helper.

From Command Prompt:

```bat
cd C:\xampp\htdocs\investment_system
C:\xampp\php\php.exe admin\create_admin.php "Portal Administrator" "admin@example.com" "Choose-A-Long-Unique-Password"
```

Replace the example email and password with your own. Use a unique password of at least 14 characters. The command should report that the admin account was created. If `C:\xampp\php\php.exe` is not your PHP path, adjust it.

Sign in at `http://localhost/investment_system/admin/login.php`.

## 5. Test the user workflow
1. Visit `http://localhost/investment_system/register.php`.
2. Register using an email inbox you can access.
3. Check the inbox and spam folder for the six-digit code.
4. Enter the code at the verification page.
5. Sign in to the admin page and open **Users**. Approve the verified account.
6. Sign in as that user at `http://localhost/investment_system/login.php`.
7. Submit a currency and amount; the record appears in the dashboard chart and in **Admin → Investments**.
8. As admin, use **Post news** to publish an article. It appears on the user's dashboard and News page.

## 6. Configuration and security notes
- Passwords are hashed using PHP `password_hash`; OTP codes are stored as hashes and expire after 10 minutes.
- Verification attempts are limited to five per code. This starter does not implement production-grade per-IP/email rate limiting or automated OTP resend; add these before public deployment.
- Forms use session CSRF tokens, prepared SQL statements, output escaping, and role checks.
- For production, use HTTPS, set secure cookie flags, least-privilege database credentials, rate limiting, audit logs, backups, security monitoring, and a proper password reset flow.
- Do not expose phpMyAdmin to the public internet.
- `APP_URL` is `http://localhost/investment_system` by default. It must match the folder and domain.
- CDN-hosted Bootstrap and Chart.js need internet access in the browser.
- Admin approval is separate from email verification. A verified user remains unable to sign in until approved.
- The admin can see users' submitted names, emails, verification and approval status, and investment entries. Password hashes and OTP hashes are not displayed.
- The demonstration graph plots **submitted entries by date**, not profit, portfolio value, or actual market performance. Entries in different currencies are not converted or combined into a single monetary total.
