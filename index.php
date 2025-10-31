<?php

/**
 * *************************************
 * Projet : PROJECT_NAME
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
 * 
 * Activer l'affichage des erreurs pour le débogage (à désactiver en production)
 * 
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * 
 * Require les fichiers de configurations et loader des fonctions 
 * 
 * 
 */
try {
    require 'config.php';
    require 'loader.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de chargement des fichiers de configuration',
        'error' => $e->getMessage()
    ]);
    exit;
}

/**
 * ------------------------------------------------------------------
 * CHARGER LE FICHIER .ENV EN PREMIER
 * -------------------------------------------------------------------
 * IMPORTANT: Doit être chargé AVANT tout le reste pour que les variables
 * d'environnement soient disponibles (DEBUG_MODE, HOMEPAGE_DISPLAY_HTML, etc.)
 */
loadEnv(); // Exemple : loadEnv("private/.env");

/**
 * Point d'entrée principal
 */

/**
 * Récupérer l'URI et la methode de la requete
 * Note: Préfixe $_so_ pour éviter les conflits avec les variables des routes
 */
$_so_requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$_so_requestMethod = $_SERVER['REQUEST_METHOD'];

/**
 * ANCIENNE MÉTHODE (conservée pour rétrocompatibilité)
 * Extraire le nom de la route
 */
$_so_routeName = getRouteName($_so_requestUri, BASE_APP_DIR);

/**
 * NOUVELLE MÉTHODE (pour le versionning)
 * Détection automatique de la version et de la route
 */
$_so_routeInfo = detectRouteAndVersion($_so_requestUri, BASE_APP_DIR);

/**
 * Si aucune route n'est trouvée, afficher un message d'accueil
 */
if (!$_so_routeName && !$_so_routeInfo) {
    /**
     * Reponse par defaut de l'API 
     * Affiche des informations détaillées uniquement si DEBUG_MODE est activé
     * Affiche une page HTML stylée si HOMEPAGE_DISPLAY_HTML est activé
     */
    $_so_debugMode = (env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true);
    $_so_htmlDisplay = (env('HOMEPAGE_DISPLAY_HTML') === 'true' || env('HOMEPAGE_DISPLAY_HTML') === true);
    
    // Si affichage HTML activé
    if ($_so_htmlDisplay) {
        // Préparer les variables pour la page HTML
        $systemVersion = '2.1.1+ (rétrocompatible)';
        $debugMode = $_so_debugMode;
        $availableVersions = getAvailableVersions();
        
        // Envoyer les headers HTML
        header('Content-Type: text/html; charset=utf-8');
        
        // Inclure la page d'accueil HTML
        include __DIR__ . '/core/homepage.php';
        exit;
    }
    
    // Sinon, affichage JSON classique
    if ($_so_debugMode) {
        // Mode DEBUG : Afficher toutes les informations
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
            'debug_mode' => 'ON',
            'version' => '2.1.1+ (rétrocompatible)'
        ]);
    } else {
        // Mode PRODUCTION : Afficher uniquement les informations essentielles
        echo api_response(200, "API System is running");
    }
    exit;
}

/**
 * ------------------------------------------------------------------
 * NOTE: loadEnv() est maintenant appelé au début du fichier (ligne 57)
 * -------------------------------------------------------------------
 * Si vous avez besoin d'un chemin personnalisé, modifiez la ligne 57 :
 * loadEnv("private/.env");
 */

/***
 * 
 * 
 * -------------------------------------------------------------------------
 * IMPORTANT POUR GERER  COMPOSER 
 * -------------------------------------------------------------------------
 * Cette fonction est tres utile car elle vous permet d'utiliser des dependances 
 * 
 * 
 */
getComposer();

/**
 * CHARGEMENT INTELLIGENT DES ROUTES
 * Priorité au nouveau système de versionning, puis à l'ancien système
 */
$_so_loadResult = null;

// Essayer d'abord le nouveau système de versionning
if ($_so_routeInfo && $_so_routeInfo['routeName']) {
    $_so_loadResult = loadRouteFiles($_so_routeInfo['routeName'], $_so_routeInfo['version'], $_so_routeInfo['basePath']);
} 
// Fallback sur l'ancien système
elseif ($_so_routeName) {
    $_so_loadResult = loadRouteFiles($_so_routeName, 'legacy', 'core/routes');
}

/**
 * Gerer le resultat du chargement
 */
if (!$_so_loadResult || !$_so_loadResult['success']) {
    /**
     * Afficher l'erreur si le chargement a echoue
     */
    $_so_errorData = [
        'requested_route' => $_so_routeName ?: ($_so_routeInfo['routeName'] ?? 'unknown')
    ];
    
    // Ajouter les détails de débogage si DEBUG_MODE est activé
    if (env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true) {
        $_so_errorData['debug'] = [
            'searched_path' => $_so_loadResult['searched_path'] ?? null,
            'available_routes' => getAvailableRoutes($_so_routeInfo['basePath'] ?? 'core/routes'),
            'route_info' => $_so_routeInfo,
            'legacy_route_name' => $_so_routeName
        ];
    }
    
    echo api_response(404, $_so_loadResult['error'] ?? 'Route not found', $_so_errorData);
    exit;
}

/**
 * Si tout s'est bien passe, la route a ete chargee avec succes
 * Le traitement continue dans les fichiers inclus de la route
 */

?>
