<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dataFile = __DIR__ . '/redirections.json';
$clientId = $_GET['clientId'] ?? '';

if (!$clientId) {
    echo json_encode(['success' => false, 'error' => 'Client ID manquant']);
    exit;
}

if (!file_exists($dataFile)) {
    echo json_encode(['success' => false, 'destinationUrl' => '']);
    exit;
}

$redirections = json_decode(file_get_contents($dataFile), true);

if (!is_array($redirections)) {
    echo json_encode(['success' => false, 'destinationUrl' => '']);
    exit;
}

foreach ($redirections as $redirection) {
    if ($redirection['clientId'] === $clientId) {
        echo json_encode([
            'success' => true,
            'destinationUrl' => $redirection['destinationUrl'] ?? '',
            'status' => $redirection['status'] ?? 'en_attente'
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'destinationUrl' => '']);
?>