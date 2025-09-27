<?php

/**
 * 
 * *************************************
 * Projet : Mololo plus
 * Nom du fichier : loader.php
 * Decsription : Ce fichier va contenir toute les functions utiles pour le fonctionnement de l'API , ses fonctions ne vont pas dependre des differentes routes , c'est des fonctions accessibles a toutes les routes de l'API.
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
 * Function pour afficher la reponses de l'API 
 * 
 */
function api_response($status,$message = null,$data = null){
   
    /**
     * 
     * Creer un tableau des reponses de l'API , chaque status equivaut a une reponses de l'API 
     * 
     */
    $_response = [
        200 => ["success",$message ?? "Request was successful."],
        404 => ["error",$message ?? "The requested resource was not found."],
        400 => ["error",$message ?? "Bad request. Please check your input."],
        500 => ["error",$message ?? "Internal server error. Please try again later."],
        401 => ["error",$message ?? "Unauthorized access. Please provide valid credentials."],
        403 => ["error",$message ?? "Forbidden access. You do not have permission to access this resource."],
        422 => ["error",$message ?? "Unprocessable entity. The request was well-formed but was unable to be followed due to semantic errors."],
        429 => ["error",$message ?? "Too many requests. Please slow down your request rate."],
        503 => ["error",$message ?? "Service unavailable. The server is currently unable to handle the request due to temporary overload or maintenance."]
    ];

    /**
     * 
     * verifier su le status est valide , et creer un tableau de reponse correspondant a la reponse 
     * 
     */
    if(array_key_exists($status,$_response)){
        $response = [
            "status" => $_response[$status][0],
            "message" => $_response[$status][1]
        ];
    }
    
    /**
     * 
     * Verifier si les donnees *data* ne sont pas null et les ajouter a la reponse
     * 
     */
    if($data !== null){
        /**
         * Ajouter les donnees a la reponse , sachant bien que *data* sera un tableau 
         * 
         */
        $response += $data;
    }

    /**
     * 
     * Mettre le bon status de la reponse au header 
     * 
     */
    http_response_code($status);
    /**
     * 
     * Renvoyer la reponse de l'API au format JSON 
     * 
     */
    return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit; // stopper l'execution du script
}




/**
 * 
 * Fonction simple pour extraire le nom de la route
 * 
 * 
 */
function getRouteName($uri, $basePath) {
    /**
     * 
     * Retirer le chemin de base de l'URI si nécessaire
     * 
     */
    if (!empty($basePath) && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
    
    /**
     * 
     * Nettoyer l'URI
     * 
     */
    $uri = rtrim($uri, '/');
    
    /**
     * 
     * Extraire le nom de la route après /api/
     * 
     */
    if (preg_match('#^/api/([^/]+)#', $uri, $matches)) {
        return $matches[1];
    }
    
    return null;
}

/**
 * 
 * Fonction simple pour charger une route
 * 
 * 
 */
function loadRouteFiles($routeName) {
    /**
     * 
     * Obtenir le chemin absolu du script courant
     * 
     */
    $baseDir = dirname(__FILE__);
    
    /**
     * 
     * Construire le chemin ABSOLU vers le dossier de la route
     * 
     */
    $routeDir = $baseDir . BASE_ROUTEUR . "/{$routeName}";
    $appFile = $routeDir . "/index.php";
    $functionFile = $routeDir . "/functions.php";
    
    /**
     * 
     * Debug: Afficher les chemins pour verification
     * 
     */
    /*
    error_log("Base dir: " . $baseDir);
    error_log("Route dir: " . $routeDir);
    error_log("App file: " . $appFile);
    error_log("Function file: " . $functionFile);
    error_log("Directory exists: " . (is_dir($routeDir) ? 'yes' : 'no'));
    */
    
    /**
     * 
     * Verifier si le dossier existe
     * 
     */
    if (!is_dir($routeDir)) {
        return [
            'success' => false,
            'error' => "Route directory '{$routeDir}' not found"
        ];
    }
    
    /**
     * 
     * Verifier si les fichiers existent
     * 
     */
    if (!file_exists($appFile)) {
        return [
            'success' => false,
            'error' => "Index file for route '{$routeName}' not found at: {$appFile}"
        ];
    }
    
    if (!file_exists($functionFile)) {
        return [
            'success' => false,
            'error' => "Functions file for route '{$routeName}' not found at: {$functionFile}"
        ];
    }
    
    /**
     * 
     * Charger les fichiers
     * 
     */
    try {
        require $functionFile;
        require $appFile;
        
        return ['success' => true];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => "Error loading route: " . $e->getMessage()
        ];
    }
}

/**
 * 
 * Fonction pour charger les variables d'environnement depuis le fichier .env
 * 
 * @param string $envPath Chemin vers le fichier .env
 * @return bool True si le chargement a reussi, false sinon
 * 
 */
function loadEnv($envPath = '.env') {
    /**
     * 
     * Verifier si le fichier .env existe
     * 
     */
    if (!file_exists($envPath)) {
        error_log("Fichier .env non trouvé: " . $envPath);
        return false;
    }
    
    /**
     * 
     * Lire le contenu du fichier .env
     * 
     */
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lines === false) {
        error_log("Impossible de lire le fichier .env");
        return false;
    }
    
    /**
     * 
     * Parcourir chaque ligne du fichier
     * 
     */
    foreach ($lines as $line) {
        /**
         * 
         * Ignorer les lignes de commentaire (commençant par #)
         * 
         */
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        /**
         * 
         * Separer la cle de la valeur
         * 
         */
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            
            /**
             * 
             * Nettoyer la cle et la valeur
             * 
             */
            $key = trim($key);
            $value = trim($value);
            
            /**
             * 
             * Gerer les valeurs entre guillemets
             * 
             */
            if (preg_match('/^"(.+)"$/', $value, $matches) || preg_match("/^'(.+)'$/", $value, $matches)) {
                $value = $matches[1];
            }
            
            /**
             * 
             * Definir la variable d'environnement si elle n'existe pas deja
             * 
             */
            if (!empty($key) && !array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    return true;
}

/**
 * 
 * Fonction pour obtenir une variable d'environnement
 * 
 * @param string $key Cle de la variable
 * @param mixed $defaultValue Valeur par defaut si la variable n'existe pas
 * @return mixed Valeur de la variable ou valeur par defaut
 * 
 */
function env($key, $defaultValue = null) {
    /**
     * 
     * Chercher dans l'ordre: getenv(), $_ENV, $_SERVER
     * 
     */
    $value = getenv($key);
    
    if ($value === false) {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : (isset($_SERVER[$key]) ? $_SERVER[$key] : $defaultValue);
    }
    
    return $value;
}


?>