<?php
require_once __DIR__ . '/../includes/bootstrap.php';
unset($_SESSION['admin_id'], $_SESSION['admin_name']);
session_regenerate_id(true);
flash('success', 'Administrator signed out.');
redirect('admin/login.php');
