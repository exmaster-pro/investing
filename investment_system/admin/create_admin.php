<?php
/*
 * CLI-only helper. Run from a terminal:
 * php admin/create_admin.php "Administrator Name" "admin@example.com" "a-long-unique-password"
 * Do not run this script through a browser.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this script from the command line only.');
}
require_once __DIR__ . '/../includes/db.php';
$name = trim($argv[1] ?? '');
$email = strtolower(trim($argv[2] ?? ''));
$password = (string)($argv[3] ?? '');
if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 14) {
    fwrite(STDERR, "Usage: php admin/create_admin.php \"Admin Name\" \"admin@example.com\" \"password-at-least-14-chars\"\n");
    exit(1);
}
$stmt = db()->prepare('INSERT INTO admins (full_name,email,password_hash,is_active,created_at) VALUES (?,?,?,1,NOW())');
$stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
fwrite(STDOUT, "Administrator account created for $email\n");
