<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

function user_status_label(string $status): string {
    return match ($status) {
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        default => 'Pending approval',
    };
}
