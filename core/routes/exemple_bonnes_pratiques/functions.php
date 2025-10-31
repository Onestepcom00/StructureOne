<?php
/**
 * ====================================================================
 * 📚 EXEMPLE: Fichier de fonctions pour une route
 * ====================================================================
 * 
 * Ce fichier est automatiquement chargé AVANT index.php
 * Utilisez-le pour définir des fonctions spécifiques à cette route
 * 
 * ✅ BONNE PRATIQUE: Préfixez vos fonctions pour éviter les conflits
 * Exemples: userRoute_validateEmail(), productRoute_calculatePrice()
 * 
 * ✅ NOUVEAU: Les variables définies ici sont accessibles dans index.php
 * Utilisez 'global $nomVariable' dans index.php pour y accéder
 * 
 */

// ✅ Variables de configuration de la route (accessibles via 'global' dans index.php)
$exampleRouteVersion = "1.0.0";
$exampleRouteAuthor = "Votre Nom";
$exampleRouteMaxAttempts = 3;

/**
 * ✅ BONNE PRATIQUE: Nom de fonction préfixé par le contexte
 * Exemple: exampleRoute_validateUserData()
 * 
 * @param array $inputData Les données à valider
 * @return array Tableau avec 'valid' (bool) et 'errors' (array)
 */
function exampleRoute_validateUserData($inputData) {
    $validationResult = [
        'valid' => true,
        'errors' => []
    ];
    
    // Validation du nom
    if (empty($inputData['name']) || strlen($inputData['name']) < 3) {
        $validationResult['valid'] = false;
        $validationResult['errors'][] = "Le nom doit contenir au moins 3 caractères";
    }
    
    // Validation de l'email
    if (empty($inputData['email']) || !filter_var($inputData['email'], FILTER_VALIDATE_EMAIL)) {
        $validationResult['valid'] = false;
        $validationResult['errors'][] = "Email invalide";
    }
    
    return $validationResult;
}

/**
 * ✅ BONNE PRATIQUE: Fonction utilitaire avec nom explicite
 * 
 * @param string $userEmail Email à formater
 * @return string Email en minuscules et trimé
 */
function exampleRoute_formatEmail($userEmail) {
    return strtolower(trim($userEmail));
}

/**
 * ✅ BONNE PRATIQUE: Fonction pour générer des réponses standardisées
 * 
 * @param array $userData Données utilisateur
 * @return array Réponse formatée
 */
function exampleRoute_formatUserResponse($userData) {
    return [
        'id' => $userData['id'] ?? null,
        'name' => $userData['name'] ?? '',
        'email' => $userData['email'] ?? '',
        'created_at' => $userData['created_at'] ?? date('Y-m-d H:i:s')
    ];
}

/**
 * ====================================================================
 * 🔴 ANTI-PATTERNS À ÉVITER DANS LES FONCTIONS
 * ====================================================================
 * 
 * ❌ MAUVAIS - Fonction avec nom trop générique:
 * function validate($data) { ... }
 * 
 * ❌ MAUVAIS - Fonction qui modifie des variables globales:
 * function setUser() {
 *     global $_so_routeName; // NE JAMAIS TOUCHER AUX VARIABLES SYSTÈME !
 *     $_so_routeName = "test";
 * }
 * 
 * ❌ MAUVAIS - Fonction sans préfixe (risque de conflit):
 * function formatEmail($email) { ... }
 * 
 * ✅ BON - Fonction avec préfixe contexte:
 * function userRoute_formatEmail($email) { ... }
 * 
 */

?>
