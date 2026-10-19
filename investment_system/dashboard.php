<?php
require_once __DIR__ . '/includes/db.php';
$user = require_user();
$pdo = db();
$stmt = $pdo->prepare('SELECT id,currency,amount,submitted_at,status FROM investments WHERE user_id = ? ORDER BY submitted_at ASC');
$stmt->execute([$user['id']]);
$investments = $stmt->fetchAll();
$news = $pdo->query("SELECT id,title,body,published_at FROM news WHERE status='published' AND published_at <= NOW() ORDER BY published_at DESC LIMIT 4")->fetchAll();
$currencies = ['KES'=>'Kenyan shilling','USD'=>'US dollar','EUR'=>'Euro','GBP'=>'Pound sterling'];
$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
 <div><span class="eyebrow">USER DASHBOARD</span><h1 class="h2 fw-bold mt-2">Hello, <?= e($user['name']) ?></h1><p class="text-secondary mb-0">Record entries and explore your submitted history.</p></div>
 <a class="btn btn-outline-primary" href="<?= e(app_url('news.php')) ?>">View all news</a>
</div>
<div class="row g-3 mb-4">
 <div class="col-md-4"><div class="metric-card"><div class="metric-label">Submitted entries</div><div class="metric-value"><?= count($investments) ?></div></div></div>
 <div class="col-md-4"><div class="metric-card"><div class="metric-label">Currencies used</div><div class="metric-value"><?= count(array_unique(array_column($investments, 'currency'))) ?></div></div></div>
 <div class="col-md-4"><div class="metric-card"><div class="metric-label">Latest entry</div><div class="metric-value metric-date"><?= $investments ? e(date('d M Y', strtotime(end($investments)['submitted_at']))) : '—' ?></div></div></div>
</div>
<div class="row g-4">
 <div class="col-lg-5"><section class="content-card h-100">
  <h2 class="h5 fw-bold">Submit an investment entry</h2>
  <p class="text-secondary small">This demo records an amount; it does not transfer money or generate real investment returns.</p>
  <form method="post" action="<?= e(app_url('investment_submit.php')) ?>">
   <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
   <div class="mb-3"><label class="form-label" for="currency">Currency</label><select class="form-select" name="currency" id="currency" required><?php foreach ($currencies as $code=>$label): ?><option value="<?= e($code) ?>"><?= e($code . ' — ' . $label) ?></option><?php endforeach; ?></select></div>
   <div class="mb-3"><label class="form-label" for="amount">Amount</label><input class="form-control" id="amount" type="number" name="amount" min="0.01" max="999999999999" step="0.01" required placeholder="e.g. 5000.00"></div>
   <button class="btn btn-primary w-100" type="submit">Submit entry</button>
  </form>
 </section></div>
 <div class="col-lg-7"><section class="content-card">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"><h2 class="h5 fw-bold mb-0">Investment history graph</h2>
   <div class="d-flex gap-2"><select id="chartCurrency" class="form-select form-select-sm" aria-label="Currency filter"><option value="ALL">All currencies</option><?php foreach ($currencies as $code=>$label): ?><option value="<?= e($code) ?>"><?= e($code) ?></option><?php endforeach; ?></select>
   <select id="chartRange" class="form-select form-select-sm" aria-label="Graph timeframe"><option value="7">7 days</option><option value="30" selected>30 days</option><option value="90">3 months</option><option value="180">6 months</option><option value="365">1 year</option><option value="all">All time</option></select></div>
  </div>
  <div class="chart-wrap"><canvas id="investmentChart" aria-label="Investment entries by date" role="img"></canvas></div>
  <p class="form-text mt-2">The chart shows submitted amounts by date. Different currencies are kept as separate series and are not converted.</p>
 </section></div>
</div>
<section class="content-card mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 fw-bold mb-0">Recent submissions</h2><span class="text-secondary small"><?= count($investments) ?> total</span></div>
 <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Date</th><th>Currency</th><th class="text-end">Amount</th><th>Status</th></tr></thead><tbody>
 <?php if (!$investments): ?><tr><td colspan="4" class="text-center text-secondary py-4">No entries yet. Submit your first entry above.</td></tr><?php else: foreach (array_reverse(array_slice($investments, -8)) as $entry): ?>
 <tr><td><?= e(date('d M Y, H:i', strtotime($entry['submitted_at']))) ?></td><td><?= e($entry['currency']) ?></td><td class="text-end"><?= number_format((float)$entry['amount'], 2) ?></td><td><span class="badge text-bg-secondary"><?= e(ucfirst($entry['status'])) ?></span></td></tr>
 <?php endforeach; endif; ?>
 </tbody></table></div>
</section>
<section class="mt-4"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 fw-bold mb-0">Latest news</h2><a href="<?= e(app_url('news.php')) ?>">See all</a></div>
 <div class="row g-3"><?php if (!$news): ?><div class="col-12"><div class="info-card">No news has been published yet.</div></div><?php else: foreach ($news as $article): ?>
 <div class="col-md-6"><article class="news-card h-100"><div class="small text-secondary"><?= e(date('d M Y', strtotime($article['published_at']))) ?></div><h3 class="h5 mt-2"><?= e($article['title']) ?></h3><p class="mb-0"><?= nl2br(e(mb_strimwidth($article['body'], 0, 240, '…'))) ?></p></article></div>
 <?php endforeach; endif; ?></div>
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>window.INVESTMENT_DATA = <?= json_encode(array_map(fn($r)=>['date'=>date('Y-m-d',strtotime($r['submitted_at'])),'currency'=>$r['currency'],'amount'=>(float)$r['amount']],$investments), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;</script>
<script src="<?= e(app_url('assets/js/dashboard.js')) ?>"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
