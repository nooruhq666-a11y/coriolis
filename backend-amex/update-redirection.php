<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$dataFile = __DIR__ . '/redirections.json';
$postData = json_decode(file_get_contents('php://input'), true);

if (!$postData || !isset($postData['clientId']) || !isset($postData['destinationUrl'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

if (!file_exists($dataFile)) {
    echo json_encode(['success' => false, 'error' => 'Aucune redirection trouvée']);
    exit;
}

$redirections = json_decode(file_get_contents($dataFile), true);
$found = false;

foreach ($redirections as &$redirection) {
    if ($redirection['clientId'] === $postData['clientId']) {
        $redirection['destinationUrl'] = $postData['destinationUrl'];
        $redirection['status'] = 'redirige';
        $found = true;
        break;
    }
}

if ($found && file_put_contents($dataFile, json_encode($redirections, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Client non trouvé ou erreur sauvegarde']);
}
?>