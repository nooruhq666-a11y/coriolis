<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dataFile = __DIR__ . '/redirections.json';

if (!file_exists($dataFile)) {
    echo json_encode(['success' => true, 'redirections' => []]);
    exit;
}

$redirections = json_decode(file_get_contents($dataFile), true);

if (!is_array($redirections)) {
    $redirections = [];
}

echo json_encode(['success' => true, 'redirections' => $redirections]);
?>