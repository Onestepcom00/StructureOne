![strctureOne Logo](./core/github_save/logo.png)

# StructureOne - Architecture PHP

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%252B-777BB4.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

---

## 🚀 Introduction

**StructureOne** est une architecture pensée pour simplifier le développement de projets PHP.  
Elle réduit considérablement le temps de configuration et facilite la gestion dynamique des routes API.  
Le but est d’offrir aux équipes de développement un cadre structuré, clair et évolutif.  

---

## 📂 Structure du projet

Le projet est composé de **6 fichiers principaux** et **1 dossier racine**.  

### Fichiers :

- **index.php**  
  Gère dynamiquement les routes.  
  Ajoutez simplement un dossier dans `/core/routes/NOM_DU_DOSSIER` et le système appellera automatiquement son code pour gérer la route.  
  ➝ Aucun besoin de modifier ce fichier.

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

## 👨‍💻 Auteur

Projet **StructureOne** créé par : **Exauce Stan Malka (Exauce Malumba)**  
Développé pour simplifier la vie des développeurs et répondre aux besoins des équipes ayant atteint une certaine échelle.  

✨ Soutenu par la startup **Kreatyva**, utilisé notamment dans le projet **EdithAI_Personal**.  

📧 Contact : **onestepcom00@gmail.com**  

---

## 📜 Licence

Ce projet est distribué sous licence **MIT**.
