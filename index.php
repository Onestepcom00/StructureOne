<?php

/**
 * 
 * *************************************
 * Projet : Mololo plus
 * Nom du fichier : index.php
 * Decsription : IL s'agit du fichier principale qui va gerer les routes d'apis
 * necessaire pour le bon fonctionnement des API system.
 * Date de creation : 27/09/2025
 * Date de modification : 27/09/2025
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP, MySQL, API
 * *************************************
 * 
 */

/**
 * 
 * Require les fichiers de configurations et loader des fonctions 
 * 
 * 
 */
require 'config.php';
require 'loader.php';

/**
 * 
 * Point d'entrée principal
 * 
 * 
 */

/**
 * 
 * Recuperer l'URI et la methode de la requete
 * 
 */
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

/**
 * 
 * Extraire le nom de la route
 * 
 */
$routeName = getRouteName($requestUri, BASE_APP_DIR);

/**
 * 
 * Si aucune route n'est trouvee, afficher un message d'accueil
 * 
 */
if (!$routeName) {
    /**
     * 
     * Reponse par defaut de l'API 
     */
    echo api_response(200, "API System is running", [
        'message' => 'Welcome to the API system',
        'usage' => 'Access routes via /api/{route_name}',
        'example' => '/api/test'
    ]);
}

/**
 * 
 * Charger le fichier env
 * 
 */
loadEnv();

/**
 * 
 * Charger la route demandee
 * 
 */
$loadResult = loadRouteFiles($routeName);

/**
 * 
 * Gerer le resultat du chargement
 * 
 */
if (!$loadResult['success']) {
    /**
     * 
     * Afficher l'erreur si le chargement a echoue
     * 
     */
    echo api_response(404, $loadResult['error'], [
        'requested_route' => $routeName
    ]);
}

/**
 * 
 * Si tout s'est bien passe, la route a ete chargee avec succes
 * Le traitement continue dans les fichiers inclus de la route
 * 
 */

?>