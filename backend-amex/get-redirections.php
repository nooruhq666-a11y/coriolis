<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/storage.php';

$redirections = getRedirections();
echo json_encode(['success' => true, 'redirections' => $redirections]);