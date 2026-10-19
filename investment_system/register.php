<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/mailer.php';
if (!empty($_SESSION['user_id'])) redirect('dashboard.php');
$errors = [];
$name = $email = '';
if (is_post()) {
    verify_csrf();
    $name = trim((string)($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (mb_strlen($name) < 2 || mb_strlen($name) > 120) $errors[] = 'Enter your full name (2–120 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 10) $errors[] = 'Password must contain at least 10 characters.';
    if (!$errors) {
        $pdo = db();
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'That email address is already registered. Try signing in.';
        } else {
            $code = (string)random_int(100000, 999999);
            $expires = date('Y-m-d H:i:s', time() + OTP_TTL_MINUTES * 60);
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO users (full_name,email,password_hash,email_verified,approval_status,created_at) VALUES (?,?,?,0,'pending',NOW())");
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                $userId = (int)$pdo->lastInsertId();
                $otp = $pdo->prepare('INSERT INTO email_verifications (user_id,code_hash,expires_at,attempts,used_at,created_at) VALUES (?,?,?,0,NULL,NOW())');
                $otp->execute([$userId, password_hash($code, PASSWORD_DEFAULT), $expires]);
                $pdo->commit();
                if (send_otp_email($email, $name, $code)) {
                    $_SESSION['pending_verification_user_id'] = $userId;
                    flash('success', 'We sent a verification code to your email address.');
                    redirect('verify_email.php');
                }
                // Keep the account and hashed OTP record out of a misleading "sent" state.
                $pdo->prepare('DELETE FROM email_verifications WHERE user_id = ?')->execute([$userId]);
                $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
                $errors[] = 'We could not send the verification email. Check SMTP setup and try again.';
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                error_log('Registration error: ' . $e->getMessage());
                $errors[] = 'Registration could not be completed. Please try again.';
            }
        }
    }
}
$pageTitle = 'Create account';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="form-card">
 <h1 class="h3 fw-bold">Create your account</h1><p class="text-secondary">A verification code will be sent to your email.</p>
 <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
 <form method="post" novalidate>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <div class="mb-3"><label class="form-label" for="full_name">Full name</label><input class="form-control" id="full_name" name="full_name" maxlength="120" required value="<?= e($name) ?>"></div>
  <div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" type="email" name="email" required autocomplete="email" value="<?= e($email) ?>"></div>
  <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" minlength="10" required autocomplete="new-password"><div class="form-text">Use at least 10 characters.</div></div>
  <button class="btn btn-primary w-100" type="submit">Register and send code</button>
 </form><p class="mt-3 mb-0 text-center">Already registered? <a href="<?= e(app_url('login.php')) ?>">Sign in</a></p>
</div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
