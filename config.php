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
 * ===========================================================
 * 🔐 CONFIGURATION DES EN-TÊTES HTTP (HEADERS DE SÉCURITÉ)
 * ===========================================================
 * Ces headers protègent ton API contre diverses attaques :
 * - XSS (cross-site scripting)
 * - Clickjacking
 * - MIME sniffing
 * - Vol de tokens via CORS mal configuré
 * - Cache non contrôlé
 */

/**
 * 🌍 CORS (Cross-Origin Resource Sharing)
 * Autoriser les domaines et méthodes spécifiques.
 * ⚠️ En production, évite le "*" et remplace-le par ton domaine :
 *     ex: header('Access-Control-Allow-Origin: https://baziks.media');
 */
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With'); 

/**
 * 📦 Type de contenu par défaut
 */
header('Content-Type: application/json; charset=utf-8'); 

/**
 * 🧱 Protection contre le sniffing du contenu
 * (Empêche le navigateur d'interpréter un fichier JSON comme du HTML)
 */
header('X-Content-Type-Options: nosniff'); 

/**
 * 🛡️ Protection contre le clickjacking
 * (Empêche le chargement de ton API dans une iframe externe)
 */
header('X-Frame-Options: DENY'); 

/**
 * 🚫 Protection XSS de base (ancien mécanisme IE/Chrome)
 * ⚠️ Optionnel car obsolète sur navigateurs modernes, mais sans danger
 */
header('X-XSS-Protection: 1; mode=block'); // optionnel

/**
 * 🔏 Politique stricte du cache
 * (Empêche les navigateurs/proxy de stocker des réponses sensibles)
 */
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache'); 
header('Expires: 0'); 

/**
 * 🧩 Optionnel : Cross-Origin Resource Policy (CORP)
 * Renforce la séparation des ressources entre origines.
 * À activer uniquement si ton API est utilisée par des frontends sûrs.
 */
// header('Cross-Origin-Resource-Policy: same-origin'); 

/**
 * 🧩 Optionnel : Cross-Origin-Embedder & Opener Policy (COEP/COOP)
 * Utiles pour les apps web modernes (WebAssembly, SharedArrayBuffer)
 */
// header('Cross-Origin-Opener-Policy: same-origin');
// header('Cross-Origin-Embedder-Policy: require-corp');

/**
 * ✅ Gérer les requêtes OPTIONS (prévol CORS)
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
