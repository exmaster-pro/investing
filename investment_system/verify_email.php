<?php
require_once __DIR__ . '/includes/db.php';
if (empty($_SESSION['pending_verification_user_id'])) {
    flash('warning', 'Register first to verify your email.');
    redirect('register.php');
}
$userId = (int)$_SESSION['pending_verification_user_id'];
$errors = [];
if (is_post()) {
    verify_csrf();
    $code = trim((string)($_POST['code'] ?? ''));
    if (!preg_match('/^\d{6}$/', $code)) {
        $errors[] = 'Enter the six-digit code from your email.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id,code_hash,expires_at,attempts,used_at FROM email_verifications WHERE user_id = ? AND used_at IS NULL ORDER BY id DESC LIMIT 1');
        $stmt->execute([$userId]);
        $record = $stmt->fetch();
        if (!$record || strtotime($record['expires_at']) < time()) {
            $errors[] = 'The code has expired. Please register again or contact the administrator for help.';
        } elseif ((int)$record['attempts'] >= 5) {
            $errors[] = 'Too many attempts. Please contact the administrator.';
        } else {
            $pdo->prepare('UPDATE email_verifications SET attempts = attempts + 1 WHERE id = ?')->execute([$record['id']]);
            if (password_verify($code, $record['code_hash'])) {
                $pdo->beginTransaction();
                $pdo->prepare('UPDATE email_verifications SET used_at = NOW() WHERE id = ?')->execute([$record['id']]);
                $pdo->prepare('UPDATE users SET email_verified = 1 WHERE id = ?')->execute([$userId]);
                $pdo->commit();
                unset($_SESSION['pending_verification_user_id']);
                flash('success', 'Email verified. Your account is now waiting for administrator approval.');
                redirect('login.php');
            } else {
                $errors[] = 'That code is not correct.';
            }
        }
    }
}
$pageTitle = 'Verify email';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="form-card">
 <h1 class="h3 fw-bold">Verify your email</h1><p class="text-secondary">Enter the six-digit code sent to your registered email address.</p>
 <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
 <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="code" class="form-label">Verification code</label>
  <input id="code" class="form-control form-control-lg text-center code-input mb-3" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code">
  <button class="btn btn-primary w-100" type="submit">Verify email</button>
 </form><p class="form-text mt-3 mb-0">For security, the code expires after <?= (int)OTP_TTL_MINUTES ?> minutes and is limited to five attempts.</p>
</div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
