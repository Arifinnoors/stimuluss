<?php
// AJAX endpoint — update language session without page reload
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/translations.php';

$lang = $_POST['lang'] ?? $_GET['lang'] ?? '';
if (in_array($lang, ['id', 'en'])) {
    set_language($lang);
    echo json_encode(['ok' => true, 'lang' => $lang]);
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid language']);
}
