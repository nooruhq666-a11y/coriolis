<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$jsonFile = __DIR__ . '/dossier-amex.json';

// GET - Récupérer tous les dossiers
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($jsonFile) && filesize($jsonFile) > 0) {
        $content = file_get_contents($jsonFile);
        $dossiers = json_decode($content, true);
        
        if (!is_array($dossiers)) {
            $dossiers = [];
        }
        
        echo json_encode([
            'success' => true,
            'dossiers' => $dossiers
        ]);
    } else {
        // Créer le fichier s'il n'existe pas
        file_put_contents($jsonFile, json_encode([], JSON_PRETTY_PRINT));
        echo json_encode([
            'success' => true,
            'dossiers' => []
        ]);
    }
    exit;
}

// POST - Ajouter un nouveau dossier ou remplacer la liste (pour suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'error' => 'Données invalides']);
        exit;
    }
    
    // Vérifier si c'est un tableau indexé (pour suppression/remplacement) ou un objet (pour ajout)
    if (isset($input[0]) || empty($input)) {
        // C'est un tableau de dossiers (pour suppression ou mise à jour complète)
        $result = file_put_contents($jsonFile, json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Dossiers mis à jour'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la sauvegarde'
            ]);
        }
    } else {
        // C'est un dossier individuel (pour ajout)
        $dossiers = [];
        if (file_exists($jsonFile)) {
            $content = file_get_contents($jsonFile);
            $dossiers = json_decode($content, true);
            if (!is_array($dossiers)) {
                $dossiers = [];
            }
        }
        
        // Ajouter le nouveau dossier
        $dossiers[] = $input;
        
        // Sauvegarder dans le fichier
        $result = file_put_contents($jsonFile, json_encode($dossiers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        if ($result !== false) {
            echo json_encode([
                'success' => true,
                'message' => 'Dossier sauvegardé',
                'dossier' => $input
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la sauvegarde'
            ]);
        }
    }
    exit;
}

// Méthode non autorisée
echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
?>