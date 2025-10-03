# StructureOne - Architecture PHP Évolutive

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1.svg?logo=mysql)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![API](https://img.shields.io/badge/API-RESTful-FF6B6B.svg)
![JWT](https://img.shields.io/badge/Security-JWT-32CD32.svg)
![Architecture](https://img.shields.io/badge/Architecture-MVC-9B59B6.svg)

---

## 🚀 Introduction

**StructureOne** est une architecture PHP moderne conçue pour accélérer le développement d'APIs robustes et sécurisées.  
Elle combine simplicité d'utilisation avec des fonctionnalités avancées pour les projets d'entreprise.

**✨ Nouveautés v2.0 :**
- ✅ **Versionning d'API** (`/api/v1/`, `/api/v2/`)
- ✅ **Gestion d'erreurs globale** avec mode debug
- ✅ **Système d'authentification JWT** automatique
- ✅ **Fonctions de base de données** optimisées
- ✅ **Rétrocompatibilité** totale avec l'ancien système


---

## 📂 Structure du projet

Le projet est composé de **6 fichiers principaux** et **1 dossier racine**.  

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

- **.htaccess** et **.env**  
  - `.htaccess` → Crucial pour la réécriture d’URL et la redirection des requêtes vers le routeur.  
  - `.env` → Généré automatiquement lors de l’installation (contient les configurations sensibles).  

### Dossier `/core/` :

- **/routes/** → Contient les dossiers de chaque route API.  
- **/logs/** → Stockage des logs (erreurs, succès, monitoring).  
- **/database/** → Contient les fichiers `.sql` ou `.bdd`.  
- **/uploads/** → Contient les fichiers uploadés (organisez par sous-dossiers : `/uploads/file/`, `/uploads/images/` …).  
- **/cache/** → Contient les caches du système.  

Vous pouvez ajouter d’autres dossiers spécifiques à votre projet (exemple : `/core/templates/`) et les définir dans `config.php` :

```php
define('BASE_TEMPLATES','/core/templates');
```

---

## ⚙️ Installation

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

Créez simplement un dossier `test` dans `/core/routes/` avec :

- `index.php` → contient le code de l’API.  
- `functions.php` → contient les fonctions utiles appelées par `index.php`.  

⚠️ Inutile d’inclure `loader.php` et `config.php`, le routeur s’en charge déjà.  

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

🔧 Mode Debug Intelligent
Sans DEBUG_MODE :

```json
{
    "status": "error",
    "message": "Route non trouvée"
}
```
Avec DEBUG_MODE=true :

```json
{
    "status": "error", 
    "message": "Route 'users' non trouvée",
    "data": {
        "requested_route": "users",
        "version": "v2",
        "debug": {
            "searched_path": "core/versions/v2/users",
            "available_routes": ["v1", "auth", "test"]
        }
    }
}
```

## ⚙️ Configuration

```php
// .env
DEBUG_MODE=true
API_TOKEN_SECRET=votre_cle_super_secrete
API_TOKEN_EXP=3600
```
---

## 🛠️ Fonctions globales

### `loadEnv()`
Charge automatiquement les variables du fichier `.env`.  

### `api_response($status, $message = null, $data = null)`  
Simplifie le retour JSON formaté avec le bon **HTTP status code**.  

Exemple :

```php
echo api_response(200, "Requête réussie", [
    "token" => "example-token"
]);
```

### `env($key)`  
Récupère une variable définie dans `.env` :  

```php
$db_host = env('DB_HOST');
```

---

## 🔒 Nouveautés : Sécurité & Validation

Nous avons récemment ajouté des fonctionnalités de **sécurisation avancée** :  

### 🔑 `jwt_generate($id)` 
- Cette fonction prends en charge un tableau ou un id simple .

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





- Génère rapidement un **token JWT** à partir de l’ID utilisateur.  
- Utilise la clé secrète définie dans `.env` ou `config.php`.  
- Nécessite deux constantes :  
  - `API_TOKEN_SECRET`  
  - `API_TOKEN_EXP` (durée d’expiration).  

### ✅ `jwt_validate($token)`  
- Valide un token JWT existant.  
- Retourne l’**ID** si le token est valide.  
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

Si le token est valide vous aurez une reponse similaire : 
```json
{
    "status": "success",
    "message": "Token valide",
    "jwt_decoded": {
        "uid": "8",
        "exp": 1759446464
    }
}
```

### 🛡️ `validate($data, $rules)`  
- Vérifie strictement les entrées pour éviter les injections arbitraires.  
- Supporte les données venant de `php://input`, `$_POST` ou `$_GET`.  
- Exemple d’utilisation :  

```php
validate($_POST,[
   "username" => "required",
   "password" => "required"
]);
```

Cette fonction empêche efficacement l’envoi de données non conformes.  

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


### 🚀 Améliorations du système d'inscription

### GERER LES METHODES DE REQUETES AUTORISER 

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

Nous avons récemment ajouté un **module complet pour la création sécurisée d'utilisateurs**. Voici les points clés :

- Vérification automatique si l'utilisateur **existe déjà** dans la base de données.
- Gestion des erreurs robustes avec `db_find()` et `db_execute()`.
- Retour JSON clair pour informer le client si l'utilisateur existe ou si la création est réussie.
- Génération automatique d'un **jeton JWT** après création.
- Utilisation des nouvelles fonctions globales `db_connect()`, `db_find()`, `db_execute()`, et `db_last_id()`.
- Gestion du hashage de mot de passe via `db_hash()`.

### 🔍 Exemple rapide d'utilisation

```php
// Créer un nouvel utilisateur
create_user('john', db_hash('motdepasse123'));

// Retour si succès :
// {
//     "status": 201,
//     "message": "Utilisateur créé avec succès",
//     "data": {"jwt_token": "<token>"}
// }

// Retour si utilisateur existant :
// {
//     "status": 409,
//     "message": "L'utilisateur existe déjà",
//     "data": null
// }
```

### 📂 Où trouver le code

Le code de base pour créer un système d'inscription sécurisé est disponible dans :

```
/core/routes/signup/
```

- `index.php` : logique principale pour créer l'utilisateur et générer le JWT.
- `functions.php` : fonctions utilitaires pour la base de données et la validation.

> Vous pouvez consulter ce dossier pour comprendre la logique, réutiliser ou adapter le code pour d'autres routes.

### ⚡ Notes importantes

- Les fonctions globales `db_connect()`, `db_find()`, `db_execute()` et `db_last_id()` assurent maintenant que la connexion à la base de données est vérifiée avant toute requête, ce qui empêche les erreurs critiques si la base de données n'est pas disponible.
- `DEBUG_MODE` dans `.env` est pris en compte pour afficher ou cacher les messages détaillés d'erreur.
- La création d'utilisateur est maintenant **conditionnée par l'existence dans la base** et ne peut plus créer un doublon.

Ces mises à jour permettent de créer rapidement un système d'inscription sécurisé et plug-and-play dans StructureOne.



---

## 🔄 Mise à jour des routes

Pour ajouter une nouvelle API :  
1. Créez un dossier dans `/core/routes/` au nom de la route.  
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
$user = require_auth_role(['admin', 'superadmin']);

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

## 🔮 Futures Améliorations
### 🚧 En Développement
-Middleware avancé pour la validation des données
- Rate limiting et protection contre les attaques DDoS
- Cache Redis intégré pour les performances
- Documentation API auto-générée (OpenAPI/Swagger)
- Tests automatisés avec PHPUnit
- Container Docker pour le déploiement

### 💡 Idées en Réflexion
- WebSocket pour les applications temps réel
- GraphQL en alternative à REST
- Microservices avec communication inter-APIs
- Monitoring en temps réel avec métriques

---
### 👨‍💻 Auteur & Contribution
StructureOne est créé et maintenu par Exauce Stan Malka (Exauce Malumba)

💼 Utilisé en production par :

Kreatyva - Plateforme de création digitale
EdithAI_Personal - Assistant IA personnel
Plusieurs Autres projet

📧 Contact : onestepcom00@gmail.com
🐛 Issues & Contributions : Les PR sont les bienvenues !

---

### 📜 Licence

Ce projet est distribué sous licence **MIT**.


