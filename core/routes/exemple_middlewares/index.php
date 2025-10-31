<?php
/**
 * 🎯 EXEMPLES COMPLETS DES NOUVEAUX MIDDLEWARES
 * 
 * Route: GET /api/exemple_middlewares
 * 
 * Cette route démontre l'utilisation de:
 * - ✅ Middleware de validation JSON
 * - ✅ Middleware d'authentification
 * - ✅ Middleware de vérification de rôles
 * - ✅ Rate limiting
 * - ✅ Détection de conflits de variables
 */

require_method('GET');

try {
    // ========================================
    // 🔍 DÉTECTION DE CONFLITS (MODE DEBUG)
    // ========================================
    $conflicts = debug_detect_variable_conflicts();
    $sharedVars = debug_get_shared_variables();
    
    // ========================================
    // 📚 EXEMPLES DE CODE
    // ========================================
    $examples = [
        'middleware_validation' => [
            'title' => '1. Validation JSON (POST/PUT/PATCH)',
            'code' => '
// Dans votre route index.php
$data = middleware_validate_json(
    [\'email\', \'password\'],           // Champs requis
    [\'remember\' => false]              // Champs optionnels avec défaut
);

if (!$data) exit; // Erreur déjà envoyée

// Utiliser les données validées
$email = $data[\'email\'];
$password = $data[\'password\'];
$remember = $data[\'remember\'];
',
            'usage' => 'POST /api/ma_route avec JSON: {"email": "test@test.com", "password": "123456"}'
        ],
        
        'middleware_auth' => [
            'title' => '2. Authentification JWT',
            'code' => '
// Vérifier l\'authentification
$user = middleware_require_auth();
if (!$user) exit; // Non authentifié (401)

// Utiliser les données du token
$userId = $user[\'id\'];
$username = $user[\'username\'];
',
            'usage' => 'Ajouter header: Authorization: Bearer YOUR_JWT_TOKEN'
        ],
        
        'middleware_roles' => [
            'title' => '3. Vérification des Rôles',
            'code' => '
// Vérifier les permissions
if (!middleware_require_role([\'admin\', \'moderator\'])) {
    exit; // Accès refusé (403)
}

// Code réservé aux admins
',
            'usage' => 'Le token JWT doit contenir un champ "role" avec la valeur "admin" ou "moderator"'
        ],
        
        'rate_limiting_simple' => [
            'title' => '4. Rate Limiting Simple',
            'code' => '
// Limiter à 10 requêtes par minute
if (!rate_limit(10, 60)) {
    echo api_response(429, "Trop de requêtes. Réessayez plus tard.");
    exit;
}

// Headers automatiques:
// X-RateLimit-Limit: 10
// X-RateLimit-Remaining: 7
// X-RateLimit-Reset: 1234567890
// Retry-After: 45
',
            'usage' => 'Max 10 requêtes/minute par IP'
        ],
        
        'rate_limiting_advanced' => [
            'title' => '5. Rate Limiting Avancé (par route)',
            'code' => '
// Configuration personnalisée par route
if (!rate_limit_advanced([
    \'/api/login\' => [\'max\' => 5, \'window\' => 300],      // 5 par 5min
    \'/api/register\' => [\'max\' => 3, \'window\' => 3600],  // 3 par heure
    \'/api/search\' => [\'max\' => 100, \'window\' => 60],    // 100 par minute
    \'default\' => [\'max\' => 60, \'window\' => 60]         // 60 par minute
])) {
    echo api_response(429, "Limite atteinte");
    exit;
}
',
            'usage' => 'Limites différentes selon la route'
        ],
        
        'middleware_sanitize' => [
            'title' => '6. Sanitization des Données',
            'code' => '
// Nettoyer les entrées utilisateur
$cleanEmail = middleware_sanitize($email, \'email\');
$cleanUrl = middleware_sanitize($url, \'url\');
$cleanInt = middleware_sanitize($number, \'int\');
$cleanHtml = middleware_sanitize($text, \'html\');
$cleanString = middleware_sanitize($input, \'string\'); // Par défaut
',
            'usage' => 'Protection contre XSS et injection'
        ],
        
        'debug_conflicts' => [
            'title' => '7. Détection de Conflits (DEBUG)',
            'code' => '
// En mode DEBUG uniquement
$conflicts = debug_detect_variable_conflicts();

// Utiliser set_safe() pour détecter les écrasements
set_safe(\'userName\', \'John\'); // OK
set_safe(\'userName\', \'Jane\'); // ⚠️ WARNING dans les logs

// Forcer l\'écrasement
set_safe(\'userName\', \'Jane\', true); // OK, forcé

// Voir toutes les variables partagées
$vars = debug_get_shared_variables();
',
            'usage' => 'Activer DEBUG_MODE=true dans .env'
        ],
        
        'complete_example' => [
            'title' => '8. Exemple Complet (Login avec tous les middlewares)',
            'code' => '
<?php
require_method(\'POST\');

try {
    // 1. Rate limiting (max 5 tentatives par 5 minutes)
    if (!rate_limit(5, 300)) {
        echo api_response(429, "Trop de tentatives. Réessayez dans 5 minutes.");
        exit;
    }
    
    // 2. Valider les données JSON
    $data = middleware_validate_json(
        [\'email\', \'password\'],
        [\'remember\' => false]
    );
    if (!$data) exit;
    
    // 3. Sanitizer les entrées
    $email = middleware_sanitize($data[\'email\'], \'email\');
    $password = $data[\'password\'];
    
    // 4. Valider l\'email
    if (!middleware_validate_email($email, true)) {
        echo api_response(400, "Email invalide");
        exit;
    }
    
    // 5. Vérifier dans la BDD
    $user = db_find("SELECT * FROM users WHERE email = ?", [$email]);
    
    if (!$user || !password_verify($password, $user[\'password\'])) {
        echo api_response(401, "Identifiants incorrects");
        exit;
    }
    
    // 6. Générer JWT
    $token = jwt_generate([
        \'id\' => $user[\'id\'],
        \'email\' => $user[\'email\'],
        \'role\' => $user[\'role\']
    ]);
    
    echo api_response(200, "Connexion réussie", [
        \'token\' => $token,
        \'user\' => [
            \'id\' => $user[\'id\'],
            \'email\' => $user[\'email\']
        ]
    ]);
    
} catch(Exception $e) {
    echo getError($e);
}
?>
',
            'usage' => 'POST /api/login avec {"email": "user@example.com", "password": "secret"}'
        ]
    ];
    
    // ========================================
    // 📊 STATISTIQUES ET INFOS
    // ========================================
    $stats = [
        'total_middlewares' => 7,
        'rate_limiting_active' => function_exists('rate_limit'),
        'debug_mode' => env('DEBUG_MODE') === 'true',
        'conflicts_detected' => count($conflicts),
        'shared_variables' => count($sharedVars)
    ];
    
    // ========================================
    // 📤 RÉPONSE
    // ========================================
    echo api_response(200, "Documentation Middlewares StructureOne", [
        'version' => '2.1.1+',
        'examples' => $examples,
        'stats' => $stats,
        'debug' => [
            'conflicts' => $conflicts,
            'shared_variables' => $sharedVars
        ]
    ]);
    
} catch(Exception $e) {
    echo getError($e);
}
?>
