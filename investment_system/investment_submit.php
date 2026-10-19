<?php
require_once __DIR__ . '/includes/db.php';
$user = require_user();
if (!is_post()) { http_response_code(405); exit('Method not allowed'); }
verify_csrf();
$currency = strtoupper(trim((string)($_POST['currency'] ?? '')));
$amountText = trim((string)($_POST['amount'] ?? ''));
$allowed = ['KES','USD','EUR','GBP'];
if (!in_array($currency, $allowed, true) || !is_numeric($amountText)) {
    flash('danger', 'Choose a supported currency and enter a valid amount.');
    redirect('dashboard.php');
}
$amount = (float)$amountText;
if (!is_finite($amount) || $amount <= 0 || $amount > 999999999999) {
    flash('danger', 'Amount must be greater than zero and within the allowed limit.');
    redirect('dashboard.php');
}
$stmt = db()->prepare("INSERT INTO investments (user_id,currency,amount,status,submitted_at) VALUES (?,?,?,'submitted',NOW())");
$stmt->execute([$user['id'], $currency, number_format($amount, 2, '.', '')]);
flash('success', 'Your investment entry has been recorded.');
redirect('dashboard.php');
