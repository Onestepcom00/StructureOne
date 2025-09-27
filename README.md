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

