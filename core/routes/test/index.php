<?php
/**
 * Ceci est un fichier d'API d'exemple, soyez libre de le tester
 * 
 * ✅ MÉTHODE SIMPLE: Utilisez get() pour récupérer des variables
 */

// Autoriser la methode GET
require_method("GET");

try {
    // ✅ Récupérer les variables avec get()
    $name = get('name');
    $config = get('config');
    
    // ✅ Les fonctions sont automatiquement accessibles
    $version = testRoute_getVersion();
    
    echo api_response(200, "L'API fonctionne bien ! Projet: $name, Version: $version", [
        'config' => $config
    ]);
} catch(Exception $e) {
    // Afficher les erreurs en fonction du debug 
    echo getError($e);
}
?>