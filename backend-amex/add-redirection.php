<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dataFile = __DIR__ . '/redirections.json';

// Récupérer les données POST
$postData = json_decode(file_get_contents('php://input'), true);

if (!$postData || !isset($postData['clientId']) || !isset($postData['sourceUrl'])) {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
    exit;
}

// Charger les redirections existantes
$redirections = [];
if (file_exists($dataFile)) {
    $redirections = json_decode(file_get_contents($dataFile), true);
    if (!is_array($redirections)) {
        $redirections = [];
    }
}

// Ajouter la nouvelle redirection
$newRedirection = [
    'clientId' => $postData['clientId'],
    'sourceUrl' => $postData['sourceUrl'],
    'destinationUrl' => '',
    'status' => $postData['status'] ?? 'en_attente',
    'createdAt' => date('Y-m-d H:i:s')
];

$redirections[] = $newRedirection;

// Sauvegarder
if (file_put_contents($dataFile, json_encode($redirections, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur sauvegarde']);
}
?>