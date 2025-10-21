<?php

/**
 * 
 * *************************************
 * Projet : PROJECT_NAME
 * Nom du fichier : config.php
 * Decsription : Il s'agit du fichier de configuration de l'API , ici nous allons mettre toute les configuration
 * necessaire pour le bon fonctionnement des API system.
 * Date de creation : 27/09/2025
 * Date de modification : 27/09/2025
 * version : PROJECT_VERSION
 * Auteur : Exaustan Malka
 * Stacks : PHP, MySQL, API
 * *************************************
 * 
 */

/**
 * 
 * Configuration de l'API , ici nous allons mettre 
 * les configurations lier a l'API et les methodes autoriser 
 * 
 */
header('Access-Control-Allow-Origin: *'); // Autoriser toutes les origines 
header('Access-Control-Allow-Methods: GET, POST , OPTIONS'); // Autoriser les methodes HTTP
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Autoriser les headers specifiques
header('Content-Type: application/json;charset=utf-8'); // Type de contenu JSON


/**
 * 
 * 
 * Récupération ROBUSTE du token JWT - Version compatible tous serveurs
 * 
 * 
 */
function getAuthToken() {
    // Méthode 1: Header standard
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    
    // Méthode 2: Header alternatif (pour certains serveurs)
    if (empty($authHeader)) {
        $authHeader = $_SERVER['Authorization'] ?? '';
    }
    
    // Méthode 3: Header REDIRECT_HTTP_AUTHORIZATION (pour certains hébergements)
    if (empty($authHeader)) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    }
    
    // Méthode 4: Récupération depuis getallheaders() si disponible
    if (empty($authHeader) && function_exists('getallheaders')) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    
    // Méthode 5: Récupération alternative pour serveurs sans getallheaders()
    if (empty($authHeader)) {
        // Méthode de secours pour récupérer les headers
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerKey = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                if ($headerKey === 'Authorization') {
                    $authHeader = $value;
                    break;
                }
            }
        }
    }
    
    // Extraction du token
    if (!empty($authHeader)) {
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        } else {
            return trim(str_replace('Bearer', '', $authHeader));
        }
    }
    
    return '';
}

/**
 * 
 * Fonction polyfill pour getallheaders() si elle n'existe pas
 * 
 */
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

/**
 * 
 * 
 * Récupération automatique du token JWT
 * 
 * 
 */
$GLOBALS['JWT_HTTP_TOKEN'] = getAuthToken();

/**
 * 
 * 
 * Configuration du gestionnaire de routeur 
 * 
 * 
 */
define('BASE_ROUTEUR','/core/routes'); // Le dossier qui va contenir les routes 
define('BASE_DATA','/core/database'); // Le dossier qui va contenir les bases de donnees 
define('BASE_UPLOADS','/core/uploads'); // Le dossier qui va contenir les fichiers uploades
define('BASE_CACHE','/core/cache'); // Le dossier qui va contenir les fichiers caches
define('BASE_LOGS','/core/logs'); // Le dossier qui va contenir les fichiers logs

/**
 * 
 * Mettre le chemin ou se trouve le dossier de l'API
 * IMPORTANT: Sur InfinityFree, le chemin est souvent relatif
 * 
 */
// Chemin absolu pour les inclusions de fichiers
define('BASE_PATH', __DIR__);

// Chemin relatif pour les routes web
define('BASE_APP_DIR',''); // Laisser vide pour InfinityFree

/**
 * 
 * 
 * Cle Secret pour la generation du token 
 * 
 * 
 */
define('API_TOKEN_SECRET','Exemple_key');
define('API_TOKEN_EXP',2592000); 

?>
