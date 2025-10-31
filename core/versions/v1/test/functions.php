<?php
/**
 * Fichier de fonctions pour la route /api/v1/test
 * 
 * ✅ MÉTHODE SIMPLE: Utilisez set() pour partager des variables
 */

// ✅ Variables partagées avec set()
set('hook', 'test');
set('apiVersion', 'v1');

// ✅ Les fonctions sont automatiquement globales
function getHello() {
    return "Hello from " . get('apiVersion');
}
?>