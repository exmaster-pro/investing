<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$pdo = db();
$stats = [
 'users' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
 'pending' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE email_verified=1 AND approval_status='pending'")->fetchColumn(),
 'investments' => (int)$pdo->query('SELECT COUNT(*) FROM investments')->fetchColumn(),
 'news' => (int)$pdo->query("SELECT COUNT(*) FROM news WHERE status='published'")->fetchColumn()
];
$latest = $pdo->query('SELECT id,full_name,email,email_verified,approval_status,created_at FROM users ORDER BY created_at DESC LIMIT 8')->fetchAll();
$pageTitle = 'Admin overview';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
<h1 class="h3 fw-bold mb-4">Overview</h1>
<div class="row g-3 mb-4">
 <?php foreach (['Registered users'=>'users','Verified users awaiting approval'=>'pending','Investment entries'=>'investments','Published news'=>'news'] as $label=>$key): ?>
 <div class="col-sm-6 col-xl-3"><div class="metric-card"><div class="metric-label"><?= e($label) ?></div><div class="metric-value"><?= number_format($stats[$key]) ?></div></div></div>
 <?php endforeach; ?>
</div>
<section class="content-card"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 fw-bold mb-0">Latest registrations</h2><a href="<?= e(app_url('admin/users.php')) ?>">View all users</a></div>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Name</th><th>Email</th><th>Email verified</th><th>Approval</th><th>Registered</th></tr></thead><tbody>
<?php if (!$latest): ?><tr><td colspan="5" class="text-center text-secondary">No users registered yet.</td></tr><?php else: foreach ($latest as $row): ?><tr><td><?= e($row['full_name']) ?></td><td><?= e($row['email']) ?></td><td><?= (int)$row['email_verified'] ? '<span class="badge text-bg-success">Yes</span>' : '<span class="badge text-bg-warning">No</span>' ?></td><td><?= e(user_status_label($row['approval_status'])) ?></td><td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
