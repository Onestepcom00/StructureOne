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
 * Gestionnaire d'exceptions global
 */
function globalExceptionHandler($exception) {
    $debugMode = env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true;
    
    // Log de l'exception
    error_log("EXCEPTION: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    
    $response = [
        'status' => 'error',
        'message' => 'Internal Server Error'
    ];
    
    // Ajouter les détails de débogage si activé
    if ($debugMode) {
        $response['debug'] = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => basename($exception->getFile()),
            'line' => $exception->getLine()
        ];
    }
    
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Enregistrer les gestionnaires d'erreurs au chargement du fichier
set_error_handler('globalErrorHandler');
set_exception_handler('globalExceptionHandler');

// =============================================================================
// FONCTIONS DE ROUTAGE (RÉTROCOMPATIBLES)
// =============================================================================

/**
 * ANCIENNE FONCTION - Conservée pour rétrocompatibilité
 * Extraire le nom de la route depuis l'URI
 * 
 * @param string $requestUri URI de la requête
 * @param string $baseAppDir Répertoire de base de l'application
 * @return string|null Nom de la route ou null
 */
function getRouteName($requestUri, $baseAppDir) {
    // Nettoyer l'URI
    $cleanUri = str_replace($baseAppDir, '', $requestUri);
    $cleanUri = trim($cleanUri, '/');
    
    // Séparer les parties de l'URI
    $uriParts = explode('/', $cleanUri);
    
    // Ancienne logique : /api/routeName
    if (count($uriParts) >= 2 && $uriParts[0] === 'api') {
        return $uriParts[1];
    }
    
    return null;
}

/**
 * NOUVELLE FONCTION - Détection du versionning
 * Détecte la route et la version à partir de l'URI
 */
function detectRouteAndVersion($requestUri, $baseAppDir) {
    // Nettoyer l'URI et enlever le base path
    $cleanUri = str_replace($baseAppDir, '', $requestUri);
    $cleanUri = trim($cleanUri, '/');
    
    // Séparer les parties de l'URI
    $uriParts = explode('/', $cleanUri);
    
    // Vérifier si c'est une route versionnée (format: /api/v1/routeName)
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
    // Vérifier si c'est une route legacy (format: /api/routeName)
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
 * FONCTION UNIFIÉE - Charge les fichiers de route
 * Supporte à la fois l'ancien et le nouveau système
 */
function loadRouteFiles($routeName, $version = 'legacy', $basePath = 'core/routes') {
    $routePath = "{$basePath}/{$routeName}";
    
    // Vérifier si le dossier de la route existe
    if (!is_dir($routePath)) {
        return [
            'success' => false,
            'error' => "Route '{$routeName}' not found",
            'version' => $version,
            'searched_path' => $routePath
        ];
    }
    
    // Chemin vers le fichier index principal de la route
    $indexFile = "{$routePath}/index.php";
    
    // Vérifier si le fichier index existe
    if (!file_exists($indexFile)) {
        return [
            'success' => false,
            'error' => "Route handler not found for '{$routeName}'",
            'version' => $version,
            'searched_file' => $indexFile
        ];
    }
    
    try {
        /**
         * INCLUSION INTELLIGENTE DES FONCTIONS
         * Inclure d'abord functions.php s'il existe, puis index.php
         */
        $functionsFile = "{$routePath}/functions.php";
        if (file_exists($functionsFile)) {
            require $functionsFile;
        }
        
        // Inclure le fichier index principal de la route
        require $indexFile;
        
        return [
            'success' => true,
            'message' => "Route '{$routeName}' loaded successfully",
            'version' => $version,
            'type' => ($version === 'legacy') ? 'legacy' : 'versioned'
        ];
    } catch (Exception $e) {
        $debugInfo = [];
        
        // Ajouter info debug si activé
        if (env('DEBUG_MODE') === 'true' || env('DEBUG_MODE') === true) {
            $debugInfo = [
                'exception_file' => basename($e->getFile()),
                'exception_line' => $e->getLine()
            ];
        }
        
        return [
            'success' => false,
            'error' => "Error loading route '{$routeName}'",
            'version' => $version,
            'debug_info' => $debugInfo
        ];
    }
}

/**
 * Récupère la liste des routes disponibles
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
 * Récupère la liste des versions disponibles
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
    
    // Toujours inclure la version legacy
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
    return htmlspecialchars(strip_tags($input));
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

?>
