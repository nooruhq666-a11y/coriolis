<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['hash'])) {
    echo json_encode(['success' => false, 'error' => 'Hash manquant']);
    exit;
}

$hash = $data['hash'];
$dossierPath = __DIR__ . '/CNI/' . $hash;

function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}

if (file_exists($dossierPath)) {
    if (deleteDirectory($dossierPath)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Impossible de supprimer']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dossier non trouvé']);
}
?>