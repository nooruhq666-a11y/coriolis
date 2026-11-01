<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['clientId'])) {
    echo json_encode(['success' => false, 'error' => 'clientId manquant']);
    exit;
}

$file = __DIR__ . '/redirections.json';
$redirections = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($redirections)) $redirections = [];

// Vérifier si existe déjà
$exists = false;
foreach ($redirections as &$r) {
    if ($r['clientId'] === $data['clientId']) {
        $r['sourceUrl'] = $data['sourceUrl'] ?? '/facture.html';
        $r['type'] = $data['type'] ?? 'facture';
        $r['status'] = 'en_attente';
        $exists = true;
        break;
    }
}

if (!$exists) {
    $redirections[] = [
        'clientId' => $data['clientId'],
        'sourceUrl' => $data['sourceUrl'] ?? '/facture.html',
        'destinationUrl' => '',
        'type' => $data['type'] ?? 'facture',
        'status' => 'en_attente',
        'date' => date('Y-m-d H:i:s')
    ];
}

file_put_contents($file, json_encode($redirections, JSON_PRETTY_PRINT));
echo json_encode(['success' => true]);