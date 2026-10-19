<?php
require_once __DIR__ . '/includes/db.php';
if (!empty($_SESSION['user_id'])) redirect('dashboard.php');
$pageTitle = 'Welcome';
require __DIR__ . '/includes/header.php';
?>
<section class="hero p-4 p-lg-5 rounded-4">
 <div class="row align-items-center g-4">
  <div class="col-lg-7">
   <span class="eyebrow">YOUR FINANCIAL OVERVIEW</span>
   <h1 class="display-5 fw-bold mt-3">Track your investment activity in one place.</h1>
   <p class="lead mt-3">Submit investment entries, view your recorded history, and keep up with announcements from the administrator.</p>
   <div class="d-flex gap-2 flex-wrap mt-4"><a class="btn btn-light btn-lg" href="<?= e(app_url('register.php')) ?>">Create account</a><a class="btn btn-outline-light btn-lg" href="<?= e(app_url('login.php')) ?>">Sign in</a></div>
  </div>
  <div class="col-lg-5"><div class="hero-stat"><div class="small opacity-75">Your workspace</div><div class="display-6 fw-bold my-2">Simple. Secure. Clear.</div><div class="opacity-75">Email verification · Admin approval · History charts</div></div></div>
 </div>
</section>
<div class="row g-3 mt-3">
 <div class="col-md-4"><div class="info-card h-100"><h3>Verified access</h3><p>Confirm your email before your account is reviewed.</p></div></div>
 <div class="col-md-4"><div class="info-card h-100"><h3>Investment history</h3><p>Record amounts and view submitted entries over a chosen timeframe.</p></div></div>
 <div class="col-md-4"><div class="info-card h-100"><h3>Latest news</h3><p>Read updates published by the administrator after signing in.</p></div></div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
