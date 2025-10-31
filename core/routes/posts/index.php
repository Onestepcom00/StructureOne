<?php
/**
 * ========================================
 * ROUTE POSTS - CRUD COMPLET
 * ========================================
 * 
 * Démontre l'utilisation du système de middleware simplifié
 * 
 * ENDPOINTS:
 * - GET    /api/posts           → Lister les posts (public, rate limit 100/min)
 * - GET    /api/posts?id=X      → Obtenir un post (public, rate limit 100/min)
 * - POST   /api/posts           → Créer un post (auth requis, rate limit 10/min)
 * - PUT    /api/posts           → Modifier un post (auth requis, rate limit 20/min)
 * - DELETE /api/posts?id=X      → Supprimer un post (auth requis, rate limit 20/min)
 * - DELETE /api/posts/admin?id=X → Supprimer n'importe quel post (admin uniquement)
 */

require_method_in(['GET', 'POST', 'PUT', 'DELETE']);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // ========================================
    // GET - LISTER OU OBTENIR UN POST
    // ========================================
    if ($method === 'GET') {
        // Rate limit public (généreux)
        $data = middleware([
            'rate' => [100, 60]  // 100 requêtes par minute
        ]);
        
        if (!$data) exit;
        
        // Si ID fourni, retourner un post spécifique
        if (isset($_GET['id'])) {
            $post = posts_get($_GET['id']);
            
            if (!$post) {
                echo api_response(404, "Post non trouvé");
                exit;
            }
            
            echo api_response(200, "Post trouvé", $post);
            exit;
        }
        
        // Sinon, lister les posts
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $result = posts_list($page);
        
        echo api_response(200, "Liste des posts", $result);
        exit;
    }
    
    // ========================================
    // POST - CRÉER UN POST
    // ========================================
    if ($method === 'POST') {
        // Middleware complet avec auth + validation + rate limit + sanitization
        $data = middleware([
            'rate' => [10, 60],              // 10 posts par minute max
            'auth' => true,                   // Authentification requise
            'json' => ['title', 'content'],   // Champs requis
            'sanitize' => [                   // Nettoyage automatique
                'title' => 'string',
                'content' => 'string'
            ],
            'validate' => function($data) {   // Validation personnalisée
                if (strlen($data['title']) < 3) {
                    return "Le titre doit contenir au moins 3 caractères";
                }
                if (strlen($data['title']) > 200) {
                    return "Le titre ne peut pas dépasser 200 caractères";
                }
                if (strlen($data['content']) < 10) {
                    return "Le contenu doit contenir au moins 10 caractères";
                }
                return true;
            }
        ]);
        
        if (!$data) exit;
        
        // Récupérer l'utilisateur depuis le middleware
        $user = middleware_auth();
        
        // Créer le post
        $post = posts_create($data, $user['id']);
        
        echo api_response(201, "Post créé avec succès", $post);
        exit;
    }
    
    // ========================================
    // PUT - MODIFIER UN POST
    // ========================================
    if ($method === 'PUT') {
        // Middleware avec auth + validation
        $data = middleware([
            'rate' => [20, 60],               // 20 modifications par minute max
            'auth' => true,
            'json' => ['id', 'title', 'content'],
            'sanitize' => [
                'title' => 'string',
                'content' => 'string'
            ],
            'validate' => function($data) {
                if (!isset($data['id']) || !is_numeric($data['id'])) {
                    return "ID invalide";
                }
                if (strlen($data['title']) < 3) {
                    return "Le titre doit contenir au moins 3 caractères";
                }
                if (strlen($data['content']) < 10) {
                    return "Le contenu doit contenir au moins 10 caractères";
                }
                return true;
            }
        ]);
        
        if (!$data) exit;
        
        $user = middleware_auth();
        
        // Mettre à jour le post
        $post = posts_update($data['id'], $data, $user['id']);
        
        if (!$post) {
            echo api_response(403, "Vous ne pouvez modifier que vos propres posts");
            exit;
        }
        
        echo api_response(200, "Post modifié avec succès", $post);
        exit;
    }
    
    // ========================================
    // DELETE - SUPPRIMER UN POST
    // ========================================
    if ($method === 'DELETE') {
        // Vérifier si c'est une suppression admin
        $isAdminRoute = strpos($_SERVER['REQUEST_URI'], '/admin') !== false;
        
        if ($isAdminRoute) {
            // Route admin - Vérifier le rôle admin
            $data = middleware([
                'rate' => [50, 60],           // Limite plus élevée pour admins
                'auth' => true,
                'role' => ['admin', 'moderator']
            ]);
            
            if (!$data) exit;
            
            $user = middleware_auth();
            $postId = $_GET['id'] ?? null;
            
            if (!$postId) {
                echo api_response(400, "ID du post requis");
                exit;
            }
            
            // Admin peut supprimer n'importe quel post
            $deleted = posts_delete($postId, $user['id'], true);
            
            if (!$deleted) {
                echo api_response(404, "Post non trouvé");
                exit;
            }
            
            echo api_response(200, "Post supprimé par admin");
            exit;
        }
        
        // Route normale - L'utilisateur peut supprimer ses propres posts
        $data = middleware([
            'rate' => [20, 60],
            'auth' => true
        ]);
        
        if (!$data) exit;
        
        $user = middleware_auth();
        $postId = $_GET['id'] ?? null;
        
        if (!$postId) {
            echo api_response(400, "ID du post requis");
            exit;
        }
        
        $deleted = posts_delete($postId, $user['id'], false);
        
        if (!$deleted) {
            echo api_response(403, "Vous ne pouvez supprimer que vos propres posts");
            exit;
        }
        
        echo api_response(200, "Post supprimé avec succès");
        exit;
    }
    
} catch(Exception $e) {
    echo getError($e);
}
?>
