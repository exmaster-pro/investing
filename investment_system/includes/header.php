<?php
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $pageTitle ?? APP_NAME;
$flashMessage = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · <?= e(APP_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= e(app_url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-nav">
 <div class="container">
  <a class="navbar-brand fw-bold" href="<?= e(app_url('index.php')) ?>"><?= e(APP_NAME) ?></a>
  <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="navMain">
   <div class="navbar-nav ms-auto">
    <?php if (!empty($_SESSION['user_id'])): ?>
      <a class="nav-link" href="<?= e(app_url('dashboard.php')) ?>">Dashboard</a>
      <a class="nav-link" href="<?= e(app_url('news.php')) ?>">News</a>
      <a class="nav-link" href="<?= e(app_url('logout.php')) ?>">Sign out</a>
    <?php else: ?>
      <a class="nav-link" href="<?= e(app_url('login.php')) ?>">Sign in</a>
      <a class="nav-link" href="<?= e(app_url('register.php')) ?>">Register</a>
    <?php endif; ?>
    <a class="nav-link" href="<?= e(app_url('admin/login.php')) ?>">Admin</a>
   </div>
  </div>
 </div>
</nav>
<main class="container py-4">
<?php if ($flashMessage): ?>
<div class="alert alert-<?= e($flashMessage['type']) ?> alert-dismissible fade show" role="alert">
 <?= e($flashMessage['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
