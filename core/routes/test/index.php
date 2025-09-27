<?php

/**
 * Route de test - Exemple d'implémentation
 */

// Inclure les fonctions spécifiques à cette route


// Exemple de traitement de requête
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = test_function();
    echo api_response(200, "Route test fonctionne", [
        'message' => $result,
        'timestamp' => date('Y-m-d H:i:s'),
        'host' => env('DB_HOST')
    ]);
} else {
    echo api_response(405, "Méthode non autorisée");
}

?>
