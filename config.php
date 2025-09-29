<?php

/**
 * 
 * *************************************
 * Projet : Mololo plus
 * Nom du fichier : config.php
 * Decsription : Il s'agit du fichier de configuration de l'API , ici nous allons mettre toute les configuration
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
 * Configuration de l'API , ici nous allons mettre 
 * les configurations lier a l'API et les methodes autoriser 
 * 
 */
header('Access-Control-Allow-Origin: *'); // Autoriser toutes les origines 
header('Access-Control-Allow-Methods: GET, POST , OPTIONS'); // Autoriser les methodes HTTP
//header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Autoriser les headers specifiques
header('Content-Type: application/json'); // Type de contenu JSON

/**
 * 
 * cette partie du code permet  de recuperer directement et automatiquement les autorisations http 
 * pour valider le token , la variable $JWT_HTTP_TOKEN devra etre utiliser pour verification 
 * 
 */
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$JWT_HTTP_TOKEN = str_replace('Bearer ','',$authHeader);


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
 * 
 */
define('BASE_APP_DIR','/MOLOLO_PLUS/BACKEND'); // Le dossier ou se trouve l'API system


/**
 * 
 * 
 * Cle Secret pour la generation du token 
 * 
 * 
 */
define('API_TOKEN_SECRET','Exemple_key');
define('API_TOKEN_EXP',3600); 


?>