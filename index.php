<?php

/**
 * *************************************
 * Projet : Mololo plus
 * Nom du fichier : index.php
 * Description : IL s'agit du fichier principale qui va gerer les routes d'apis
 * necessaire pour le bon fonctionnement des API system.
 * Date de creation : 27/09/2025
 * Date de modification : 27/09/2025
 * version : 2.0 (Rétrocompatible)
 * Auteur : Exaustan Malka
 * Stacks : PHP, MySQL, API
 * *************************************
 * 
 * NOUVEAUTÉS v2.0 :
 * - Support du versionning des APIs (/api/v1/route, /api/v2/route)
 * - Gestion d'erreurs globale avec DEBUG_MODE
 * - Rétrocompatibilité totale avec l'ancien système
 */

/**
 * Require les fichiers de configurations et loader des fonctions 
 */
require 'config.php';
require 'loader.php';

/**
 * Point d'entrée principal
 */

/**
 * Récupérer l'URI et la methode de la requete
 */
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

/**
 * ANCIENNE MÉTHODE (conservée pour rétrocompatibilité)
 * Extraire le nom de la route
 */
$routeName = getRouteName($requestUri, BASE_APP_DIR);

/**
 * NOUVELLE MÉTHODE (pour le versionning)
 * Détection automatique de la version et de la route
 */
$routeInfo = detectRouteAndVersion($requestUri, BASE_APP_DIR);

/**
 * Si aucune route n'est trouvée, afficher un message d'accueil
 */
if (!$routeName && !$routeInfo) {
    /**
     * Reponse par defaut de l'API 
     */
    echo api_response(200, "API System is running", [
        'message' => 'Welcome to the API system',
        'usage' => [
            'legacy' => 'Access routes via /api/{route_name}',
            'versioned' => 'Access routes via /api/v{version}/{route_name}',
            'examples' => [
                '/api/test',
                '/api/v1/test', 
                '/api/v2/users'
            ]
        ],
        'available_versions' => getAvailableVersions(),
        'debug_mode' => env('DEBUG_MODE') ? 'ON' : 'OFF',
        'version' => '2.0 (rétrocompatible)'
    ]);
    exit;
}

/**
 * Charger le fichier env
 */
loadEnv();

/**
 * CHARGEMENT INTELLIGENT DES ROUTES
 * Priorité au nouveau système de versionning, puis à l'ancien système
 */
$loadResult = null;

// Essayer d'abord le nouveau système de versionning
if ($routeInfo && $routeInfo['routeName']) {
    $loadResult = loadRouteFiles($routeInfo['routeName'], $routeInfo['version'], $routeInfo['basePath']);
} 
// Fallback sur l'ancien système
elseif ($routeName) {
    $loadResult = loadRouteFiles($routeName, 'legacy', 'core/routes');
}

/**
 * Gerer le resultat du chargement
 */
if (!$loadResult || !$loadResult['success']) {
    /**
     * Afficher l'erreur si le chargement a echoue
     */
    $errorData = [
        'requested_route' => $routeName ?: ($routeInfo['routeName'] ?? 'unknown')
    ];
    
    // Ajouter les détails de débogage si DEBUG_MODE est activé
    if (env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true) {
        $errorData['debug'] = [
            'searched_path' => $loadResult['searched_path'] ?? null,
            'available_routes' => getAvailableRoutes($routeInfo['basePath'] ?? 'core/routes'),
            'route_info' => $routeInfo,
            'legacy_route_name' => $routeName
        ];
    }
    
    echo api_response(404, $loadResult['error'] ?? 'Route not found', $errorData);
    exit;
}

/**
 * Si tout s'est bien passe, la route a ete chargee avec succes
 * Le traitement continue dans les fichiers inclus de la route
 */

?>