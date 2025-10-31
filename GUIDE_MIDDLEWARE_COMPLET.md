# 📚 Guide Complet : Middlewares & Rate Limiting

## 🎓 Cours Complet sur les Middlewares

### 📖 Qu'est-ce qu'un Middleware ?

Un **middleware** est une **couche intermédiaire** qui s'exécute **AVANT** votre code métier pour :

```
Requête HTTP → Middleware → Votre Code → Réponse
```

**Analogie Simple :**
Imaginez un **agent de sécurité à l'entrée d'un bâtiment** :
1. Il vérifie votre identité (authentification)
2. Il vérifie vos permissions (autorisation)
3. Il vérifie que vous n'entrez pas trop souvent (rate limiting)
4. Il fouille votre sac (validation des données)

Le middleware fait **exactement la même chose** pour votre API !

---

## 🎯 Pourquoi utiliser des Middlewares ?

### ❌ Sans Middleware (Code Répétitif)

```php
// Route 1: Login
require_method('POST');
if (!rate_limit(5, 300)) exit;
$json = file_get_contents('php://input');
$data = json_decode($json, true);
if (!isset($data['email'])) { /* erreur */ }
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { /* erreur */ }
// ... 20 lignes de validation ...
// ENFIN votre code métier

// Route 2: Register
require_method('POST');
if (!rate_limit(3, 3600)) exit;
$json = file_get_contents('php://input');
$data = json_decode($json, true);
if (!isset($data['email'])) { /* erreur */ }
// ... ENCORE les mêmes 20 lignes ...
```

**Problèmes :**
- 😫 Code dupliqué partout
- 🐛 Bugs si vous oubliez une validation
- 🔧 Difficile à maintenir
- 📝 Trop de lignes

### ✅ Avec Middleware (Code Propre)

```php
// Route 1: Login
require_method('POST');
$data = middleware([
    'rate' => [5, 300],
    'json' => ['email', 'password'],
    'sanitize' => ['email' => 'email']
]);
if (!$data) exit;

// DIRECT À VOTRE CODE MÉTIER
$user = loginUser($data['email'], $data['password']);

// Route 2: Register  
require_method('POST');
$data = middleware([
    'rate' => [3, 3600],
    'json' => ['email', 'password', 'name']
]);
if (!$data) exit;

// DIRECT À VOTRE CODE MÉTIER
$user = registerUser($data);
```

**Avantages :**
- ✅ Code court et lisible
- ✅ Pas de duplication
- ✅ Validation garantie
- ✅ Facile à maintenir

---

## 🚀 Le Système de Middleware StructureOne

### Principe : Une Fonction, Tout Gérer

StructureOne utilise **UNE SEULE fonction** pour gérer TOUS vos besoins :

```php
middleware([
    'rate' => [max, secondes],           // Rate limiting
    'auth' => true,                      // Authentification
    'role' => ['admin'],                 // Autorisation
    'json' => ['champ1', 'champ2'],     // Validation JSON
    'optional' => ['champ' => defaut],   // Champs optionnels
    'sanitize' => ['champ' => 'type'],   // Nettoyage
    'validate' => function($data) {}     // Validation custom
]);
```

---

## 📝 Utilisation Étape par Étape

### Niveau 1 : Validation JSON Simple

**Cas d'usage :** Route publique qui reçoit des données JSON

```php
<?php
// core/routes/contact/index.php
require_method('POST');

try {
    // Valider que email, nom et message sont présents
    $data = middleware([
        'json' => ['email', 'nom', 'message']
    ]);
    
    if (!$data) exit; // Erreur déjà envoyée automatiquement
    
    // Utiliser les données validées
    $email = $data['email'];
    $nom = $data['nom'];
    $message = $data['message'];
    
    // Envoyer email, sauver en BDD, etc.
    sendContactEmail($email, $nom, $message);
    
    echo api_response(200, "Message envoyé avec succès");
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

**Test :**
```bash
curl -X POST http://localhost:8080/api/contact \
  -H "Content-Type: application/json" \
  -d '{"email":"user@test.com","nom":"John","message":"Hello"}'
```

---

### Niveau 2 : Avec Rate Limiting

**Cas d'usage :** Protéger contre les abus

```php
<?php
// core/routes/newsletter/index.php
require_method('POST');

try {
    // Rate limit : max 3 inscriptions par heure depuis une IP
    $data = middleware([
        'rate' => [3, 3600],  // [3 requêtes, 3600 secondes]
        'json' => ['email']
    ]);
    
    if (!$data) exit;
    
    // Inscrire à la newsletter
    subscribeNewsletter($data['email']);
    
    echo api_response(200, "Inscription réussie");
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

**Comportement :**
- 1ère requête : ✅ OK
- 2ème requête : ✅ OK
- 3ème requête : ✅ OK
- 4ème requête : ❌ 429 Too Many Requests (attendre 1 heure)

**Headers retournés automatiquement :**
```
X-RateLimit-Limit: 3
X-RateLimit-Remaining: 0
X-RateLimit-Reset: 1698765432
Retry-After: 3542
```

---

### Niveau 3 : Avec Authentification

**Cas d'usage :** Route réservée aux utilisateurs connectés

```php
<?php
// core/routes/profile/index.php
require_method('GET');

try {
    // Requiert un token JWT valide
    $data = middleware([
        'auth' => true,
        'rate' => [60, 60]  // 60 requêtes par minute
    ]);
    
    if (!$data) exit; // 401 si pas de token
    
    // Récupérer les infos utilisateur du token
    $user = middleware_auth();
    
    // Utiliser $user['id'], $user['email'], etc.
    $profile = getUserProfile($user['id']);
    
    echo api_response(200, "Profil", $profile);
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

**Test :**
```bash
curl http://localhost:8080/api/profile \
  -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

---

### Niveau 4 : Avec Autorisation (Rôles)

**Cas d'usage :** Route réservée aux admins

```php
<?php
// core/routes/admin/users/index.php
require_method('GET');

try {
    // Requiert auth + rôle admin
    $data = middleware([
        'auth' => true,
        'role' => ['admin', 'moderator'],  // Liste des rôles autorisés
        'rate' => [100, 60]
    ]);
    
    if (!$data) exit; // 403 si rôle insuffisant
    
    // Code réservé aux admins
    $allUsers = getAllUsers();
    
    echo api_response(200, "Liste des utilisateurs", $allUsers);
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

**Le token JWT doit contenir :**
```json
{
  "id": 123,
  "email": "admin@example.com",
  "role": "admin"
}
```

---

### Niveau 5 : Avec Sanitization

**Cas d'usage :** Nettoyer automatiquement les données pour éviter XSS

```php
<?php
// core/routes/comments/index.php
require_method('POST');

try {
    $data = middleware([
        'auth' => true,
        'rate' => [10, 60],
        'json' => ['post_id', 'comment'],
        'sanitize' => [
            'post_id' => 'int',      // Convertir en entier
            'comment' => 'string'     // Échapper HTML/XSS
        ]
    ]);
    
    if (!$data) exit;
    
    $user = middleware_auth();
    
    // $data['comment'] est maintenant nettoyé (pas de <script>, etc.)
    saveComment($user['id'], $data['post_id'], $data['comment']);
    
    echo api_response(201, "Commentaire ajouté");
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

**Types de sanitization disponibles :**
- `'email'` - Nettoie un email
- `'url'` - Nettoie une URL
- `'int'` - Convertit en entier
- `'float'` - Convertit en décimal
- `'html'` - Échappe les balises HTML
- `'string'` - Nettoyage général (défaut)

---

### Niveau 6 : Avec Validation Personnalisée

**Cas d'usage :** Règles de validation complexes

```php
<?php
// core/routes/register/index.php
require_method('POST');

try {
    $data = middleware([
        'rate' => [3, 3600],  // 3 inscriptions par heure max
        'json' => ['email', 'password', 'username'],
        'sanitize' => [
            'email' => 'email',
            'username' => 'string'
        ],
        'validate' => function($data) {
            // Validation email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return "Email invalide";
            }
            
            // Validation mot de passe
            if (strlen($data['password']) < 8) {
                return "Le mot de passe doit contenir au moins 8 caractères";
            }
            if (!preg_match('/[A-Z]/', $data['password'])) {
                return "Le mot de passe doit contenir au moins une majuscule";
            }
            if (!preg_match('/[0-9]/', $data['password'])) {
                return "Le mot de passe doit contenir au moins un chiffre";
            }
            
            // Validation username
            if (strlen($data['username']) < 3) {
                return "Le nom d'utilisateur doit contenir au moins 3 caractères";
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
                return "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et _";
            }
            
            // Vérifier si l'email existe déjà
            if (db_element_exist('users', ['email' => $data['email']])) {
                return "Cet email est déjà utilisé";
            }
            
            // Vérifier si le username existe déjà
            if (db_element_exist('users', ['username' => $data['username']])) {
                return "Ce nom d'utilisateur est déjà pris";
            }
            
            return true; // ✅ Validation réussie
        }
    ]);
    
    if (!$data) exit;
    
    // Créer l'utilisateur
    $userId = createUser($data);
    
    // Générer token JWT
    $token = jwt_generate([
        'id' => $userId,
        'email' => $data['email'],
        'role' => 'user'
    ]);
    
    echo api_response(201, "Inscription réussie", ['token' => $token]);
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

---

### Niveau 7 : Tout Combiné (Exemple Complet)

```php
<?php
// core/routes/posts/index.php (POST)
require_method('POST');

try {
    $data = middleware([
        // 1. Rate limiting
        'rate' => [10, 60],  // 10 posts par minute max
        
        // 2. Authentification
        'auth' => true,
        
        // 3. Validation JSON
        'json' => ['title', 'content'],
        'optional' => [
            'published' => false,
            'tags' => []
        ],
        
        // 4. Sanitization
        'sanitize' => [
            'title' => 'string',
            'content' => 'string'
        ],
        
        // 5. Validation personnalisée
        'validate' => function($data) {
            if (strlen($data['title']) < 3 || strlen($data['title']) > 200) {
                return "Le titre doit contenir entre 3 et 200 caractères";
            }
            if (strlen($data['content']) < 10) {
                return "Le contenu doit contenir au moins 10 caractères";
            }
            if (isset($data['tags']) && count($data['tags']) > 10) {
                return "Maximum 10 tags autorisés";
            }
            return true;
        }
    ]);
    
    if (!$data) exit;
    
    $user = middleware_auth();
    
    // Créer le post
    $post = createPost([
        'user_id' => $user['id'],
        'title' => $data['title'],
        'content' => $data['content'],
        'published' => $data['published'],
        'tags' => $data['tags']
    ]);
    
    echo api_response(201, "Post créé avec succès", $post);
    
} catch(Exception $e) {
    echo getError($e);
}
?>
```

---

## 🚦 Le Rate Limiting en Détail

### Qu'est-ce que le Rate Limiting ?

Le **rate limiting** (limitation de taux) consiste à **limiter le nombre de requêtes** qu'un utilisateur peut faire **dans une période donnée**.

**Analogie :**
Comme un **distributeur automatique de billets** qui limite à 3 retraits par jour pour éviter la fraude.

### Pourquoi c'est Important ?

#### 1. Protection contre le Force Brute

**Sans rate limiting :**
```
Attaquant essaie 1 000 000 de mots de passe en 1 heure
→ Compte piraté ❌
```

**Avec rate limiting :**
```
Attaquant bloqué après 5 tentatives
→ Doit attendre 5 minutes entre chaque tentative
→ 1 000 000 essais = 3 472 jours (9,5 ans!)
→ Compte protégé ✅
```

#### 2. Protection contre le DDoS

**Sans rate limiting :**
```
Attaquant envoie 10 000 requêtes/seconde
→ Serveur surchargé
→ Site down pour tout le monde ❌
```

**Avec rate limiting :**
```
Max 60 requêtes/minute par IP
→ Attaquant bloqué automatiquement
→ Site fonctionne normalement ✅
```

#### 3. Protection des Ressources

**Sans rate limiting :**
```
Robot scrap tout votre site
→ 10 000 requêtes en 5 minutes
→ Serveur lent pour les vrais utilisateurs ❌
```

**Avec rate limiting :**
```
Max 100 requêtes/minute
→ Robot ralenti
→ Performances maintenues ✅
```

### Configuration du Rate Limiting

```php
middleware([
    'rate' => [max_requetes, fenetre_secondes]
]);
```

**Exemples de configuration :**

| Route | Config | Usage |
|-------|--------|-------|
| `/api/login` | `[5, 300]` | 5 tentatives par 5 min (force brute) |
| `/api/register` | `[3, 3600]` | 3 inscriptions par heure (spam) |
| `/api/search` | `[100, 60]` | 100 recherches par minute (usage normal) |
| `/api/upload` | `[10, 600]` | 10 uploads par 10 min (bande passante) |
| `/api/posts` (POST) | `[10, 60]` | 10 posts par minute (spam) |
| `/api/posts` (GET) | `[1000, 60]` | 1000 lectures par minute (public) |

### Comment ça Marche Techniquement ?

```
1. Requête arrive
2. Système récupère l'IP
3. Vérifie le fichier cache : /core/cache/rate_limit_IP_ROUTE.json
4. Compte les requêtes dans la fenêtre de temps
5. Si < max : OK, ajouter cette requête
   Si >= max : Bloquer, retourner 429
```

**Fichier cache exemple :**
```json
[1698765432, 1698765445, 1698765459, 1698765471, 1698765489]
```
= 5 timestamps des 5 dernières requêtes

**Avantages :**
- ✅ Pas de base de données requise
- ✅ Rapide (lecture/écriture fichier)
- ✅ Auto-nettoyage des anciennes entrées
- ✅ Par IP et par route

---

## 📦 Fonctions Raccourcies

Pour les cas simples, utilisez les fonctions raccourcies :

```php
// JSON uniquement
$data = middleware_json(['email', 'password']);

// Auth uniquement
$user = middleware_auth();

// Rate limit uniquement
middleware_rate(60, 60); // 60/min
```

---

## 🎨 Patterns Courants

### Pattern 1: API Publique

```php
// Route publique avec rate limit généreux
$data = middleware([
    'rate' => [100, 60]
]);
```

### Pattern 2: Login/Register

```php
// Rate limit strict + validation complète
$data = middleware([
    'rate' => [5, 300],
    'json' => ['email', 'password'],
    'sanitize' => ['email' => 'email'],
    'validate' => function($data) {
        // Validation password, email, etc.
    }
]);
```

### Pattern 3: CRUD Authentifié

```php
// Auth + rate limit modéré
$data = middleware([
    'auth' => true,
    'rate' => [60, 60],
    'json' => ['field1', 'field2']
]);
```

### Pattern 4: Admin

```php
// Auth + role admin
$data = middleware([
    'auth' => true,
    'role' => ['admin'],
    'rate' => [100, 60]
]);
```

---

## 🧪 Tester Vos Middlewares

### Test Rate Limiting

```bash
# Envoyer 10 requêtes rapidement
for i in {1..10}; do
  echo "Request $i:"
  curl http://localhost:8080/api/mon_endpoint
  echo ""
done
```

**Résultat attendu :**
```
Request 1: 200 OK
Request 2: 200 OK
Request 3: 200 OK
Request 4: 200 OK
Request 5: 200 OK
Request 6: 429 Too Many Requests
```

### Test Authentification

```bash
# Sans token
curl http://localhost:8080/api/protected
# → 401 Unauthorized

# Avec token
curl http://localhost:8080/api/protected \
  -H "Authorization: Bearer VOTRE_TOKEN"
# → 200 OK
```

### Test Validation

```bash
# Données manquantes
curl -X POST http://localhost:8080/api/contact \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com"}'
# → 400 Bad Request (champ 'message' manquant)

# Données complètes
curl -X POST http://localhost:8080/api/contact \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","nom":"John","message":"Hello"}'
# → 200 OK
```

---

## ✅ Checklist Middleware

Avant de créer une route, demandez-vous :

```
☐ Est-ce une route publique ou privée ?
   → Si privée : 'auth' => true

☐ Y a-t-il des rôles spécifiques ?
   → Si oui : 'role' => ['admin']

☐ Faut-il protéger contre les abus ?
   → Toujours ! 'rate' => [max, window]

☐ Quels sont les champs JSON requis ?
   → 'json' => ['champ1', 'champ2']

☐ Y a-t-il des champs optionnels ?
   → 'optional' => ['champ' => defaut]

☐ Faut-il nettoyer les données (XSS) ?
   → 'sanitize' => ['champ' => 'type']

☐ Y a-t-il des règles de validation complexes ?
   → 'validate' => function($data) {}
```

---

## 🎓 Résumé Final

**Middleware = Gardien de sécurité de votre API**

**Un seul appel :**
```php
$data = middleware([...]);
if (!$data) exit;
```

**Gère automatiquement :**
- ✅ Rate limiting (protection abus)
- ✅ Authentification JWT
- ✅ Autorisation par rôles
- ✅ Validation JSON
- ✅ Sanitization XSS
- ✅ Validation personnalisée
- ✅ Réponses HTTP standardisées

**Résultat :**
- 📝 Code 70% plus court
- 🛡️ Sécurité maximale
- 🐛 Moins de bugs
- 🚀 Développement rapide

---

**🎉 Vous maîtrisez maintenant les middlewares ! Bon développement ! 🚀**
