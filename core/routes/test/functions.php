<?php
/**
 * Fichier de fonctions pour la route /api/test
 * 
 * ✅ MÉTHODE SIMPLE: Utilisez set() pour partager des variables
 */

// ✅ Variables partagées avec set()
set('name', 'structureOne');
set('version', '2.1.1+');
set('config', [
    'timeout' => 30,
    'retries' => 3
]);

// ✅ Les fonctions sont automatiquement globales
function testRoute_getVersion() {
    return get('version');
}
?>