<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['clientId'])) {
    echo json_encode(['success' => false, 'error' => 'clientId manquant']);
    exit;
}

$file = __DIR__ . '/redirections.json';

// Charger ou créer le fichier
$redirections = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    $redirections = json_decode($content, true);
    if (!is_array($redirections)) $redirections = [];
}

// Vérifier si clientId existe déjà
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

// Écrire avec vérification
$result = file_put_contents($file, json_encode($redirections, JSON_PRETTY_PRINT));
if ($result === false) {
    echo json_encode(['success' => false, 'error' => 'Impossible d\'écrire dans redirections.json (vérifier permissions)']);
    exit;
}

echo json_encode(['success' => true, 'total' => count($redirections)]);