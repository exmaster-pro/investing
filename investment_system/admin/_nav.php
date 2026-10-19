<?php $adminUser = require_admin(); ?>
<div class="admin-head d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
 <div><div class="eyebrow">ADMINISTRATION</div><div class="h4 fw-bold mb-0">Hello, <?= e($adminUser['name']) ?></div></div>
 <div class="d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/dashboard.php')) ?>">Overview</a><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/users.php')) ?>">Users</a><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/investments.php')) ?>">Investments</a><a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/post_news.php')) ?>">Post news</a><a class="btn btn-sm btn-outline-secondary" href="<?= e(app_url('admin/manage_news.php')) ?>">Manage news</a><a class="btn btn-sm btn-outline-danger" href="<?= e(app_url('admin/logout.php')) ?>">Sign out</a></div>
</div>
