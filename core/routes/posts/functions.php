<?php
/**
 * Route POSTS - Exemple complet avec middleware et rate limiting
 * 
 * Cette route démontre l'utilisation COMPLÈTE du système de middleware
 */

// Configuration
set('postsTable', 'posts');
set('postsPerPage', 20);

/**
 * Créer un post
 */
function posts_create($data, $userId) {
    $table = get('postsTable');
    
    db_execute(
        "INSERT INTO {$table} (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())",
        [$userId, $data['title'], $data['content']]
    );
    
    return [
        'id' => db_last_id(),
        'title' => $data['title'],
        'content' => $data['content'],
        'user_id' => $userId
    ];
}

/**
 * Lister les posts
 */
function posts_list($page = 1) {
    $table = get('postsTable');
    $perPage = get('postsPerPage');
    $offset = ($page - 1) * $perPage;
    
    $posts = db_find(
        "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT ? OFFSET ?",
        [$perPage, $offset]
    );
    
    $total = db_count("SELECT COUNT(*) FROM {$table}");
    
    return [
        'posts' => $posts,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'pages' => ceil($total / $perPage)
        ]
    ];
}

/**
 * Obtenir un post par ID
 */
function posts_get($id) {
    $table = get('postsTable');
    return db_find("SELECT * FROM {$table} WHERE id = ?", [$id]);
}

/**
 * Mettre à jour un post
 */
function posts_update($id, $data, $userId) {
    $table = get('postsTable');
    
    // Vérifier que le post appartient à l'utilisateur
    $post = posts_get($id);
    if (!$post || $post['user_id'] != $userId) {
        return false;
    }
    
    db_execute(
        "UPDATE {$table} SET title = ?, content = ?, updated_at = NOW() WHERE id = ?",
        [$data['title'], $data['content'], $id]
    );
    
    return posts_get($id);
}

/**
 * Supprimer un post
 */
function posts_delete($id, $userId, $isAdmin = false) {
    $table = get('postsTable');
    
    // Vérifier que le post appartient à l'utilisateur (sauf si admin)
    if (!$isAdmin) {
        $post = posts_get($id);
        if (!$post || $post['user_id'] != $userId) {
            return false;
        }
    }
    
    db_execute("DELETE FROM {$table} WHERE id = ?", [$id]);
    return true;
}
?>
