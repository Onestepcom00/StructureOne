<?php

/**
 * 
 * *************************************
 * Projet : PROJECT_NAME
 * Nom du fichier : loader.php
 * Decsription : Ce fichier va contenir toute les functions utiles pour le fonctionnement de l'API , ses fonctions ne vont pas dependre des differentes routes , c'est des fonctions accessibles a toutes les routes de l'API.
 * Date de creation : PROJECT_DATE
 * Date de modification : PROJECT_DATE
 * version : PROJECT_VERSION
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
    // 2xx Success
    200 => ["success", $message ?? "Request was successful."],
    201 => ["success", $message ?? "Resource created successfully."],
    202 => ["success", $message ?? "Request accepted for processing."],
    204 => ["success", $message ?? "Request successful, no content to return."],
    
    // 3xx Redirection
    301 => ["redirect", $message ?? "Resource has been moved permanently."],
    302 => ["redirect", $message ?? "Resource has been moved temporarily."],
    304 => ["redirect", $message ?? "Resource not modified."],
    307 => ["redirect", $message ?? "Temporary redirect."],
    308 => ["redirect", $message ?? "Permanent redirect."],
    
    // 4xx Client Errors
    400 => ["error", $message ?? "Bad request. Please check your input."],
    401 => ["error", $message ?? "Unauthorized access. Please provide valid credentials."],
    402 => ["error", $message ?? "Payment required."],
    403 => ["error", $message ?? "Forbidden access. You do not have permission to access this resource."],
    404 => ["error", $message ?? "The requested resource was not found."],
    405 => ["error", $message ?? "Method not allowed."],
    406 => ["error", $message ?? "Not acceptable. The server cannot produce a response matching the list of acceptable values."],
    407 => ["error", $message ?? "Proxy authentication required."],
    408 => ["error", $message ?? "Request timeout."],
    409 => ["error", $message ?? "Conflict. The request could not be completed due to a conflict with the current state of the resource."],
    410 => ["error", $message ?? "Gone. The requested resource is no longer available."],
    411 => ["error", $message ?? "Length required."],
    412 => ["error", $message ?? "Precondition failed."],
    413 => ["error", $message ?? "Payload too large."],
    414 => ["error", $message ?? "URI too long."],
    415 => ["error", $message ?? "Unsupported media type."],
    416 => ["error", $message ?? "Range not satisfiable."],
    417 => ["error", $message ?? "Expectation failed."],
    418 => ["error", $message ?? "I'm a teapot."],
    421 => ["error", $message ?? "Misdirected request."],
    422 => ["error", $message ?? "Unprocessable entity. The request was well-formed but was unable to be followed due to semantic errors."],
    423 => ["error", $message ?? "Locked. The resource that is being accessed is locked."],
    424 => ["error", $message ?? "Failed dependency."],
    425 => ["error", $message ?? "Too early."],
    426 => ["error", $message ?? "Upgrade required."],
    428 => ["error", $message ?? "Precondition required."],
    429 => ["error", $message ?? "Too many requests. Please slow down your request rate."],
    431 => ["error", $message ?? "Request header fields too large."],
    451 => ["error", $message ?? "Unavailable for legal reasons."],
    
    // 5xx Server Errors
    500 => ["error", $message ?? "Internal server error. Please try again later."],
    501 => ["error", $message ?? "Not implemented. The server does not support the functionality required to fulfill the request."],
    502 => ["error", $message ?? "Bad gateway."],
    503 => ["error", $message ?? "Service unavailable. The server is currently unable to handle the request due to temporary overload or maintenance."],
    504 => ["error", $message ?? "Gateway timeout."],
    505 => ["error", $message ?? "HTTP version not supported."],
    506 => ["error", $message ?? "Variant also negotiates."],
    507 => ["error", $message ?? "Insufficient storage."],
    508 => ["error", $message ?? "Loop detected."],
    510 => ["error", $message ?? "Not extended."],
    511 => ["error", $message ?? "Network authentication required."]
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

// =============================================================================
// GESTIONNAIRES D'ERREURS GLOBAUX (NOUVEAU)
// =============================================================================

/**
 * Configure l'affichage des erreurs selon DEBUG_MODE
 */
function configureErrorDisplay() {
    $debugMode = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
    
    if ($debugMode) {
        // Mode développement
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_log("DEBUG_MODE: Activated - Full error display enabled");
    } else {
        // Mode production
        error_reporting(0);
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
    }
}

/**
 * Gestionnaire d'erreurs global pour l'API
 */
function globalErrorHandler($errno, $errstr, $errfile, $errline) {
    $debugMode = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
    
    // Log de l'erreur
    error_log("PHP Error [{$errno}]: {$errstr} in {$errfile} on line {$errline}");
    
    // Si DEBUG_MODE est activé et erreur critique, afficher détails
    if ($debugMode && in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Internal Server Error',
            'debug' => [
                'error_type' => 'PHP_ERROR',
                'message' => $errstr,
                'file' => basename($errfile),
                'line' => $errline
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Ne pas exécuter le gestionnaire d'erreurs interne de PHP
    return true;
}

/**
 * Gestionnaire d'exceptions global - VERSION AMÉLIORÉE
 * Centralise toute la gestion des exceptions avec traçabilité complète
 * Support de l'affichage HTML stylé si ERROR_DISPLAY_HTML est activé
 */
function globalExceptionHandler($exception) {
    $debugMode = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
    $htmlDisplay = env('ERROR_DISPLAY_HTML') === 'true' || env('ERROR_DISPLAY_HTML') === true;
    
    // Log de l'exception avec timestamp
    $logMessage = sprintf(
        "[%s] EXCEPTION: %s in %s:%d | Trace: %s",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $debugMode ? $exception->getTraceAsString() : 'hidden'
    );
    error_log($logMessage);
    
    // Si affichage HTML activé et mode debug
    if ($htmlDisplay && $debugMode) {
        // Préparer les variables pour la page HTML
        $errorType = get_class($exception);
        $errorTitle = 'Exception Détectée';
        $errorMessage = $exception->getMessage();
        $errorFile = $exception->getFile();
        $errorLine = $exception->getLine();
        $errorTrace = $exception->getTrace();
        $errorCode = null;
        
        // Essayer de lire le code autour de la ligne d'erreur
        if (file_exists($errorFile)) {
            $fileLines = file($errorFile);
            $start = max(0, $errorLine - 3);
            $end = min(count($fileLines), $errorLine + 2);
            $codeLines = array_slice($fileLines, $start, $end - $start);
            $errorCode = '';
            foreach ($codeLines as $i => $line) {
                $lineNum = $start + $i + 1;
                $marker = ($lineNum == $errorLine) ? '→' : ' ';
                $errorCode .= sprintf("%s %4d | %s", $marker, $lineNum, $line);
            }
        }
        
        // Envoyer les headers HTML
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        
        // Inclure la page d'erreur HTML
        include __DIR__ . '/core/error_page.php';
        exit;
    }
    
    // Sinon, affichage JSON classique
    $response = [
        'status' => 'error',
        'message' => 'Internal Server Error'
    ];
    
    if ($debugMode) {
        $response['debug'] = [
            'exception_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => basename($exception->getFile()),
            'line' => $exception->getLine(),
            'trace' => array_slice($exception->getTrace(), 0, 5)
        ];
    }
    
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Enregistrer les gestionnaires d'erreurs au chargement du fichier
set_error_handler('globalErrorHandler');
set_exception_handler('globalExceptionHandler');

// =============================================================================
// FONCTIONS DE ROUTAGE (RÉTROCOMPATIBLES AVEC AUTO-CHARGEMENT)
// =============================================================================

/**
 * ANCIENNE FONCTION - Conservée pour rétrocompatibilité
 * Extraire le nom de la route depuis l'URI
 */
function getRouteName($requestUri, $baseAppDir) {
    // Utilise la nouvelle fonction en arrière-plan
    $routeInfo = detectRouteAndVersion($requestUri, $baseAppDir);
    return $routeInfo ? $routeInfo['routeName'] : null;
}

/**
 * NOUVELLE FONCTION - Détection du versionning (inchangée)
 */
function detectRouteAndVersion($requestUri, $baseAppDir) {
    $cleanUri = str_replace($baseAppDir, '', $requestUri);
    $cleanUri = trim($cleanUri, '/');
    $uriParts = explode('/', $cleanUri);
    
    if (count($uriParts) >= 3 && $uriParts[0] === 'api' && preg_match('/^v\d+$/', $uriParts[1])) {
        $version = $uriParts[1];
        $routeName = $uriParts[2];
        $basePath = "core/versions/{$version}";
        
        return [
            'version' => $version,
            'routeName' => $routeName,
            'basePath' => $basePath,
            'type' => 'versioned'
        ];
    }
    elseif (count($uriParts) >= 2 && $uriParts[0] === 'api') {
        $routeName = $uriParts[1];
        $basePath = "core/routes";
        
        return [
            'version' => 'legacy',
            'routeName' => $routeName,
            'basePath' => $basePath,
            'type' => 'legacy'
        ];
    }
    
    return null;
}

/**
 * FONCTION UNIFIÉE AMÉLIORÉE - Charge tous les fichiers automatiquement
 * Supporte rétrocompatibilité + auto-chargement intelligent
 */
function loadRouteFiles($routeName, $version = 'legacy', $basePath = 'core/routes') {
    global $_so_routePath_global;
    
    $_so_routePath_global = "{$basePath}/{$routeName}";
    
    if (!is_dir($_so_routePath_global)) {
        return [
            'success' => false,
            'error' => "Route '{$routeName}' not found",
            'version' => $version,
            'searched_path' => $_so_routePath_global
        ];
    }
    
    $indexFile = "{$_so_routePath_global}/index.php";
    
    if (!file_exists($indexFile)) {
        return [
            'success' => false,
            'error' => "Route handler not found for '{$routeName}'",
            'version' => $version,
            'searched_file' => $indexFile
        ];
    }
    
    try {
        // CHARGEMENT INTELLIGENT DE TOUS LES FICHIERS
        $loadResult = loadAllRouteFiles($_so_routePath_global, $version);
        
        if (!$loadResult['success']) {
            return $loadResult;
        }
        
        // Inclure le fichier index principal en dernier
        // Les variables définies dans functions.php via $GLOBALS seront accessibles
        require $indexFile;
        
        return [
            'success' => true,
            'message' => "Route '{$routeName}' loaded successfully",
            'version' => $version,
            'type' => ($version === 'legacy') ? 'legacy' : 'versioned',
            'loaded_files' => $loadResult['loaded_files']
        ];
    } catch (Exception $e) {
        return handleRouteError($e, $routeName, $version);
    }
}

/**
 * NOUVELLE FONCTION - Charge automatiquement tous les fichiers PHP du dossier
 * Maintient la rétrocompatibilité avec l'ancien système
 * Note: Variables préfixées $_so_ pour éviter conflits avec routes utilisateur
 */
function loadAllRouteFiles($routePath, $version) {
    $_so_loadedFiles = [];
    
    if (!is_dir($routePath)) {
        return [
            'success' => false,
            'error' => "Route path not found: {$routePath}",
            'loaded_files' => $_so_loadedFiles
        ];
    }
    
    // Récupérer tous les fichiers PHP du dossier (sauf index.php qui sera chargé après)
    $_so_files = scandir($routePath);
    if ($_so_files === false) {
        return [
            'success' => false,
            'error' => "Unable to read directory: {$routePath}",
            'loaded_files' => $_so_loadedFiles
        ];
    }
    
    $_so_phpFiles = [];
    
    foreach ($_so_files as $_so_file) {
        if ($_so_file !== '.' && $_so_file !== '..' && 
            $_so_file !== 'index.php' && 
            pathinfo($_so_file, PATHINFO_EXTENSION) === 'php' &&
            is_file("{$routePath}/{$_so_file}")) { // Vérifier que c'est bien un fichier
            $_so_phpFiles[] = $_so_file;
        }
    }
    
    // TRI INTELLIGENT pour maintenir l'ordre de chargement
    // 1. functions.php en premier (rétrocompatibilité)
    // 2. Les autres fichiers par ordre alphabétique
    $_so_priorityFiles = [];
    $_so_otherFiles = [];
    
    foreach ($_so_phpFiles as $_so_file) {
        if ($_so_file === 'functions.php') {
            $_so_priorityFiles[] = $_so_file;
        } else {
            $_so_otherFiles[] = $_so_file;
        }
    }
    
    // Trier les autres fichiers par ordre alphabétique
    sort($_so_otherFiles);
    $_so_sortedFiles = array_merge($_so_priorityFiles, $_so_otherFiles);
    
    // CHARGEMENT DES FICHIERS - MÉTHODE SIMPLE ET FIABLE
    // Les fichiers sont chargés normalement, les fonctions sont automatiquement globales
    // Pour les variables, utilisez $GLOBALS dans les fichiers inclus
    foreach ($_so_sortedFiles as $_so_file) {
        $_so_filePath = "{$routePath}/{$_so_file}";
        if (file_exists($_so_filePath) && is_readable($_so_filePath)) {
            try {
                // Charger le fichier simplement
                require $_so_filePath;
                $_so_loadedFiles[] = $_so_file;
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'error' => "Error loading file '{$_so_file}': " . $e->getMessage(),
                    'failed_file' => $_so_file,
                    'loaded_files' => $_so_loadedFiles
                ];
            }
        }
    }
    
    return [
        'success' => true,
        'loaded_files' => $_so_loadedFiles,
        'total_files' => count($_so_sortedFiles)
    ];
}

/**
 * Gestion centralisée des erreurs de route
 */
function handleRouteError($e, $routeName, $version) {
    $debugInfo = [];
    
    if (env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true) {
        $debugInfo = [
            'exception_file' => basename($e->getFile()),
            'exception_line' => $e->getLine(),
            'exception_message' => $e->getMessage()
        ];
    }
    
    return [
        'success' => false,
        'error' => "Error loading route '{$routeName}'",
        'version' => $version,
        'debug_info' => $debugInfo
    ];
}

/**
 * Récupère la liste des routes disponibles (inchangée)
 */
function getAvailableRoutes($basePath) {
    if (!is_dir($basePath)) {
        return ["Directory not found: {$basePath}"];
    }
    
    $items = scandir($basePath);
    $routes = [];
    
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_dir("{$basePath}/{$item}")) {
            $routes[] = $item;
        }
    }
    
    return $routes;
}

/**
 * Récupère la liste des versions disponibles (inchangée)
 */
function getAvailableVersions() {
    $versions = [];
    $versionsPath = 'core/versions';
    
    if (is_dir($versionsPath)) {
        $folders = scandir($versionsPath);
        foreach ($folders as $folder) {
            if ($folder !== '.' && $folder !== '..' && is_dir("{$versionsPath}/{$folder}")) {
                if (preg_match('/^v\d+$/', $folder)) {
                    $versions[] = $folder;
                }
            }
        }
        sort($versions);
    }
    
    $versions[] = 'legacy';
    
    return $versions;
}

// Configuration automatique de l'affichage des erreurs
configureErrorDisplay();


/**
 * 
 * Fonction pour charger les variables d'environnement depuis le fichier .env
 * 
 * @param string $envPath Chemin vers le fichier .env
 * @return bool True si le chargement a reussi, false sinon
 * 
 */
function loadEnv($envPath = null) {
   /**
    *
    *  Verifier si le fichier n'environnement n'a pas ete donner 
    *
    *
    */
    if($envPath == null){
       $envPath = ".env"; // Utiliser le chemin par defaut 
    }else{
       $envPath = $envPath; // Utiliser le chemin que l'utilisateur chosit
    }
   
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
             * Définir la variable d'environnement (TOUJOURS écraser avec .env)
             * Le fichier .env a toujours la priorité
             * 
             */
            if (!empty($key)) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
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


/**
 * Cette fonction permet de générer un token JWT de manière dynamique
 * 
 * @param mixed $data - Peut être un ID simple ou un tableau de données arbitraires
 * @return string - Le token JWT généré (URL-safe)
 */
function jwt_generate($data) {
    /**
     * Récupérer le token depuis la configuration 
     */
    $secret = env('API_TOKEN_SECRET') ?? API_TOKEN_SECRET;

    /**
     * Créer une date d'expiration 
     */
    $expiry = time() + (env('API_TOKEN_EXP') ?? API_TOKEN_EXP);

    /**
     * Préparer le payload selon le type de données
     */
    if (is_array($data)) {
        // Si c'est un tableau, on l'utilise TELL QUEL sans modification
        $payload = $data;
    } else {
        // Si c'est un simple élément, on crée un payload minimal
        $payload = [
            'uid' => $data
        ];
    }

    /**
     * Ajouter l'expiration au payload (obligatoire pour JWT)
     */
    $payload['exp'] = $expiry;

    /**
     * Encoder en JSON puis en base64 URL-safe
     */
    $encodedPayload = base64url_encode(json_encode($payload));
    $signature = hash_hmac('sha256', $encodedPayload . $expiry, $secret);
    
    $token = $encodedPayload . '.' . base64url_encode($signature);

    /**
     * Renvoyer le token 
     */
    return $token;
}

/**
 * Fonction pour encoder en base64 URL-safe
 * Supprime les = de padding et remplace les caractères problématiques
 */
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Fonction pour décoder le base64 URL-safe
 */
function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

/**
 * Cette fonction permet de vérifier et décoder un token JWT
 * 
 * @param string $token - Le token JWT à vérifier (URL-safe)
 * @return mixed - Les données du payload ou false si invalide
 * 
 */
function jwt_validate($token) {
    $secret = env('API_TOKEN_SECRET') ?? API_TOKEN_SECRET;
    
    // Séparer le payload et la signature
    $parts = explode('.', $token);
    
    if (count($parts) !== 2) {
        return false;
    }
    
    list($encodedPayload, $encodedSignature) = $parts;
    
    // Décoder le payload avec base64 URL-safe
    $payloadJson = base64url_decode($encodedPayload);
    $payload = json_decode($payloadJson, true);
    
    if (!$payload || !isset($payload['exp'])) {
        return false;
    }
    
    // Vérifier l'expiration
    if ($payload['exp'] < time()) {
        return false;
    }
    
    // Vérifier la signature
    $expectedSignature = hash_hmac('sha256', $encodedPayload . $payload['exp'], $secret);
    $signature = base64url_decode($encodedSignature);
    
    if (!hash_equals($expectedSignature, $signature)) {
        return false;
    }
    
    // Retourner les données UTILISATEUR
    return $payload;
}


/**
 * 
 * Fonction pour valider les entrees dans une requete
 * 
 * 
 */
function validate($data,$rules){
    foreach($rules as $field => $rule){
        if($rule === 'required' && empty($data[$field])){
            throw new Exception("Le champ $field est requis.");
        }
    }
}

/**
 * 
 * Authentification rapide avec JWT : Composant 
 * 
 */
function JWTAuth(){
    /**
     * 
     * Recuperer la fonction global 
     * 
     */
    global $JWT_HTTP_TOKEN;

    /**
     * 
     * Valider le token pour recuperer l'ID de l'utilisateur 
     * 
     */
    $userId = jwt_validate($JWT_HTTP_TOKEN);

  
}


/**
 * ********************
 * Composant : BASE DES DONNEES
 * ********************
 */

/**
 * Gestion centralisée des erreurs
 * 
 * @param string $message
 * @param Exception|null $exception
 * 
 */
function db_error(string $message, ?Exception $exception = null) {
    /**
     * 
     * Lire le mode debug depuis .env
     * 
     */
    $debug = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;

    /**
     * 
     * 
     * Construire le message d'erreur complet si debug activé
     * 
     * 
     */
    $errorMessage = $debug && $exception ? $message . ' - ' . $exception->getMessage() : $message;

    /* Optionnel : logger dans un fichier
    $logFile = BASE_LOGS . '/db_errors.log';
    $logContent = "[" . date("Y-m-d H:i:s") . "] " . $errorMessage . "\n";
    file_put_contents($logFile, $logContent, FILE_APPEND);
    */

    /**
     * 
     * Retour JSON pour API
     * 
     */
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * 
     * Ici nous ne pouvons pas utiliser api_response car on risquerai de creer des conflits 
     * 
     */
    echo json_encode([
        'status' => 'error',
        'message' => $errorMessage
    ], JSON_UNESCAPED_UNICODE);

    /**
     * 
     * 
     * Stoppe proprement l'exécution pour éviter d'appeler db_connect() sur null
     * 
     */
    exit;
}

/**
 * 
 * Connexion à la base de données
 * @return PDO
 * 
 * 
 */
function db_connect(): PDO {
    /**
     * 
     * Mettre la variable $pdo en static 
     * 
     * 
     */
    static $pdo = null;


    /**
     *  
     * 
     * Verifier si la connexion existe 
     * 
     * 
     */
    if ($pdo === null) {
        /**
         * 
         * DSN complet
         * 
         * 
         */
        $host = env('DB_HOST') ?? 'localhost';
        $port = env('DB_PORT') ?? 3306;
        $dbname = env('DB_NAME') ?? 'test';
        $user = env('DB_USER') ?? 'root';
        $pass = env('DB_PASS') ?? '';

        /**
         * 
         * 
         * MEttre en place 
         * 
         * 
         */
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        /**
         * 
         * Essayer d'executer la requete 
         * 
         * 
         */
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            /**
             * 
             * Gerer l'erreur directement
             * 
             */
            db_error("Erreur de connexion à la base de données", $e);
        }
    }

    /**
     * 
     * Renvoyer la conexion
     * 
     */
    return $pdo;
}

/**
 * 
 * 
 * SELECT multiple lignes
 * 
 * 
 */
function db_select(string $sql, array $params = []): array {
    /**
     * 
     * Essayer d'executer la requete 
     * 
     * 
     */
    try {
        /**
         * 
         * Preparer la requete 
         * 
         */
        $stmt = db_connect()->prepare($sql);

        /**
         * 
         * 
         * Executer la requete 
         * 
         * 
         */
        $stmt->execute($params);

        /**
         * 
         * fetchAll
         * 
         * 
         */
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        /**
         * 
         * gerer mieux les erreurs 
         * 
         */
        db_error("Erreur SELECT", $e);
    }
}

/**
 * 
 * 
 * SELECT une seule ligne
 * 
 * 
 */
function db_find(string $sql, array $params = []): ?array {
    /**
     * 
     * Essayer d'executer la requete 
     * 
     */
    try {
        /**
         * 
         * Preparer la requete 
         * 
         */
        $stmt = db_connect()->prepare($sql);

        /**
         * 
         * Executer la requete 
         * 
         */
        $stmt->execute($params);

        /**
         * 
         * fecth
         * 
         */
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        /**
         * 
         * gerer mieux les erreurs 
         * 
         */
        db_error("Erreur SELECT ONE", $e);
    }
}

/**
 * 
 * 
 * INSERT / UPDATE / DELETE
 * 
 * 
 */
function db_execute(string $sql, array $params = []): bool {
    /**
     * 
     * Essayer d'executer la requete 
     * 
     */
    try {
        /**
         * 
         * Preparer la requete
         * 
         */
        $stmt = db_connect()->prepare($sql);

        /**
         * 
         * Executer la requete 
         * 
         */
        return $stmt->execute($params);
    } catch (PDOException $e) {
        /**
         * 
         * Gerer la reponse 
         * 
         */
        db_error("Erreur EXECUTE", $e);
    }
}

/**
 * 
 * 
 * Dernier ID inséré
 * 
 * 
 */
function db_last_id(): string {
    /**
     * 
     * Essayer d'executer la requete 
     * 
     * 
     */
    try {
        /**
         * 
         * Renvoyer le dernier ID 
         * 
         */
        return db_connect()->lastInsertId();
    } catch (PDOException $e) {
        /**
         * 
         * Gerer mieux les erreurs
         * 
         */
        db_error("Impossible de récupérer le dernier ID", $e);
    }
}

/**
 * 
 * Sécurisation des entrées
 * 
 */
function db_escape(string $input): string {
    return trim(strip_tags($input));
}

/**
 * 
 * 
 * Hash du mot de passe
 * 
 * 
 */
function db_hash(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}


/**
 * Compte le nombre de lignes correspondant à une requête
 * 
 * @param string $sql Requête SQL (doit contenir un COUNT)
 * @param array $params Paramètres pour la requête
 * @return int Nombre de lignes
 * 
 * @example db_count("SELECT COUNT(*) FROM users WHERE active = ?", [1])
 */
function db_count(string $sql, array $params = []): int {
    try {
        $stmt = db_connect()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        db_error("Erreur COUNT", $e);
    }
}

/**
 * Vérifie si des éléments existent dans une table avec des conditions flexibles
 * 
 * @param string $table Table à vérifier
 * @param array $conditions Conditions sous forme [colonne => valeur] ou string SQL
 * @param array $params Paramètres si condition SQL
 * @return bool True si au moins un élément existe
 * 
 * @example db_element_exist('users', ['email' => 'test@example.com'])
 * @example db_element_exist('users', 'email = ? AND active = ?', ['test@example.com', 1])
 * 
 */
function db_element_exist(string $table, $conditions = [], array $params = []): bool {
    try {
        if (is_array($conditions) && !empty($conditions)) {
            // Mode tableau associatif
            $whereParts = [];
            $whereParams = [];
            
            foreach ($conditions as $column => $value) {
                $whereParts[] = "`{$column}` = ?";
                $whereParams[] = $value;
            }
            
            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE " . implode(' AND ', $whereParts);
            $params = $whereParams;
        } elseif (is_string($conditions) && !empty($conditions)) {
            // Mode SQL personnalisé
            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$conditions}";
        } else {
            // Compter toutes les lignes
            $sql = "SELECT COUNT(*) FROM `{$table}`";
        }
        
        return db_count($sql, $params) > 0;
        
    } catch (PDOException $e) {
        $debug = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
        $message = "Erreur vérification existence dans {$table}";
        
        if ($debug) {
            error_log("DB_HAS Error: " . $e->getMessage() . " | Conditions: " . json_encode($conditions));
        }
        
        db_error($message, $debug ? $e : null);
    }
}

/**
 * Insère des données avec validation et gestion d'erreurs améliorée
 * 
 * @param string $table Table cible
 * @param array $data Données à insérer
 * @param bool $ignoreDuplicate Ignorer les erreurs de doublon
 * @return string|bool Dernier ID inséré ou false
 */
function db_insert(string $table, array $data, bool $ignoreDuplicate = false) {
    if (empty($data)) {
        error_log("DB_INSERT Error: Données vides pour la table {$table}");
        return false;
    }
    
    try {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $ignore = $ignoreDuplicate ? 'IGNORE' : '';
        $sql = "INSERT {$ignore} INTO `{$table}` (`" . implode('`, `', $columns) . "`) 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $success = db_execute($sql, array_values($data));
        
        if ($success) {
            $lastId = db_last_id();
            
            // Log en mode debug
            $debug = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
            if ($debug) {
                error_log("DB_INSERT Success: Table {$table} | ID: {$lastId} | Data: " . json_encode($data));
            }
            
            return $lastId;
        }
        
        return false;
        
    } catch (PDOException $e) {
        $debug = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
        $message = "Erreur insertion dans {$table}";
        
        if ($debug) {
            error_log("DB_INSERT Error: " . $e->getMessage() . " | Data: " . json_encode($data));
        }
        
        // Gestion spécifique des doublons
        if (strpos($e->getMessage(), 'Duplicate entry') !== false && $ignoreDuplicate) {
            return false;
        }
        
        db_error($message, $debug ? $e : null);
    }
}


/**
 * PRISE EN CHARGE DE LA GESTION DES METHODES 
 * 
 * Ces fonctions vérifient la méthode HTTP et bloquent l'exécution
 * si la méthode n'est pas autorisée
 */


/**
 * Vérifie la méthode et renvoie une erreur si non conforme
 * 
 * @param string $expectedMethod La méthode HTTP attendue (GET, POST, etc.)
 * @param mixed $errorResponse Réponse d'erreur personnalisée (optionnel)
 * @return bool Retourne true si la méthode est conforme
 */
function require_method($expectedMethod, $errorResponse = null) {
   /**
    * 
    *
    * Nous avons ajouter cette ligne pour eviter les erreurs de CORS origin
    *
    *
    */
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
   
    $expectedMethod = strtoupper($expectedMethod);
    
    if ($_SERVER['REQUEST_METHOD'] !== $expectedMethod) {
        if ($errorResponse === null) {
            /**
             * Envoyer une réponse d'erreur par défaut
             */
            header('Allow: ' . $expectedMethod);
            echo api_response(405, "Cette endpoint nécessite la méthode $expectedMethod", null);
        } else {
            if (is_callable($errorResponse)) {
                $errorResponse();
            } else {
                echo $errorResponse;
            }
        }
        
        /**
         * ARRÊTER IMMÉDIATEMENT L'EXÉCUTION DU SCRIPT
         * pour éviter que le code suivant soit exécuté
         */
        exit;
    }
    
    return true;
}

/**
 * Vérifie que l'une des méthodes est utilisée
 * 
 * @param array|string $expectedMethods Les méthodes HTTP autorisées
 * @param mixed $errorResponse Réponse d'erreur personnalisée (optionnel)
 * @return bool Retourne true si une des méthodes est conforme
 */
function require_method_in($expectedMethods, $errorResponse = null) {
   /**
    * 
    *
    * Nous avons ajouter cette ligne pour eviter les erreurs de CORS origin
    *
    *
    */
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
   
    if (is_string($expectedMethods)) {
        $expectedMethods = explode(',', $expectedMethods);
    }
    
    $expectedMethods = array_map('strtoupper', $expectedMethods);
    $currentMethod = $_SERVER['REQUEST_METHOD'];
    
    if (!in_array($currentMethod, $expectedMethods)) {
        if ($errorResponse === null) {
            /**
             * Renvoyer une réponse d'erreur par défaut
             */
            header('Allow: ' . implode(', ', $expectedMethods));
            echo api_response(405, "Méthodes autorisées: " . implode(', ', $expectedMethods), null);
        } else {
            if (is_callable($errorResponse)) {
                $errorResponse();
            } else {
                echo $errorResponse;
            }
        }
        
        /**
         * ARRÊTER IMMÉDIATEMENT L'EXÉCUTION DU SCRIPT
         * pour éviter que le code suivant soit exécuté
         */
        exit;
    }
    
    return true;
}

/**
 * ============================================================================
 * FONCTIONS HELPERS POUR VARIABLES PARTAGÉES (AUTOMATIQUE)
 * ============================================================================
 * Ces fonctions simplifient le partage de variables entre functions.php et index.php
 */

/**
 * Définir une variable partagée (utilisable dans functions.php)
 * 
 * @param string $name Nom de la variable
 * @param mixed $value Valeur de la variable
 * 
 * @example set('userName', 'John Doe');
 * @example set('config', ['timeout' => 30]);
 */
function set($name, $value) {
    $GLOBALS[$name] = $value;
}

/**
 * Récupérer une variable partagée (utilisable dans index.php)
 * 
 * @param string $name Nom de la variable
 * @param mixed $default Valeur par défaut si non trouvée
 * @return mixed Valeur de la variable
 * 
 * @example $userName = get('userName');
 * @example $config = get('config', []);
 */
function get($name, $default = null) {
    return $GLOBALS[$name] ?? $default;
}

/**
 * Vérifier si une variable partagée existe
 * 
 * @param string $name Nom de la variable
 * @return bool
 * 
 * @example if (has('userName')) { ... }
 */
function has($name) {
    return isset($GLOBALS[$name]);
}

/**
 * Définir plusieurs variables en une seule fois
 * 
 * @param array $vars Tableau associatif [nom => valeur]
 * 
 * @example setMany(['user' => 'John', 'age' => 25]);
 */
function setMany(array $vars) {
    foreach ($vars as $name => $value) {
        $GLOBALS[$name] = $value;
    }
}

/**
 * ============================================================================
 * FIN DES FONCTIONS HELPERS
 * ============================================================================
 */

/**
 * LA FONCTION POUR GERER LES ERREURS - VERSION AMÉLIORÉE
 * 
 * Gestion centralisée des erreurs avec logging automatique
 * Respecte DEBUG_MODE pour l'affichage des détails
 * 
 * @param Exception $e L'exception capturée
 * @param int $httpCode Code HTTP à retourner (par défaut 500)
 * @param string $customMessage Message personnalisé (optionnel)
 * @return string Réponse JSON formatée
 */
function getError($e, $httpCode = 500, $customMessage = null) {
    $debugMode = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
    
    // Logger l'erreur avec timestamp
    $logMessage = sprintf(
        "[%s] ERROR [%d]: %s in %s:%d",
        date('Y-m-d H:i:s'),
        $httpCode,
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    error_log($logMessage);
    
    // Préparer les données de debug si nécessaire
    $debugData = null;
    if ($debugMode) {
        $debugData = [
            'error' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'type' => get_class($e)
        ];
    }
    
    // Utiliser le message personnalisé ou celui par défaut
    $message = $customMessage ?: null;
    
    return api_response($httpCode, $message, $debugData);
}

/**
 * Fonction helper pour logger des messages personnalisés
 * 
 * @param string $message Message à logger
 * @param string $level Niveau: 'INFO', 'WARNING', 'ERROR', 'DEBUG'
 */
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}";
    error_log($logEntry);
}

/**
 * Charger automatiquement les dépendances Composer
 *
 * Cette fonction détecte automatiquement le bon chemin vers vendor/autoload.php
 * peu importe depuis quel dossier ou environnement (Apache, Nginx, CLI)
 * l’application est exécutée.
 */
function getComposer()
{
    $autoloadPaths = [
        __DIR__ . '/vendor/autoload.php',            // racine du projet
        __DIR__ . '/../vendor/autoload.php',         // exécution depuis /core/
        dirname(__DIR__) . '/vendor/autoload.php',   // exécution depuis /core/routes/ ou /core/versions/
        __DIR__ . '/../../vendor/autoload.php',      // sécurité : route ou version dans un sous-niveau
        dirname(__DIR__, 2) . '/vendor/autoload.php',// fallback supplémentaire (cas rare)
        $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php' // exécution via serveur web mal configuré
    ];

    $autoloadLoaded = false;

    foreach ($autoloadPaths as $autoloadFile) {
        if (file_exists($autoloadFile)) {
            require_once $autoloadFile;
            $autoloadLoaded = true;
            break;
        }
    }

    return $autoloadLoaded;
}

/**
 * ============================================================================
 * 🧱 SYSTÈME DE MIDDLEWARE ULTRA-SIMPLIFIÉ
 * ============================================================================
 * Un seul point d'entrée pour TOUS vos besoins de validation et sécurité
 * 
 * PRINCIPE : Une seule fonction middleware() qui gère TOUT
 * 
 * UTILISATION BASIQUE :
 *   middleware(['json' => ['email', 'password']]);
 * 
 * UTILISATION AVANCÉE :
 *   middleware([
 *       'json' => ['email', 'password'],
 *       'auth' => true,
 *       'role' => ['admin'],
 *       'rate' => [5, 300]
 *   ]);
 */

/**
 * MIDDLEWARE CENTRAL - Point d'entrée unique ultra-simple
 * 
 * @param array $config Configuration du middleware
 * @return array|false Données validées ou false si erreur (avec réponse déjà envoyée)
 * 
 * @example Configuration disponible:
 * 
 * [
 *   // 1. VALIDATION JSON (POST/PUT/PATCH)
 *   'json' => ['email', 'password'],               // Champs requis
 *   'optional' => ['remember' => false],           // Champs optionnels avec défaut
 *   
 *   // 2. AUTHENTIFICATION
 *   'auth' => true,                                // Requiert JWT
 *   
 *   // 3. AUTORISATION (RÔLES)
 *   'role' => ['admin', 'moderator'],              // Rôles autorisés
 *   
 *   // 4. RATE LIMITING
 *   'rate' => [10, 60],                            // [max, secondes]
 *   
 *   // 5. VALIDATION PERSONNALISÉE
 *   'validate' => function($data) {
 *       if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
 *           return "Email invalide";
 *       }
 *       return true;
 *   },
 *   
 *   // 6. SANITIZATION AUTOMATIQUE
 *   'sanitize' => [
 *       'email' => 'email',
 *       'name' => 'string',
 *       'age' => 'int'
 *   ]
 * ]
 * 
 * @example Usage simple:
 * $data = middleware(['json' => ['email', 'password']]);
 * if (!$data) exit;
 * 
 * @example Usage complet:
 * $data = middleware([
 *     'json' => ['email', 'password'],
 *     'auth' => true,
 *     'role' => ['admin'],
 *     'rate' => [5, 300],
 *     'sanitize' => ['email' => 'email']
 * ]);
 * if (!$data) exit;
 */
function middleware(array $config = []) {
    $result = ['success' => true, 'data' => [], 'user' => null];
    
    // ========================================
    // 1. RATE LIMITING (en premier)
    // ========================================
    if (isset($config['rate'])) {
        $rate = $config['rate'];
        $max = is_array($rate) ? $rate[0] : 60;
        $window = is_array($rate) && isset($rate[1]) ? $rate[1] : 60;
        
        if (!rate_limit($max, $window)) {
            echo api_response(429, "Trop de requêtes. Réessayez plus tard.");
            return false;
        }
    }
    
    // ========================================
    // 2. AUTHENTIFICATION JWT
    // ========================================
    if (isset($config['auth']) && $config['auth'] === true) {
        $user = middleware_require_auth();
        if (!$user) return false;
        $result['user'] = $user;
    }
    
    // ========================================
    // 3. AUTORISATION (RÔLES)
    // ========================================
    if (isset($config['role'])) {
        $roles = is_array($config['role']) ? $config['role'] : [$config['role']];
        if (!middleware_require_role($roles)) return false;
    }
    
    // ========================================
    // 4. VALIDATION JSON
    // ========================================
    if (isset($config['json'])) {
        $required = is_array($config['json']) ? $config['json'] : [];
        $optional = isset($config['optional']) ? $config['optional'] : [];
        
        $data = middleware_validate_json($required, $optional);
        if (!$data) return false;
        
        $result['data'] = $data;
        
        // ========================================
        // 5. SANITIZATION AUTOMATIQUE
        // ========================================
        if (isset($config['sanitize'])) {
            foreach ($config['sanitize'] as $field => $type) {
                if (isset($result['data'][$field])) {
                    $result['data'][$field] = middleware_sanitize($result['data'][$field], $type);
                }
            }
        }
        
        // ========================================
        // 6. VALIDATION PERSONNALISÉE
        // ========================================
        if (isset($config['validate']) && is_callable($config['validate'])) {
            $validation = $config['validate']($result['data']);
            if ($validation !== true) {
                $message = is_string($validation) ? $validation : "Validation échouée";
                echo api_response(400, $message);
                return false;
            }
        }
    }
    
    // Retourner les données ou le résultat complet
    return empty($result['data']) ? $result : $result['data'];
}

/**
 * MIDDLEWARE RAPIDE - Versions raccourcies pour cas simples
 */

/**
 * Middleware JSON uniquement
 */
function middleware_json(array $required = [], array $optional = []) {
    return middleware(['json' => $required, 'optional' => $optional]);
}

/**
 * Middleware Auth uniquement
 */
function middleware_auth() {
    $result = middleware(['auth' => true]);
    return $result ? $result['user'] : false;
}

/**
 * Middleware Rate limit uniquement
 */
function middleware_rate($max = 60, $window = 60) {
    return middleware(['rate' => [$max, $window]]);
}

/**
 * ============================================================================
 * MIDDLEWARES INDIVIDUELS (pour usage avancé)
 * ============================================================================
 */

/**
 * Middleware : Valider les données JSON entrantes
 * 
 * @param array $requiredFields Champs requis
 * @param array $optionalFields Champs optionnels avec valeurs par défaut
 * @return array|false Données validées ou false si erreur
 * 
 * @example 
 * $data = middleware_validate_json(['email', 'password'], ['remember' => false]);
 * if (!$data) exit; // L'erreur est déjà envoyée
 */
function middleware_validate_json(array $requiredFields = [], array $optionalFields = []) {
    // Vérifier la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
        echo api_response(405, "Méthode non autorisée");
        return false;
    }
    
    // Récupérer et decoder le JSON
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);
    
    // Vérifier si le JSON est valide
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo api_response(400, "JSON invalide", ['error' => json_last_error_msg()]);
        return false;
    }
    
    // Vérifier les champs requis
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        echo api_response(400, "Champs manquants", [
            'missing_fields' => $missingFields,
            'required' => $requiredFields
        ]);
        return false;
    }
    
    // Ajouter les champs optionnels avec valeurs par défaut
    foreach ($optionalFields as $field => $defaultValue) {
        if (!isset($data[$field])) {
            $data[$field] = $defaultValue;
        }
    }
    
    return $data;
}

/**
 * Middleware : Vérifier l'authentification JWT
 * 
 * @param bool $required Si true, retourne 401 si pas de token
 * @return array|false Données du token ou false
 * 
 * @example
 * $user = middleware_require_auth();
 * if (!$user) exit; // Non authentifié
 */
function middleware_require_auth($required = true) {
    global $JWT_HTTP_TOKEN;
    
    $tokenData = jwt_validate($JWT_HTTP_TOKEN);
    
    if (!$tokenData && $required) {
        echo api_response(401, "Authentification requise", [
            'error' => 'Token manquant ou invalide'
        ]);
        return false;
    }
    
    return $tokenData;
}

/**
 * Middleware : Vérifier les rôles/permissions
 * 
 * @param array $allowedRoles Rôles autorisés
 * @param string $roleField Nom du champ role dans le token (défaut: 'role')
 * @return bool True si autorisé
 * 
 * @example
 * if (!middleware_require_role(['admin', 'moderator'])) exit;
 */
function middleware_require_role(array $allowedRoles, $roleField = 'role') {
    $tokenData = middleware_require_auth();
    
    if (!$tokenData) {
        return false;
    }
    
    $userRole = $tokenData[$roleField] ?? null;
    
    if (!$userRole || !in_array($userRole, $allowedRoles)) {
        echo api_response(403, "Accès refusé", [
            'error' => 'Rôle insuffisant',
            'required_roles' => $allowedRoles,
            'your_role' => $userRole
        ]);
        return false;
    }
    
    return true;
}

/**
 * Middleware : Validation d'email
 * 
 * @param string $email Email à valider
 * @param bool $checkDomain Vérifier si le domaine existe
 * @return bool
 */
function middleware_validate_email($email, $checkDomain = false) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    if ($checkDomain) {
        $domain = explode('@', $email)[1] ?? '';
        return !empty($domain) && checkdnsrr($domain, 'MX');
    }
    
    return true;
}

/**
 * Middleware : Sanitizer de données
 * 
 * @param mixed $data Données à nettoyer
 * @param string $type Type de nettoyage (string, email, url, int, float, html)
 * @return mixed Données nettoyées
 */
function middleware_sanitize($data, $type = 'string') {
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        case 'string':
        default:
            return db_escape($data);
    }
}

/**
 * ============================================================================
 * 🚦 SYSTÈME DE RATE LIMITING (LIMITATION DE TAUX)
 * ============================================================================
 * Protection contre les abus et attaques par force brute
 */

/**
 * Rate Limiter : Limiter les requêtes par IP
 * 
 * @param int $maxRequests Nombre max de requêtes
 * @param int $timeWindow Fenêtre de temps en secondes
 * @param string $identifier Identifiant unique (défaut: IP)
 * @return bool True si autorisé, false si limité
 * 
 * @example
 * // Max 10 requêtes par minute
 * if (!rate_limit(10, 60)) {
 *     echo api_response(429, "Trop de requêtes");
 *     exit;
 * }
 */
function rate_limit($maxRequests = 60, $timeWindow = 60, $identifier = null) {
    // Utiliser l'IP par défaut
    if ($identifier === null) {
        $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    // Créer une clé unique
    $key = 'rate_limit_' . md5($identifier . '_' . $_SERVER['REQUEST_URI']);
    
    // Fichier de cache pour stocker les compteurs
    $cacheDir = __DIR__ . '/core/cache';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . '/' . $key . '.json';
    
    // Lire les données existantes
    $data = [];
    if (file_exists($cacheFile)) {
        $content = file_get_contents($cacheFile);
        $data = json_decode($content, true) ?: [];
    }
    
    $now = time();
    $windowStart = $now - $timeWindow;
    
    // Nettoyer les anciennes requêtes
    $data = array_filter($data, function($timestamp) use ($windowStart) {
        return $timestamp > $windowStart;
    });
    
    // Compter les requêtes dans la fenêtre
    $requestCount = count($data);
    
    // Vérifier la limite
    if ($requestCount >= $maxRequests) {
        // Enregistrer dans les logs si DEBUG
        if (env('DEBUG_MODE') === 'true') {
            error_log("Rate limit exceeded for: $identifier on " . $_SERVER['REQUEST_URI']);
        }
        
        // Ajouter header Retry-After
        $oldestRequest = min($data);
        $retryAfter = ($oldestRequest + $timeWindow) - $now;
        header("Retry-After: $retryAfter");
        header("X-RateLimit-Limit: $maxRequests");
        header("X-RateLimit-Remaining: 0");
        header("X-RateLimit-Reset: " . ($oldestRequest + $timeWindow));
        
        return false;
    }
    
    // Ajouter la requête actuelle
    $data[] = $now;
    
    // Sauvegarder
    file_put_contents($cacheFile, json_encode($data));
    
    // Ajouter headers informatifs
    header("X-RateLimit-Limit: $maxRequests");
    header("X-RateLimit-Remaining: " . ($maxRequests - count($data)));
    header("X-RateLimit-Reset: " . ($now + $timeWindow));
    
    return true;
}

/**
 * Rate Limiter Avancé : Par route et méthode
 * 
 * @param array $limits Configuration des limites par route
 * @return bool
 * 
 * @example
 * rate_limit_advanced([
 *     '/api/login' => ['max' => 5, 'window' => 300],  // 5 par 5min
 *     '/api/register' => ['max' => 3, 'window' => 3600], // 3 par heure
 *     'default' => ['max' => 60, 'window' => 60] // 60 par minute
 * ]);
 */
function rate_limit_advanced(array $limits) {
    $route = $_SERVER['REQUEST_URI'];
    
    // Chercher la configuration pour cette route
    $config = $limits[$route] ?? $limits['default'] ?? ['max' => 60, 'window' => 60];
    
    return rate_limit($config['max'], $config['window']);
}

/**
 * ============================================================================
 * 🔍 DÉTECTION AUTOMATIQUE DES CONFLITS DE VARIABLES (MODE DEBUG)
 * ============================================================================
 * Aide au debugging en détectant les conflits de variables
 */

/**
 * Détecter les conflits de variables dans $GLOBALS
 * 
 * @param array $ignoredPrefixes Préfixes à ignorer (ex: ['_so_', 'GLOBALS'])
 * @return array Liste des conflits potentiels
 */
function debug_detect_variable_conflicts($ignoredPrefixes = ['_so_', 'GLOBALS', '_GET', '_POST', '_SERVER', '_ENV', '_FILES', '_COOKIE', '_REQUEST', '_SESSION']) {
    // Uniquement en mode DEBUG
    if (env('DEBUG_MODE') !== 'true') {
        return [];
    }
    
    $conflicts = [];
    $variables = [];
    
    // Analyser toutes les variables globales
    foreach ($GLOBALS as $name => $value) {
        // Ignorer les préfixes système
        $shouldIgnore = false;
        foreach ($ignoredPrefixes as $prefix) {
            if (strpos($name, $prefix) === 0) {
                $shouldIgnore = true;
                break;
            }
        }
        
        if ($shouldIgnore) {
            continue;
        }
        
        // Détecter les doublons ou similitudes
        foreach ($variables as $existingName => $existingValue) {
            // Vérifier similarité de nom (peut causer confusion)
            $similarity = 0;
            similar_text(strtolower($name), strtolower($existingName), $similarity);
            
            if ($similarity > 80 && $name !== $existingName) {
                $conflicts[] = [
                    'type' => 'similar_names',
                    'var1' => $name,
                    'var2' => $existingName,
                    'similarity' => round($similarity, 2),
                    'warning' => "Noms très similaires, risque de confusion"
                ];
            }
            
            // Vérifier même valeur (possible duplication)
            if ($value === $existingValue && !is_resource($value) && !is_object($value)) {
                $conflicts[] = [
                    'type' => 'same_value',
                    'var1' => $name,
                    'var2' => $existingName,
                    'value' => is_string($value) ? $value : gettype($value),
                    'warning' => "Même valeur, possible duplication"
                ];
            }
        }
        
        $variables[$name] = $value;
    }
    
    return $conflicts;
}

/**
 * Afficher un rapport de conflits en mode DEBUG
 * 
 * @return void
 */
function debug_show_conflicts_report() {
    if (env('DEBUG_MODE') !== 'true') {
        return;
    }
    
    $conflicts = debug_detect_variable_conflicts();
    
    if (empty($conflicts)) {
        return;
    }
    
    // Logger les conflits
    error_log("=== CONFLITS DE VARIABLES DÉTECTÉS ===");
    foreach ($conflicts as $conflict) {
        error_log(sprintf(
            "[%s] %s <-> %s : %s",
            $conflict['type'],
            $conflict['var1'],
            $conflict['var2'],
            $conflict['warning']
        ));
    }
    
    // Si activé, afficher dans les headers (uniquement en dev)
    if (env('DEBUG_MODE') === 'true' && !headers_sent()) {
        header('X-Debug-Variable-Conflicts: ' . count($conflicts));
    }
}

/**
 * Wrapper pour set() avec détection de conflit
 * 
 * @param string $name Nom de la variable
 * @param mixed $value Valeur
 * @param bool $force Forcer même si conflit détecté
 * @return bool
 */
function set_safe($name, $value, $force = false) {
    // En mode DEBUG, vérifier les conflits
    if (env('DEBUG_MODE') === 'true' && !$force) {
        if (has($name)) {
            $existingValue = get($name);
            if ($existingValue !== $value) {
                error_log("⚠️ WARNING: Variable '$name' déjà définie avec une valeur différente");
                error_log("  Ancienne valeur: " . var_export($existingValue, true));
                error_log("  Nouvelle valeur: " . var_export($value, true));
                
                if (!$force) {
                    return false;
                }
            }
        }
    }
    
    set($name, $value);
    return true;
}

/**
 * Obtenir un rapport complet des variables partagées
 * 
 * @return array
 */
function debug_get_shared_variables() {
    if (env('DEBUG_MODE') !== 'true') {
        return [];
    }
    
    $ignoredPrefixes = ['_so_', 'GLOBALS', '_GET', '_POST', '_SERVER', '_ENV', '_FILES', '_COOKIE', '_REQUEST', '_SESSION'];
    $sharedVars = [];
    
    foreach ($GLOBALS as $name => $value) {
        $shouldIgnore = false;
        foreach ($ignoredPrefixes as $prefix) {
            if (strpos($name, $prefix) === 0) {
                $shouldIgnore = true;
                break;
            }
        }
        
        if (!$shouldIgnore && !is_resource($value)) {
            $sharedVars[$name] = [
                'type' => gettype($value),
                'value' => is_object($value) ? get_class($value) : (is_array($value) ? 'array[' . count($value) . ']' : $value)
            ];
        }
    }
    
    return $sharedVars;
}

?>
