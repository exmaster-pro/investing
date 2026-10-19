<?php
require_once __DIR__ . '/includes/db.php';
require_user();
$articles = db()->query("SELECT title,body,published_at FROM news WHERE status='published' AND published_at <= NOW() ORDER BY published_at DESC")->fetchAll();
$pageTitle = 'News and announcements';
require __DIR__ . '/includes/header.php';
?>
<div class="mb-4"><span class="eyebrow">UPDATES</span><h1 class="h2 fw-bold mt-2">News and announcements</h1><p class="text-secondary">Official updates published by the administrator.</p></div>
<?php if (!$articles): ?><div class="content-card text-secondary">There are no published news articles yet.</div><?php else: ?>
<div class="row g-3"><?php foreach ($articles as $article): ?><div class="col-12"><article class="news-card"><div class="small text-secondary"><?= e(date('d M Y, H:i', strtotime($article['published_at']))) ?></div><h2 class="h4 mt-2"><?= e($article['title']) ?></h2><div><?= nl2br(e($article['body'])) ?></div></article></div><?php endforeach; ?></div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
