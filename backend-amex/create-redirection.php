<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/storage.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['clientId'])) {
    echo json_encode(['success' => false, 'error' => 'clientId manquant']);
    exit;
}

$redirections = getRedirections();

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

if (saveRedirections($redirections)) {
    echo json_encode(['success' => true, 'total' => count($redirections)]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur sauvegarde Telegram']);
}