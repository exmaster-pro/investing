<?php
require_once __DIR__ . '/../includes/db.php';
require_admin();
$rows = db()->query('SELECT i.id,u.full_name,u.email,i.currency,i.amount,i.status,i.submitted_at FROM investments i JOIN users u ON u.id=i.user_id ORDER BY i.submitted_at DESC')->fetchAll();
$pageTitle = 'Investment records';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
<section class="content-card"><h1 class="h4 fw-bold">Investment submissions</h1><p class="text-secondary small">Amounts are user-entered records, not proof of a payment or actual investment.</p>
<div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>ID</th><th>User</th><th>Email</th><th>Currency</th><th class="text-end">Amount</th><th>Status</th><th>Submitted</th></tr></thead><tbody>
<?php if (!$rows): ?><tr><td colspan="7" class="text-center text-secondary py-4">No investment entries yet.</td></tr><?php else: foreach ($rows as $row): ?><tr><td><?= (int)$row['id'] ?></td><td><?= e($row['full_name']) ?></td><td><?= e($row['email']) ?></td><td><?= e($row['currency']) ?></td><td class="text-end"><?= number_format((float)$row['amount'],2) ?></td><td><?= e(ucfirst($row['status'])) ?></td><td><?= e(date('d M Y H:i',strtotime($row['submitted_at']))) ?></td></tr><?php endforeach; endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
