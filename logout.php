<?php
require_once 'includes/auth.php';
$_SESSION = [];
session_destroy();
header('Location: beranda.php');
exit;
