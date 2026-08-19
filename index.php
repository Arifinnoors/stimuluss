<?php
require_once 'includes/auth.php';
header('Location: ' . (!empty($_SESSION['user_id']) ? 'dashboard.php' : 'beranda.php'));
exit;
