<?php
require_once __DIR__ . '/../includes/db.php';
require_admin();
$pdo = db();
if (is_post()) {
    verify_csrf();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $action = (string)($_POST['action'] ?? '');
    if (!$id || !in_array($action, ['publish','draft','delete'], true)) {
        flash('danger', 'Invalid news action.');
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM news WHERE id = ?'); $stmt->execute([$id]);
        flash('success', 'News article deleted.');
    } else {
        $status = $action === 'publish' ? 'published' : 'draft';
        if ($status === 'published') {
            $stmt = $pdo->prepare("UPDATE news SET status='published',published_at=COALESCE(published_at,NOW()),updated_at=NOW() WHERE id=?");
        } else {
            $stmt = $pdo->prepare("UPDATE news SET status='draft',updated_at=NOW() WHERE id=?");
        }
        $stmt->execute([$id]); flash('success', 'News status updated.');
    }
    redirect('admin/manage_news.php');
}
$rows = $pdo->query('SELECT n.id,n.title,n.status,n.created_at,n.published_at,a.full_name AS author FROM news n JOIN admins a ON a.id=n.admin_id ORDER BY n.created_at DESC')->fetchAll();
$pageTitle = 'Manage news';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
<section class="content-card"><div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 fw-bold mb-0">Manage news</h1><a class="btn btn-primary btn-sm" href="<?= e(app_url('admin/post_news.php')) ?>">Write article</a></div>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Title</th><th>Author</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead><tbody>
<?php if (!$rows): ?><tr><td colspan="5" class="text-center text-secondary">No news articles yet.</td></tr><?php else: foreach ($rows as $row): ?><tr><td><?= e($row['title']) ?></td><td><?= e($row['author']) ?></td><td><?= e(ucfirst($row['status'])) ?></td><td><?= e(date('d M Y',strtotime($row['created_at']))) ?></td><td><form method="post" class="d-flex gap-1 flex-wrap"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
<?php if ($row['status'] === 'published'): ?><button class="btn btn-sm btn-outline-secondary" name="action" value="draft">Unpublish</button><?php else: ?><button class="btn btn-sm btn-success" name="action" value="publish">Publish</button><?php endif; ?>
<button class="btn btn-sm btn-outline-danger" name="action" value="delete" onclick="return confirm('Delete this article permanently?')">Delete</button></form></td></tr><?php endforeach; endif; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
