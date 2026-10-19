<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$pdo = db();
if (is_post()) {
    verify_csrf();
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $action = (string)($_POST['action'] ?? '');
    if (!$userId || !in_array($action, ['approve','reject','pending'], true)) {
        flash('danger', 'Invalid user action.');
    } else {
        $stmt = $pdo->prepare('SELECT id,email_verified FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $target = $stmt->fetch();
        if (!$target) {
            flash('danger', 'User was not found.');
        } elseif ($action === 'approve' && !(int)$target['email_verified']) {
            flash('warning', 'Verify the email before approving this account.');
        } else {
            $status = ['approve'=>'approved','reject'=>'rejected','pending'=>'pending'][$action];
            $pdo->prepare('UPDATE users SET approval_status = ?, reviewed_at = NOW() WHERE id = ?')->execute([$status, $userId]);
            flash('success', 'Account status updated.');
        }
    }
    redirect('admin/users.php');
}
$q = trim((string)($_GET['q'] ?? ''));
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT id,full_name,email,email_verified,approval_status,created_at,reviewed_at FROM users WHERE full_name LIKE ? OR email LIKE ? ORDER BY created_at DESC');
    $term = '%' . $q . '%'; $stmt->execute([$term,$term]); $users = $stmt->fetchAll();
} else {
    $users = $pdo->query('SELECT id,full_name,email,email_verified,approval_status,created_at,reviewed_at FROM users ORDER BY created_at DESC')->fetchAll();
}
$pageTitle = 'Manage users';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
<section class="content-card"><div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><div><h1 class="h4 fw-bold mb-1">Registered users</h1><p class="text-secondary small mb-0">Personal data is restricted to authorized administrators.</p></div>
<form class="d-flex gap-2" method="get"><input class="form-control form-control-sm" name="q" value="<?= e($q) ?>" placeholder="Search name or email" aria-label="Search users"><button class="btn btn-sm btn-primary">Search</button></form></div>
<div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Verified</th><th>Approval status</th><th>Registered</th><th>Actions</th></tr></thead><tbody>
<?php if (!$users): ?><tr><td colspan="7" class="text-center text-secondary py-4">No matching users.</td></tr><?php else: foreach ($users as $row): ?><tr>
<td><?= (int)$row['id'] ?></td><td><?= e($row['full_name']) ?></td><td><?= e($row['email']) ?></td>
<td><?= (int)$row['email_verified'] ? '<span class="badge text-bg-success">Verified</span>' : '<span class="badge text-bg-warning">Not verified</span>' ?></td>
<td><?= e(user_status_label($row['approval_status'])) ?></td><td><?= e(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
<td><form method="post" class="d-flex gap-1 flex-wrap"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="user_id" value="<?= (int)$row['id'] ?>">
<button class="btn btn-sm btn-success" name="action" value="approve" <?= !(int)$row['email_verified'] || $row['approval_status']==='approved' ? 'disabled' : '' ?>>Approve</button>
<button class="btn btn-sm btn-outline-danger" name="action" value="reject" <?= $row['approval_status']==='rejected' ? 'disabled' : '' ?>>Reject</button>
<button class="btn btn-sm btn-outline-secondary" name="action" value="pending">Reset</button></form></td>
</tr><?php endforeach; endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
