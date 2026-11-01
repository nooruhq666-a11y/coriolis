<?php
header('Content-Type: application/json');
$clientId = $_GET['clientId'] ?? '';
if (!$clientId) {
    echo json_encode(['success' => false]);
    exit;
}

$file = __DIR__ . '/redirections.json';
$redirections = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

foreach ($redirections as $r) {
    if ($r['clientId'] === $clientId) {
        echo json_encode([
            'success' => true,
            'status' => $r['status'],
            'destinationUrl' => $r['destinationUrl']
        ]);
        exit;
    }
}
echo json_encode(['success' => false]);