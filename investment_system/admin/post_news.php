<?php
require_once __DIR__ . '/../includes/db.php';
$admin = require_admin();
$errors = [];
$title = $body = '';
if (is_post()) {
    verify_csrf();
    $title = trim((string)($_POST['title'] ?? ''));
    $body = trim((string)($_POST['body'] ?? ''));
    $action = (string)($_POST['action'] ?? 'publish');
    if (mb_strlen($title) < 3 || mb_strlen($title) > 180) $errors[] = 'Title must be between 3 and 180 characters.';
    if (mb_strlen($body) < 10 || mb_strlen($body) > 20000) $errors[] = 'Article content must be between 10 and 20,000 characters.';
    if (!in_array($action, ['publish','draft'], true)) $errors[] = 'Invalid publishing action.';
    if (!$errors) {
        $status = $action === 'publish' ? 'published' : 'draft';
        $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;
        $stmt = db()->prepare('INSERT INTO news (admin_id,title,body,status,published_at,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())');
        $stmt->execute([$admin['id'],$title,$body,$status,$publishedAt]);
        flash('success', $status === 'published' ? 'News published and now visible to users.' : 'News saved as a draft.');
        redirect('admin/manage_news.php');
    }
}
$pageTitle = 'Post news';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
<div class="content-card"><h1 class="h4 fw-bold">Publish news</h1><p class="text-secondary">Published articles appear on the user dashboard and the separate News page.</p>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="mb-3"><label class="form-label" for="title">News title</label><input class="form-control" id="title" name="title" maxlength="180" required value="<?= e($title) ?>"></div>
<div class="mb-3"><label class="form-label" for="body">News content</label><textarea class="form-control" id="body" name="body" rows="8" maxlength="20000" required><?= e($body) ?></textarea></div>
<div class="d-flex gap-2"><button class="btn btn-primary" name="action" value="publish">Publish news</button><button class="btn btn-outline-secondary" name="action" value="draft">Save draft</button></div>
</form></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
