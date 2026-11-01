<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$cniDir = __DIR__ . '/CNI';
$result = [];

if (file_exists($cniDir)) {
    $dossiers = array_filter(glob($cniDir . '/*'), 'is_dir');
    
    foreach ($dossiers as $dossier) {
        $hash = basename($dossier);
        $recto = $dossier . '/hash-recto.png';
        $verso = $dossier . '/hash-verso.png';
        $dateCreation = filectime($dossier);
        
        if (file_exists($recto) && file_exists($verso)) {
            $result[] = [
                'hash' => $hash,
                'recto' => 'backend-amex/CNI/' . $hash . '/hash-recto.png',
                'verso' => 'backend-amex/CNI/' . $hash . '/hash-verso.png',
                'date' => date('Y-m-d H:i:s', $dateCreation)
            ];
        }
    }
}

echo json_encode(['success' => true, 'fichiers' => $result]);
?>