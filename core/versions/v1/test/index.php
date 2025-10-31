<?php
/**
 * Route de test versionnée v1
 * 
 * MÉTHODE SIMPLE: Utilisez get() pour récupérer des variables
 */

// Autoriser la methode GET
require_method("GET");

try {
    // Récupérer les variables avec get()
    $hook = get('hook');
    
    // Les fonctions sont automatiquement accessibles
    $message = getHello();
    
    echo api_response(200, "$message and $hook", null);
} catch(Exception $e) {
    // Afficher les erreurs en fonction du debug 
    echo getError($e);
}
?>