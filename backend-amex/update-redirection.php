<?php
header('Content-Type: application/json');
require_once __DIR__ . '/storage.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['clientId']) || empty($data['destinationUrl'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

$redirections = getRedirections();
$found = false;

foreach ($redirections as &$r) {
    if ($r['clientId'] === $data['clientId']) {
        $r['destinationUrl'] = $data['destinationUrl'];
        $r['status'] = 'redirige';
        $found = true;
        break;
    }
}

if ($found && saveRedirections($redirections)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Client non trouvé ou erreur sauvegarde']);
}