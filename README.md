# ![StructureOne Logo](/core/github_save/StructureOne.jpeg)



<p align="center">
  <img src="https://img.shields.io/badge/Version-2.1.1+-blueviolet?style=for-the-badge&logo=github" />
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php" />
  <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql" />
  <img src="https://img.shields.io/badge/License-MIT-success?style=for-the-badge&logo=open-source-initiative" />
  <img src="https://img.shields.io/badge/API-RESTful-FF6B6B?style=for-the-badge&logo=postman" />
  <img src="https://img.shields.io/badge/Security-JWT-32CD32?style=for-the-badge&logo=auth0" />
  <img src="https://img.shields.io/badge/🏆-Meilleur%20Mini%20Framework%20API-orange?style=for-the-badge" />
  <img src="https://img.shields.io/badge/🔥-Classé%20N°1%20des%20MicroFrameworks-critical?style=for-the-badge" />
</p>

<p align="center"><img src="https://trendshift.io/api/badge/repositories/13425" alt="Trendshift" style="width: 250px; height: 55px;" width="250" height="55"/></p>


---

## 🚀 Introduction

**StructureOne** est une architecture PHP moderne et universelle, conçue pour être **compatible avec n’importe quel serveur**, que ce soit **Apache ou Nginx**.  
Grâce à sa nouvelle conception, le framework détecte automatiquement l’environnement serveur et s’adapte sans configuration manuelle.
Cette compatibilité universelle a été développée pour garantir une **installation fluide, rapide et sécurisée**, quel que soit l’hébergeur ou l’environnement.

---

## ✨ Fonctionnalités Clés

- 🔧 **Versioning d'API** : support natif pour `/api/v1/`, `/api/v2/`  
- 🧠 **Compatibilité multi-serveurs** : Apache & Nginx  
- 🛡️ **Sécurité avancée** : protection automatique des fichiers sensibles  
- 🧩 **Support Composer** : StructureOne a été conçue pour être compatible avec Composer 
- 🧰 **Gestion des erreurs et du debug** intégrée  
- ♻️ **Rétrocompatibilité garantie** avec les versions précédentes  
- 🎨 **Interface HTML stylée** : Pages d'erreur et d'accueil professionnelles (comme Laravel/Symfony)
- ⚡ **Variables automatiques** : Partage automatique entre fichiers, sans besoin de `global`
- 📝 **Logging avancé** : Traçabilité complète avec timestamps

StructureOne vise à évoluer continuellement. Pour les utilisateurs en production, chaque mise à jour restera compatible avec vos versions précédentes, sauf correctifs de sécurité critiques.


---

## 📂 Structure du projet

LLe projet est composer de plusieurs fichiers :

```
my-project/
├── core/
│   ├── routes/         # API route handlers
│   ├── database/       # Database migrations and seeds
│   ├── logs/           # Application logs
│   ├── versions/       # API route avec versions
│   ├── cache/          # Cache files
│   └── uploads/        # Uploaded files
├── vendor              # Les dependances au cas ou vous utilisez composer
├── .env                # Environment variables
├── .htaccess           # La configuration serveur Apache
├── index.php           # Le routeur
├── config.php          # Les configuartions manuelle
├── laoder.php          # Toute les fonctions globaux
```

### Fichiers :

- **index.php**  
  Gère dynamiquement les routes.  
  Ajoutez simplement un dossier dans `/core/routes/NOM_DU_DOSSIER` et le système appellera automatiquement son code pour gérer la route.  
  ➝ Aucun besoin de modifier ce fichier.
  ➝ Peut desormais prendre en charge des API par version depuis le dossier `/core/versions/v1/NOM_DU_DOSSIER` , la meme logique est garder.

- **config.php**  
  Contient toutes les **configurations globales** et importantes.  
  Utilisez **des constantes** plutôt que des variables afin d’éviter des conflits.

- **loader.php**  
  Contient toutes les **fonctions globales**.  
  ➝ Ajoutez ici vos nouvelles fonctions réutilisables (connexion BDD, helpers, etc.).  
  ⚠️ Ne supprimez pas les fonctions par défaut sauf en cas de nécessité.

- **install.py** et **install.js**  
  Scripts d’installation du projet (au choix Python ou Node.js).  
  Une fois l’installation terminée, vous pouvez supprimer ces fichiers.

- **.htaccess** ,  **.env** et **nginx.conf.example**  
  - `nginx.conf.example` → Crucial car il est le fichier d'example pour la configuration Nginx
  - `.htaccess` → Crucial pour la réécriture d’URL et la redirection des requêtes vers le routeur.  
  - `.env` → Généré automatiquement lors de l’installation (contient les configurations sensibles).  

Par defaut vous n'eetes pas obliger d'ajouter la configuration Nginx sauf si cela vous est utile.

> Pour eviter d'exposer votre fichier d'environnement , veuillez ouvrir le fichier du routeur (index.php) puis allez a la ligne 107 , mettez le chemin vers votre nouvelle emplacement du fichier .env

### Dossier `/core/` :

- **/routes/** → Contient les dossiers de chaque route API.  
- **/versions** → Contient les dossiers par versions des routes , `/versions/NOM_DE_LA_VERSIO/NOM_DE_LA_ROUTE` (ex : `/versions/v1/users_info`)
- **/logs/** → Stockage des logs (erreurs, succès, monitoring).  
- **/database/** → Contient les fichiers `.sql` ou `.bdd`.  
- **/uploads/** → Contient les fichiers uploadés (organisez par sous-dossiers : `/uploads/file/`, `/uploads/images/` …).  
- **/cache/** → Contient les caches du système.  


> Vous etes libre de personnaliser vos dossiers , sauf les dossiers ci-haut , créez autant des dossiers que vous voulez soyez libre , nous vous imposons rien , au cas ou vous voulez que vos dossiers deviennet des parametres du systemes alors ouvrez le fichier config.php ajoutez son chemin comme constante

```php
define('BASE_TEMPLATES','/core/templates');
```

---

## ⚙️ Installation

Lorsque vous clonez le projet depuis github , vous ne serez pas obliger d'utiliser les installateurs car ceux-ci sont rarement mis en jour , nous vous recommandons de passer directement a l'etape suivante.


### Via Node.js

Assurez-vous d’avoir installé Node.js et ses dépendances :

```js
const fs = require('fs');
const path = require('path');
const readline = require('readline');
```

Puis lancez :

```bash
node install.js
```

### Via Python 3

Assurez-vous d’avoir Python 3 et ces dépendances :

```python
import os
import re
import datetime
import sys
```

Puis lancez :

```bash
python3 install.py
```

---

## 🌐 Gestion des routes

Les routes API sont automatiquement gérées.  
Exemple : route `/api/test`  

Créez simplement un dossier `test` dans `/core/routes/` ou `core/versions/VOTRE_VERSION/` avec vos fichiers PHP.

✅ **Chargement automatique de tous les fichiers** : Le routeur charge automatiquement TOUS les fichiers `.php` présents dans le dossier de la route :
- `functions.php` est chargé en premier (si présent)
- Les autres fichiers sont chargés par ordre alphabétique
- `index.php` est chargé en dernier

⚠️ Inutile d'inclure `loader.php` et `config.php`, le routeur s'en charge déjà.  

> StructureOne gère automatiquement le chargement de tous vos fichiers, plus besoin de les inclure manuellement !

### 🚫 Variables réservées - IMPORTANT

Pour éviter les conflits, **n'utilisez JAMAIS** les variables suivantes dans vos routes :
- Toutes les variables préfixées par `$_so_` (réservées au système)
- `$GLOBALS['JWT_HTTP_TOKEN']` (à lire uniquement, ne pas écraser)

**✅ Bonnes pratiques de nommage :**
```php
// ✅ BON - Noms descriptifs et contextualisés
$userData = $_POST['data'];
$dbResult = db_find(...);
$apiResponseData = [...];

// ❌ MAUVAIS - Noms trop génériques ou réservés
$data = $_POST['data'];
$_so_routeName = "test"; // INTERDIT !
```

📚 Consultez `/core/routes/exemple_bonnes_pratiques/` pour un exemple complet.

### Exemple de réponse `/api/test` :

```json
{
    "status": "success",
    "message": "L'api fonctionne",
    "role": "cette api n'est qu'un exemple qui va renvoyer les cordonnees du fichier d'env",
    "env": {
        "DB_HOST": "localhost"
    }
}
```
---

## 🐛 Gestion des Erreurs

Lorsque vous creez votre  route , n'oubliez pas d'ajouter la fonction `getError()` si vous utilisez une exception , et cela va activer ou desactiver automatiquement les erreurs si vous etes en production.
Sans DEBUG_MODE :

```php

try {
    // Votre code ici 
} catch (Exception $e) {
   // A L'execption 
    echo getError($e);
}

```

Si certains de nos fonctions de base ne tiennent pas a vos besoin comme par exemple la fonction `getError` alors , vous pouvez ajouter cette suite de code :

```php

try{
   // Votre code ici
} catch (Exception $e) {
    // Gestion des erreurs inattendues
    error_log("ERROR TITLE " . $e->getMessage());
    
    $debug_info = null;
    if (env('DEBUG_MODE') === 'true') {
        $debug_info = [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
    
    echo api_response(500, null, $debug_info);
}

```

Les deux codes ci-haut font carrement la meme chose.

## ⚙️ Configuration

```php
// .env
DEBUG_MODE=true
API_TOKEN_SECRET=votre_cle_super_secrete
API_TOKEN_EXP=3600
```
---

## 🛠️ Fonctions globales

Les fonctions globals , sont des fonctions déjà mis en place pour vous aidez à aller plus vite , vous n'etes pas conditionner de les utiliser , ses fonctions repondent a des besoins varier.


### Renvoyer une reponse a la sortie de votre API

Pour eviter a chaque fois d'ecrire un bloc de code repetif : 

```php
// Exemple de tableau 
$VOTRE_TABLEAU = [
  "status" => "success",
  "message" => "Utilisateur inscrit avec success",
  "data" => ["uid" => 1]
];
// Renvoyer un code 
http_respone_code(200);
// Renvoyer une reponse
echo json_encode($VOTRE_TABLEAU,true);
exit;
```

Ce travail pourrait etre très fatiguant , surtout lorsque vous avez beaucoup des routes a gérez , alors pour ce faire nous avons mis en place une fonction bien speciale , pour resoudre ce probleme.

```php
api_response($status, $message = null, $data = null)
```  

Cette fonction simplifie la sortit des reponses JSON , gère les caractères pour eviter les erreurs de caracters **Unicode** et bien plus.
Cette fonction prends en entré 3 valeurs : 

- `$status` : Il s'agit du status de la reponse , precedement mis dans `http_response_code(200)` , ici ce parametre prends en charge tout type de code d'erreur.
- `$message` : ce parametre est **optionnel** , c'est juste le message qui sortira a la reponse , precedement `Utilisateur inscrit avec success` , ici au cas ou vous mettez rien , il va mettre lui meme la reponse correspondant a l'erreur , par exemple 404 correspond a **Not Found**
- `$data` : Ceci est aussi **optionnel** , prends en entré un `string` ou un `tableau` , precedement `"data" => ["uid" => 1]`.

Exemple D'utilisation :

```php
echo api_response(200, "Requête réussie", [
    "token" => "example-token"
]);
```

### Recuperer un element existant dans le fichier d'environnement
Nous avons mis en place une fonction qui permet de recuperer directement un element venant du fichier d'environnement :  

```php
$db_host = env('DB_HOST');
```
 

### Generer un jeton JWT
 
Il existe une fonction pour generer un jeton JWT , fonctionnel , cette fonction prends en entré un `string` ou un `tableau`.

```php
<?php
/** Premier cas **/
// Génération du token
$userData = [
    'id' => 123,
    'name' => 'John Doe',
    'email' => 'john@example.com'
];
// Va generer un token en fonction du tableau
$token = jwt_generate($userData);

/** Deuxieme cas **/
$id = 1; // Par exemple on suppose que c'est l'id 1 qu'on veux renvoyer en token 
$myToken = jwt_generate($id);
?>
```
> Il est important d'utiliser la meme methode pour decoder votre jeton JWT 

### Verifier et Valider un jeton JWT
- Valide un token JWT existant.  
- Vérifie rigoureusement l’expiration et la validité du token.  
- Utilise également `API_TOKEN_SECRET` et `API_TOKEN_EXP`.  

Pour verifier si un token est valide , vous n'aurez qu'a entrer le token :

```php
<?php

// exemple de token 
$token = "eyJ1aWQiOiI4IiwiZXhwIjoxNzU5NDQ2NDY0fQ.NTY2NDc4ZjljMmE4OTEzYzgwZGMyYTM5MjkzODE2YTdiY2QxMWEwNDA2OTRjNjljOGVkM2VmNGQyMGJhYWViNA";

// Decoder 
$decoded = jwt_validate($token);

// Verifier si le token est valide
if($decoded){
  // creer une response a renvoyer dans la requete 
  $response = [
    "jwt_decoded" => $decoded
  ];

  // Renvoyer la reponse (en API)
  echo api_response(200,"Token valide",$response);
}else{
  // LE token est invalide 
  echo api_response(401,"Token Invalide",null);
}
?>
```

  

### 🌍 Gestion automatique du header `Authorization`  

Dans `config.php`, une variable spéciale est ajoutée :  

```php
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$JWT_HTTP_TOKEN = str_replace('Bearer ','',$authHeader);
```

- Cette variable `$JWT_HTTP_TOKEN` contient automatiquement le token transmis dans le header.  
- Pour l’utiliser dans une fonction :  

```php
function test(){
   global $JWT_HTTP_TOKEN;

   $decoded = jwt_validate($JWT_HTTP_TOKEN);

   if($decoded){
       // Le token est valide
   }else{
       // Le token est invalide
   }
}
```

👉 Cette pratique rend l’API **très sécurisée** et simple à maintenir. 


### Gérer les methodes des requetes entrant

Nous avons enormement simplifier les choses en ajoutant des fonctions globaux , pour vous permettre de designer les methodes autoriser dans vos routes.
nous avons ajouter la fonction `require_method()` et `require_method_in`
la premiere fonction prends en charge uniquement une seule methode et la deuxieme peut prendre en charge plusieurs methode.

**EXEMPLE 1:**
```php

/**
 * EXEMPLE 1: Méthode POST requise
 * Si la méthode n'est pas POST, le script s'arrête immédiatement
 */
require_method('POST');

// Le code suivant ne s'exécutera JAMAIS si la méthode n'est pas POST
echo db_escape($_GET['test']); // ← Jamais exécuté en cas d'erreur
```
**EXEMPLE 2:**

```php
/**
 * EXEMPLE 2: Méthodes multiples autorisées
 */
require_method_in(['GET', 'POST']);

// Le code suivant ne s'exécutera que si la méthode est GET ou POST
$data = $_GET['id'] ?? $_POST['id'] ?? null;
echo api_response(200, "Succès", $data);
```

**EXEMPLE 3:**

```php
/**
 * EXEMPLE 3: Avec réponse d'erreur personnalisée
 */
require_method('POST', function() {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Méthode non autorisée pour cette action'
    ]);
    // Pas besoin de exit ici, il est déjà dans la fonction require_method
});

```
#### BASE DES DONNEES : 

Nous avons mis en place un certain nombre des fonctions qui permet d'effectuer des operations SQL (actuellement uniquement MySQL pris en charge).

1.**Rechercher un exemple dans la base des donnees** `db_find()`:

Cette fonction prends en entrer deux elements , la requete SQL comme premier element , puis les elements a verifier comme deuxieme element .

```php
<?php
    // variable 
    $element_a_verifier = "mon_mail"; // dans la colonne *admin_email* dans la table *admin* pour mon cas
    /**
     * 
     * verifier si l'utilisateur existe deja 
     * 
     */
    $admin_exist = db_find("SELECT * FROM admin WHERE admin_email = ?",[
        $element_a_verifier
    ]);

    /**
     * 
     * Verifier si l'admin existe 
     * 
     */
    if($admin_exist){
        /**
         * 
         * Renvoyer une erreur car l'admin existe deja 
         * 
         */
        echo api_response(409,"Cette adresse mail existe deja",null);
        return; // Pour bloquer la suite de l'execution
    }else{
      /** Element existant dans la base des donnees **/
    }
?>
```

2.**Executer la requete SQL** : `db_execute()`

Cette fonction prends en entrer deux , la requete SQL et les donnees e inserer .

```php
<?php
  /**
  * 
  * Creer l'admin :  Adaptez ce code selon votre base des donnees et selon vos besoins
  * 
  */
  db_execute("INSERT INTO admin (admin_name,admin_email,admin_password) VALUES (?,?,?)",[
      $_admin['name'],$_admin['email'],$_admin['password']
  ]);
?>
```

3.**Recuperer le dernier id enregistrer** : `db_last_id()`

Cette operation doit etre traiter apres , une requete SQL comme vous auriez pu le faire d'habitude.

```php
<?php
// Operation SQL recente 
// Recuperer le dernier id
 $_last_id = db_last_id();
?>
```

4.**Hasher le mot de passe** : `db_hash()`

Cette fonction prends en entrer un element *string* puis le convertit en *hash* pour un mot de passe 

```php
<?php
// String 
$password = "hello";
// hasher 
echo db_hash($password);
?>
```

5.**Filtrer les entrees** : `db_escape()`

cette fonction permet de filtrer les entrees pour se proteger contre les attaques XSS

```php
<?php
// Element a filtrer : cas d'une attaque XSS 
$form = "<h1>Mon nom</h1>";
// Affcher l'element filtrer
echo db_escape($form);
?>
```

6.**Verifier un element sur la base des donnees (version courte)** : `db_element_exist()`

Il existe aussi une version plus courte pour verifier l'existance d'un element dans la base des donnees

```php
<?php
/** Verifier la methode autoriser **/
require_method('GET');

/** Verifier si l'adresse mail existe dans la table *admin* , verifier l'element dans la colonne *admin_email* adaptez cela en fonction de vos besoins **/
if(db_element_exist('admin',['admin_email' => 'test@gmail.com'])){
    echo api_response(200,"Utilisateur existe",null);
}else{
    echo api_response(404,"Utilisateur non existant",null);
}

?>
```




> Referez vous a ces exemples de code pour voir comment vous pouvez utilisez ses fonctions.

---

## 🔄 Mise à jour des routes

Pour ajouter une nouvelle API :  
1. Créez un dossier dans `/core/routes/` au nom de la route ou dans `/core/versions/VERSION_DE_ROUTE/`.  
2. Ajoutez vos fichiers `index.php` et `functions.php`.  
3. Le système détectera automatiquement cette route.  

Si la mise à jour ajoute de nouvelles fonctions globales ou configurations, pensez à les placer dans :  
- `config.php`  
- `loader.php`  
- `.env` (si nécessaire)  

---
## 📚 Exemples Complets
### 🔐 Route d'Authentification
```php
<?php
// /core/routes/auth/login/index.php

require_method('POST');

$input = json_decode(file_get_contents('php://input'), true);

// Validation
if (empty($input['email']) || empty($input['password'])) {
    echo api_response(400, "Email et mot de passe requis");
    exit;
}

// Vérification des identifiants
$user = db_find("SELECT * FROM users WHERE email = ?", [$input['email']]);

if ($user && password_verify($input['password'], $user['password'])) {
    // Génération du token
    $tokenData = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
    
    $token = jwt_generate($tokenData);
    
    echo api_response(200, "Connexion réussie", [
        'user' => $tokenData,
        'token' => $token,
        'expires_in' => 3600
    ]);
} else {
    echo api_response(401, "Identifiants invalides");
}
?>
```

### 📊 Route de Dashboard Protégée
```php
<?php
// /core/versions/v1/admin/dashboard/index.php

require_method('GET');

// Authentification et rôle admin requis


// Récupération des statistiques
$stats = [
    'total_users' => db_count("SELECT COUNT(*) FROM users"),
    'active_users' => db_count("SELECT COUNT(*) FROM users WHERE active = 1"),
    'recent_signups' => db_select("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5")
];

echo api_response(200, "Dashboard administrateur", [
    'user' => $user,
    'stats' => $stats
]);
?>
```

---

## 🧱 Système de Middleware Ultra-Simplifié (v2.1.1+)

### 🎯 Introduction

StructureOne intègre un système de middleware **révolutionnaire** : **UNE SEULE fonction** pour TOUS vos besoins de validation et sécurité !

**📚 Guide Complet :** Consultez [`GUIDE_MIDDLEWARE_COMPLET.md`](GUIDE_MIDDLEWARE_COMPLET.md) pour un cours détaillé avec exemples.

### ⚡ Usage Ultra-Simple

**Principe :** Une seule fonction `middleware()` gère TOUT :

```php
// Validation simple
$data = middleware(['json' => ['email', 'password']]);
if (!$data) exit;

// Complet (auth + rate limit + validation + sanitization)
$data = middleware([
    'rate' => [10, 60],              // Rate limiting
    'auth' => true,                   // Authentification JWT
    'role' => ['admin'],              // Autorisation
    'json' => ['title', 'content'],   // Champs requis
    'sanitize' => ['title' => 'string'], // Nettoyage XSS
    'validate' => function($data) {   // Validation custom
        return strlen($data['title']) >= 3 ? true : "Titre trop court";
    }
]);
if (!$data) exit;
```

**Résultat :** Code **70% plus court**, sécurité **maximale** ! ✨

### 📋 Options Disponibles

La fonction `middleware()` accepte ces options :

| Option | Type | Description | Exemple |
|--------|------|-------------|---------|
| `json` | `array` | Champs JSON requis | `['email', 'password']` |
| `optional` | `array` | Champs optionnels avec défaut | `['remember' => false]` |
| `auth` | `bool` | Authentification JWT requise | `true` |
| `role` | `array` | Rôles autorisés | `['admin', 'moderator']` |
| `rate` | `array` | [max requêtes, secondes] | `[10, 60]` = 10/min |
| `sanitize` | `array` | Nettoyage automatique | `['email' => 'email']` |
| `validate` | `callable` | Fonction de validation custom | `function($data) {}` |

### 🎯 Exemples d'Utilisation

#### Exemple 1 : Route Publique Simple

```php
<?php
require_method('POST');

// Validation JSON avec rate limit
$data = middleware([
    'rate' => [100, 60],  // 100 requêtes/minute
    'json' => ['email', 'message']
]);
if (!$data) exit;

sendContactEmail($data['email'], $data['message']);
echo api_response(200, "Message envoyé");
?>
```

#### Exemple 2 : Route avec Authentification

```php
<?php
require_method('GET');

// Auth + rate limit
$data = middleware([
    'auth' => true,
    'rate' => [60, 60]
]);
if (!$data) exit;

$user = middleware_auth(); // Données du token JWT
$profile = getUserProfile($user['id']);
echo api_response(200, "Profil", $profile);
?>
```

#### Exemple 3 : Route Admin Complète

```php
<?php
require_method('POST');

// TOUT : rate limit + auth + role + validation + sanitization
$data = middleware([
    'rate' => [20, 60],
    'auth' => true,
    'role' => ['admin'],
    'json' => ['title', 'content'],
    'sanitize' => [
        'title' => 'string',
        'content' => 'string'
    ],
    'validate' => function($data) {
        if (strlen($data['title']) < 3) {
            return "Titre trop court";
        }
        return true;
    }
]);
if (!$data) exit;

$post = createPost($data);
echo api_response(201, "Post créé", $post);
?>
```

### ⚡ Fonctions Raccourcies

Pour les cas simples :

```php
// JSON uniquement
$data = middleware_json(['email', 'password']);

// Auth uniquement
$user = middleware_auth();

// Rate limit uniquement
middleware_rate(60, 60); // 60 requêtes/minute
```

### 🎓 Exemple Complet : Route CRUD Posts

Consultez [`/core/routes/posts/index.php`](core/routes/posts/index.php) pour un exemple complet avec :
- ✅ CRUD complet (GET, POST, PUT, DELETE)
- ✅ Rate limiting adapté par méthode
- ✅ Authentification
- ✅ Autorisation admin
- ✅ Validation complète
- ✅ Sanitization

### 📚 Documentation Complète

**Pour apprendre en détail :**
- **[GUIDE_MIDDLEWARE_COMPLET.md](GUIDE_MIDDLEWARE_COMPLET.md)** - Cours complet sur les middlewares et rate limiting
- **[/core/routes/posts/](core/routes/posts/)** - Exemple CRUD complet

**Le guide couvre :**
- 📖 Qu'est-ce qu'un middleware ? (avec analogies)
- 🎯 Pourquoi utiliser des middlewares ?
- ⚡ Utilisation étape par étape (7 niveaux)
- 🚦 Rate limiting en détail et son importance
- 🎨 Patterns courants
- 🧪 Tests et debugging
- ✅ Checklist complète

---

## 🔍 Détection Automatique de Conflits (MODE DEBUG)

En mode DEBUG, StructureOne détecte automatiquement les conflits de variables pour vous aider au debugging.

### Activer le Mode DEBUG

```env
# .env
DEBUG_MODE=true
```

### Détecter les Conflits

```php
<?php
// Automatique: les conflits sont loggés
$conflicts = debug_detect_variable_conflicts();

// Afficher le rapport
debug_show_conflicts_report();

// Voir toutes les variables partagées
$vars = debug_get_shared_variables();
print_r($vars);
?>
```

### Utiliser set_safe() pour Prévenir les Écrasements

```php
<?php
// Définir une variable
set_safe('userName', 'John'); // ✅ OK

// Tentative d'écrasement (détectée en DEBUG)
set_safe('userName', 'Jane'); // ⚠️ WARNING dans les logs, non écrasé

// Forcer l'écrasement
set_safe('userName', 'Jane', true); // ✅ OK, forcé
?>
```

### Types de Conflits Détectés

1. **Noms similaires** (>80% similarité)
   - Exemple: `userName` vs `user_name`
   - Risque de confusion

2. **Même valeur** (duplication potentielle)
   - Exemple: `$config1 = ['timeout' => 30]` et `$config2 = ['timeout' => 30]`
   - Possible redondance

### Exemple de Log

```
=== CONFLITS DE VARIABLES DÉTECTÉS ===
[similar_names] userName <-> user_name : Noms très similaires, risque de confusion
[same_value] config <-> settings : Même valeur, possible duplication
```

---

## 🔧 Nouvelles fonctionnalités (v2.1.1+)

### ✅ Chargement automatique de tous les fichiers
**RÉSOLU** ✔️ - Le système charge maintenant automatiquement TOUS les fichiers PHP d'une route (pas seulement `index.php` et `functions.php`)

### ✅ Variables Partagées Ultra Simplifiées
**NOUVEAU** ✨ - Fonctions `set()` et `get()` pour partager facilement des variables entre fichiers !

**Dans `functions.php` - Définir les variables :**
```php
// ✅ ULTRA SIMPLE avec set()
set('name', 'StructureOne');
set('version', '2.1.1');
set('config', ['timeout' => 30, 'retries' => 3]);
```

**Dans `index.php` - Utiliser les variables :**
```php
// ✅ ULTRA SIMPLE avec get()
$name = get('name');
$config = get('config');

echo api_response(200, "Projet: $name", $config);
```

**Fonctions disponibles :**
- `set($nom, $valeur)` - Définir une variable partagée
- `get($nom, $defaut)` - Récupérer une variable (avec valeur par défaut)
- `has($nom)` - Vérifier si une variable existe
- `setMany(['nom' => 'valeur', ...])` - Définir plusieurs variables

**Exemple complet :**
```php
// core/routes/users/functions.php
<?php
// Définir plusieurs variables
set('tableName', 'users');
set('perPage', 20);
setMany([
    'allowedRoles' => ['admin', 'user'],
    'cacheEnabled' => true
]);

// Fonction helper
function users_validate($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>

// core/routes/users/index.php
<?php
require_method('GET');

try {
    // Récupérer les variables
    $table = get('tableName');
    $limit = get('perPage');
    $roles = get('allowedRoles', []);
    
    // Vérifier si une variable existe
    if (has('cacheEnabled')) {
        // Utiliser le cache...
    }
    
    // Utiliser la fonction
    $isValid = users_validate('test@example.com');
    
    $users = db_find("SELECT * FROM {$table} LIMIT ?", [$limit]);
    echo api_response(200, "Users list", $users);
} catch(Exception $e) {
    echo getError($e);
}
?>
```

### ✅ Pages HTML Professionnelles (inspirées Laravel/Symfony)
**NOUVEAU** 🎨 - Interfaces dark mode sobres et élégantes

**Activer dans `.env` :**
```env
DEBUG_MODE=true              # Activer le mode debug
ERROR_DISPLAY_HTML=true      # Page d'erreur HTML stylée
HOMEPAGE_DISPLAY_HTML=true   # Page d'accueil HTML stylée
```

**Page d'Accueil (`/`) :**
- Design sobre et professionnel (fond noir pur #000)
- Informations système (version, debug, serveur)
- Exemples d'utilisation des routes
- Versions API disponibles
- Pas d'animations distrayantes
- 100% Responsive

**Page d'Erreur :**
- Stack trace détaillée et lisible
- Extrait de code avec ligne d'erreur
- Suggestions de résolution
- Informations de debug complètes
- Design cohérent avec la homepage

**⚠️ Important :** Les pages HTML ne s'affichent QUE si activées dans `.env`. Sinon, réponse JSON classique.

### ✅ Protection contre les conflits de variables
**RÉSOLU** ✔️ - Toutes les variables système utilisent maintenant le préfixe `$_so_` pour éviter les conflits avec vos routes

**Liste des variables système protégées :**
- `$_so_requestUri` - URI de la requête
- `$_so_requestMethod` - Méthode HTTP
- `$_so_routeName` - Nom de la route
- `$_so_routeInfo` - Informations de version
- `$_so_loadResult` - Résultat du chargement
- Et toutes les variables internes du loader

**Comment éviter les conflits :**
```php
// ✅ BON - Variables descriptives et contextualisées
$userData = json_decode(file_get_contents('php://input'), true);
$dbResult = db_find("SELECT * FROM users WHERE id = ?", [$userId]);
$apiResponseData = ['success' => true];

// ❌ MAUVAIS - Variables génériques qui peuvent causer des problèmes
$data = $_POST;
$result = db_find(...);
$response = [];

// ❌ INTERDIT - Variables réservées au système
$_so_routeName = "test"; // NE JAMAIS FAIRE ÇA !
```

📚 **Exemple complet** : Consultez `/core/routes/exemple_bonnes_pratiques/` pour voir toutes les bonnes pratiques en action.

---

## ⚙️ Futures Mises à Jour

- 🧱 **Middleware avancé** pour la validation et la sécurité  
- 🧠 **Gestion du cache avec Redis**  
- 🐳 **Déploiement simplifié avec Docker**  
- 🧪 **Tests automatisés avec PHPUnit** avec Composer
- 🔍 **Détection automatique des conflits de variables** en mode DEBUG

---

### 👨‍💻 Auteur & Contribution
StructureOne est créé et maintenu par Exauce Stan Malka (Exauce Malumba) <br>

> Vous pouvez soutenir le createur de ce projet avec un petit café , cela nous encourage enormement a continuer a vous offrir le meilleur de nous
<br>

[![Support via Donation](https://img.shields.io/badge/Donate-Here-ff69b4?logo=heart&style=for-the-badge)](https://jxzmkdpz.mychariow.shop/donation)


💼 Utilisé en production par :

Kreatyva - Plateforme de création digitale
EdithAI_Personal - Assistant IA personnel
Plusieurs Autres projet

📧 Contact : onestepcom00@gmail.com
🐛 Issues & Contributions : Les PR sont les bienvenues !

---

### 📜 Licence

Ce projet est distribué sous licence **MIT**.


---

*Dernière mise à jour : 22/10/2025*

---

**📝 Changelog v2.1.1+ (31 Octobre 2024):**

**✨ Nouvelles Fonctionnalités:**
- `set()` et `get()` - Partage ultra simple de variables entre fichiers
- `has()` - Vérifier l'existence d'une variable
- `setMany()` - Définir plusieurs variables en une fois
- Pages HTML professionnelles dark mode (homepage + error page)
- Design sobre et élégant inspiré de Laravel/Symfony
- Activation conditionnelle des pages HTML via `.env`

**🧱 Système de Middleware Avancé:**
- `middleware_validate_json()` - Validation automatique JSON avec champs requis/optionnels
- `middleware_require_auth()` - Authentification JWT simplifiée
- `middleware_require_role()` - Gestion des permissions et rôles
- `middleware_validate_email()` - Validation d'email avec vérification DNS
- `middleware_sanitize()` - Nettoyage automatique des données (XSS, injection)

**🚦 Rate Limiting Intégré:**
- `rate_limit()` - Limitation simple par IP (ex: 60 req/min)
- `rate_limit_advanced()` - Configuration personnalisée par route
- Headers HTTP standard (X-RateLimit-*, Retry-After)
- Stockage en cache fichier (pas de BDD requise)
- Protection contre brute force et DDoS

**🔍 Détection Automatique de Conflits (DEBUG):**
- `debug_detect_variable_conflicts()` - Détection de noms similaires
- `debug_show_conflicts_report()` - Rapport dans les logs
- `set_safe()` - set() avec avertissement si conflit
- `debug_get_shared_variables()` - Liste toutes les variables partagées
- Logs automatiques des conflits potentiels

**🛡️ Améliorations:**
- Gestion d'erreurs améliorée avec stack trace complète
- Logging automatique avec timestamps
- Support codes HTTP personnalisés (400, 401, 403, 404, 500...)
- `logMessage()` pour logs personnalisés
- Protection des variables système (préfixe `$_so_`)
- loadEnv() appelé en premier pour garantir disponibilité des variables

**🎨 Design:**
- Page d'accueil sobre (fond noir #000, pas d'animations)
- Page d'erreur détaillée avec suggestions
- 100% Responsive (mobile, tablet, desktop)
- Logo uniquement en header (design épuré)

**📚 Documentation:**
- Exemples complets d'utilisation set()/get()
- Guide des bonnes pratiques
- Documentation middlewares complète
- Route d'exemple `/api/exemple_middlewares`
- Documentation .env mise à jour

*Dernière mise à jour : 31/10/2024*

---
