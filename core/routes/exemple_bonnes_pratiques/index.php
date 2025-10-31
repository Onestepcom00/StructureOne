<?php
/**
 * ====================================================================
 * 📚 EXEMPLE DE BONNES PRATIQUES POUR CRÉER UNE ROUTE
 * ====================================================================
 * 
 * Ce fichier démontre comment créer une route en respectant
 * les conventions de nommage pour éviter les conflits.
 * 
 * ⚠️ IMPORTANT: Évitez d'utiliser les variables réservées du système
 * Consultez config.php pour voir la liste complète des variables réservées.
 * 
 * ✅ Bonnes pratiques de nommage des variables:
 * - Utilisez des noms descriptifs et explicites
 * - Préfixez vos variables par leur contexte (ex: $userInput, $dbResult)
 * - Évitez les noms génériques comme $data, $result, $response
 * 
 * 🚫 N'utilisez JAMAIS de variables préfixées par $_so_ 
 *    (réservées au système StructureOne)
 * 
 */

// ✅ BONNE PRATIQUE: Définir la méthode autorisée en premier
require_method('POST');

try {
    // ✅ NOUVEAU: Accéder aux variables définies dans functions.php
    global $exampleRouteVersion, $exampleRouteAuthor, $exampleRouteMaxAttempts;
    
    // ✅ Logger l'accès à la route
    logMessage("Accès à la route exemple_bonnes_pratiques v{$exampleRouteVersion}", 'INFO');
    
    // ✅ BONNE PRATIQUE: Noms de variables descriptifs et contextualisés
    $userInput = json_decode(file_get_contents('php://input'), true);
    
    // ✅ BONNE PRATIQUE: Validation des entrées
    if (empty($userInput['name']) || empty($userInput['email'])) {
        logMessage("Validation échouée: champs manquants", 'WARNING');
        echo api_response(400, "Les champs 'name' et 'email' sont requis");
        exit;
    }
    
    // ✅ BONNE PRATIQUE: Sécuriser les données
    $safeUserName = db_escape($userInput['name']);
    $safeUserEmail = db_escape($userInput['email']);
    
    // ✅ BONNE PRATIQUE: Utiliser JWT_HTTP_TOKEN via global si besoin d'authentification
    global $JWT_HTTP_TOKEN;
    $tokenPayload = jwt_validate($JWT_HTTP_TOKEN);
    
    if (!$tokenPayload) {
        echo api_response(401, "Token d'authentification invalide");
        exit;
    }
    
    // ✅ BONNE PRATIQUE: Nom de variable explicite pour les résultats DB
    $existingUser = db_find("SELECT * FROM users WHERE email = ?", [$safeUserEmail]);
    
    if ($existingUser) {
        echo api_response(409, "Un utilisateur avec cet email existe déjà");
        exit;
    }
    
    // ✅ BONNE PRATIQUE: Insertion avec nom de variable clair
    $insertionSuccess = db_execute(
        "INSERT INTO users (name, email, created_at) VALUES (?, ?, NOW())",
        [$safeUserName, $safeUserEmail]
    );
    
    if ($insertionSuccess) {
        $newUserId = db_last_id();
        
        // ✅ BONNE PRATIQUE: Préparer la réponse avec un nom explicite
        $apiResponseData = [
            'user_id' => $newUserId,
            'name' => $safeUserName,
            'email' => $safeUserEmail
        ];
        
        echo api_response(201, "Utilisateur créé avec succès", $apiResponseData);
    } else {
        echo api_response(500, "Erreur lors de la création de l'utilisateur");
    }
    
} catch (Exception $exceptionError) {
    // ✅ NOUVEAU: getError() supporte maintenant des codes HTTP et messages personnalisés
    // Exemples selon le type d'erreur:
    
    $errorMessage = $exceptionError->getMessage();
    
    if (strpos($errorMessage, 'existe déjà') !== false) {
        // Erreur de conflit
        echo getError($exceptionError, 409, "Un utilisateur avec cet email existe déjà");
    } elseif (strpos($errorMessage, 'invalide') !== false) {
        // Erreur de validation
        echo getError($exceptionError, 401, "Token d'authentification invalide");
    } else {
        // Erreur générique
        echo getError($exceptionError);
    }
}

/**
 * ====================================================================
 * 🔴 ANTI-PATTERNS À ÉVITER
 * ====================================================================
 * 
 * ❌ N'utilisez PAS ces noms de variables (risque de conflits):
 * 
 * // ❌ MAUVAIS - Variables trop génériques
 * $data = $_POST['data'];
 * $result = db_find(...);
 * $response = [...];
 * 
 * // ❌ MAUVAIS - Variables réservées au système
 * $_so_routeName = "test";
 * $_so_loadResult = [];
 * 
 * // ❌ MAUVAIS - Pas de validation
 * $name = $_POST['name']; // Non sécurisé !
 * 
 * // ❌ MAUVAIS - Pas de gestion d'erreurs
 * $user = db_find(...); // Et si ça échoue ?
 * 
 * ====================================================================
 * ✅ EXEMPLES DE BONS NOMS DE VARIABLES
 * ====================================================================
 * 
 * Pour les données utilisateur:
 * - $userData, $userInput, $userProfile, $userCredentials
 * 
 * Pour les résultats de base de données:
 * - $dbResult, $dbUser, $dbProduct, $queryResult
 * 
 * Pour les réponses API:
 * - $apiResponseData, $responsePayload, $jsonOutput
 * 
 * Pour les validations:
 * - $isValid, $validationErrors, $hasPermission
 * 
 * Pour les calculs/traitements:
 * - $totalAmount, $processedItems, $filteredList
 * 
 */

?>
