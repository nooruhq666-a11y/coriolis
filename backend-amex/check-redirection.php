<?php
header('Content-Type: application/json');
require_once __DIR__ . '/storage.php';

$clientId = $_GET['clientId'] ?? '';
if (!$clientId) {
    echo json_encode(['success' => false]);
    exit;
}

$redirections = getRedirections();

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