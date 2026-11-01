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
        $typeFile = $dossier . '/type.txt';
        $dateCreation = filectime($dossier);
        
        if (file_exists($recto) && file_exists($verso)) {
            // Type par défaut = Carte d'identité, sauf si type.txt dit autre chose
            $type = 'Carte d\'identité';
            if (file_exists($typeFile)) {
                $typeContent = trim(file_get_contents($typeFile));
                if (!empty($typeContent)) {
                    $type = $typeContent;
                }
            }
            
            $result[] = [
                'hash' => $hash,
                'type' => $type,
                'recto' => 'backend-amex/CNI/' . $hash . '/hash-recto.png',
                'verso' => 'backend-amex/CNI/' . $hash . '/hash-verso.png',
                'date' => date('Y-m-d H:i:s', $dateCreation)
            ];
        }
    }
}

echo json_encode(['success' => true, 'fichiers' => $result]);
?>