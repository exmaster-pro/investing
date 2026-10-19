<?php
require_once __DIR__ . '/includes/db.php';
if (!empty($_SESSION['user_id'])) redirect('dashboard.php');
$errors = [];
$email = '';
if (is_post()) {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id,full_name,email,password_hash,email_verified,approval_status FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'Email or password is incorrect.';
    } elseif (!(int)$user['email_verified']) {
        $errors[] = 'Please verify your email before signing in.';
    } elseif ($user['approval_status'] !== 'approved') {
        $errors[] = $user['approval_status'] === 'rejected' ? 'Your account was not approved. Contact the administrator.' : 'Your email is verified, but your account is waiting for administrator approval.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        redirect('dashboard.php');
    }
}
$pageTitle = 'Sign in';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="form-card">
 <h1 class="h3 fw-bold">Welcome back</h1><p class="text-secondary">Sign in to your approved account.</p>
 <?php foreach ($errors as $error): ?><div class="alert alert-warning"><?= e($error) ?></div><?php endforeach; ?>
 <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" type="email" name="email" required autocomplete="username" value="<?= e($email) ?>"></div>
  <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required autocomplete="current-password"></div>
  <button class="btn btn-primary w-100" type="submit">Sign in</button>
 </form><p class="mt-3 mb-0 text-center">New here? <a href="<?= e(app_url('register.php')) ?>">Create an account</a></p>
</div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
