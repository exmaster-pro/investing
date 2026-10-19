<?php
require_once __DIR__ . '/../includes/db.php';
if (!empty($_SESSION['admin_id'])) redirect('admin/dashboard.php');
$errors = [];
if (is_post()) {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT id,full_name,email,password_hash,is_active FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if (!$admin || !(int)$admin['is_active'] || !password_verify($password, $admin['password_hash'])) {
        $errors[] = 'Administrator email or password is incorrect.';
    } else {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        redirect('admin/dashboard.php');
    }
}
$pageTitle = 'Administrator sign in';
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrap"><div class="form-card"><h1 class="h3 fw-bold">Administrator sign in</h1><p class="text-secondary">For authorized administrators only.</p>
 <?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
 <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
 <div class="mb-3"><label class="form-label" for="email">Admin email</label><input class="form-control" id="email" name="email" type="email" required autocomplete="username"></div>
 <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" required autocomplete="current-password"></div>
 <button class="btn btn-primary w-100" type="submit">Sign in to admin</button></form>
 <p class="form-text mt-3 mb-0">Create the first admin using the CLI script described in README.md. Do not add a public admin-registration page.</p>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
