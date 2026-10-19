<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';

$localConfig = __DIR__ . '/../config/local.php';
if (is_file($localConfig)) {
    require_once $localConfig;
    if (defined('LOCAL_DB_PASS')) { define('DB_PASS_LOCAL', LOCAL_DB_PASS); }
    if (defined('LOCAL_MAIL_USERNAME')) { define('MAIL_USERNAME_LOCAL', LOCAL_MAIL_USERNAME); }
    if (defined('LOCAL_MAIL_PASSWORD')) { define('MAIL_PASSWORD_LOCAL', LOCAL_MAIL_PASSWORD); }
}
