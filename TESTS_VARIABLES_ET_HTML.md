# 🧪 Guide de Tests - Variables & Pages HTML

## ⚙️ ÉTAPE 1: Configuration du fichier .env

**IMPORTANT:** Créez ou modifiez `d:\programmes\wamp64\www\ollama\structureone\.env`

```env
DEBUG_MODE=true
ERROR_DISPLAY_HTML=true
HOMEPAGE_DISPLAY_HTML=true
```

---

## 🧪 ÉTAPE 2: Tests des Variables/Fonctions

### Test 1: Route Legacy `/api/test`

**Fichiers:**
- `core/routes/test/functions.php` - Définit `$name = "structureOne"`
- `core/routes/test/index.php` - Utilise `$name`

**Test:**
```bash
curl http://localhost/structureone/api/test
```

**Résultat attendu:**
```json
{
    "status": "success",
    "message": "L'API sans version fonctionne bien, i us structureOne"
}
```

✅ Si vous voyez "structureOne" → **LES VARIABLES FONCTIONNENT !**

---

### Test 2: Route Versionnée `/api/v1/test`

**Fichiers:**
- `core/versions/v1/test/functions.php` - Définit `$hook = "test"` + fonction `getHello()`
- `core/versions/v1/test/index.php` - Utilise `$hook` et `getHello()`

**Test:**
```bash
curl http://localhost/structureone/api/v1/test
```

**Résultat attendu:**
```json
{
    "status": "success",
    "message": "Hello from v1 and test"
}
```

✅ Si vous voyez "Hello from v1 and test" → **VARIABLES + FONCTIONS FONCTIONNENT !**

---

## 🎨 ÉTAPE 3: Tests des Pages HTML

### Test 3: Page d'Accueil HTML

**Prérequis:** `HOMEPAGE_DISPLAY_HTML=true` dans `.env`

**Test:**
Ouvrez dans votre navigateur: `http://localhost/structureone/`

**Résultat attendu:**
- Page HTML stylée dark mode (#000)
- Logo StructureOne animé (flottement)
- Badge "Système Opérationnel" vert avec pulsation
- 3 cards avec animations au survol
- Design responsive

✅ Si vous voyez la page HTML → **HOMEPAGE HTML FONCTIONNE !**

❌ Si vous voyez du JSON → Vérifiez que `HOMEPAGE_DISPLAY_HTML=true` est bien dans `.env`

---

### Test 4: Page d'Erreur HTML

**Prérequis:** 
- `ERROR_DISPLAY_HTML=true` dans `.env`
- `DEBUG_MODE=true` dans `.env`

**Test - Créez un fichier test avec une erreur:**

```php
// core/routes/error_test/index.php
<?php
require_method("GET");

// Provoquer une erreur volontairement
throw new Exception("Ceci est un test d'erreur");
?>
```

**Ensuite testez:**
```bash
curl http://localhost/structureone/api/error_test
```

**Ou dans le navigateur:** `http://localhost/structureone/api/error_test`

**Résultat attendu:**
- Page HTML d'erreur dark mode
- Type d'exception affiché (Exception)
- Message: "Ceci est un test d'erreur"
- Stack trace détaillée (5 premiers niveaux)
- Extrait du code avec la ligne d'erreur
- Suggestions de résolution

✅ Si vous voyez la page HTML d'erreur → **ERROR PAGE HTML FONCTIONNE !**

---

## 🔍 ÉTAPE 4: Diagnostic des Problèmes

### Problème: Variables toujours vides

**Solution:**
1. Vérifiez que `functions.php` existe bien dans le dossier de la route
2. Vérifiez que les variables sont bien définies dans `functions.php`
3. Testez avec un `var_dump($GLOBALS)` dans `index.php` pour voir toutes les variables

**Debug dans index.php:**
```php
<?php
require_method("GET");

// Debug: Afficher toutes les variables globales
echo "<pre>";
print_r($GLOBALS);
echo "</pre>";
?>
```

---

### Problème: Fonctions non trouvées

**Vérifications:**
1. La fonction est bien définie dans `functions.php`
2. `functions.php` est chargé AVANT `index.php` (automatique)
3. La syntaxe de la fonction est correcte

**Exemple de fonction correcte:**
```php
// Dans functions.php
<?php
function myCustomFunction() {
    return "Hello World";
}
?>

// Dans index.php
<?php
require_method("GET");
echo api_response(200, myCustomFunction());
?>
```

---

### Problème: Page HTML ne s'affiche pas

**Checklist:**
- [ ] Le fichier `.env` existe à la racine
- [ ] `HOMEPAGE_DISPLAY_HTML=true` est dans `.env`
- [ ] `ERROR_DISPLAY_HTML=true` est dans `.env`
- [ ] `DEBUG_MODE=true` est dans `.env`
- [ ] Les fichiers `core/homepage.php` et `core/error_page.php` existent
- [ ] Apache/Nginx est redémarré après modification du `.env`

**Test rapide du .env:**
```php
// Créer test_env.php à la racine
<?php
require_once 'loader.php';

echo "DEBUG_MODE = " . env('DEBUG_MODE') . "\n";
echo "HOMEPAGE_DISPLAY_HTML = " . env('HOMEPAGE_DISPLAY_HTML') . "\n";
echo "ERROR_DISPLAY_HTML = " . env('ERROR_DISPLAY_HTML') . "\n";
?>
```

Puis: `php test_env.php`

Résultat attendu:
```
DEBUG_MODE = true
HOMEPAGE_DISPLAY_HTML = true
ERROR_DISPLAY_HTML = true
```

---

## ✅ Checklist Complète

### Variables et Fonctions:
- [ ] `$name` accessible dans `/api/test`
- [ ] `$hook` accessible dans `/api/v1/test`
- [ ] `getHello()` fonctionne dans `/api/v1/test`

### Pages HTML:
- [ ] Page d'accueil affiche HTML (pas JSON)
- [ ] Logo animé visible
- [ ] Cards interactives
- [ ] Page d'erreur affiche HTML avec stack trace

### Configuration:
- [ ] Fichier `.env` existe
- [ ] Toutes les variables d'environnement définies
- [ ] Serveur redémarré

---

## 🎉 Résultat Final Attendu

Si TOUT fonctionne correctement:

**Routes Legacy + Versionnées:**
- ✅ Variables partagées automatiquement
- ✅ Fonctions accessibles partout
- ✅ Pas besoin de `global`

**Pages HTML:**
- ✅ Homepage dark mode professionnelle
- ✅ Error page avec debug détaillé
- ✅ Responsive (mobile, tablet, desktop)
- ✅ Animations fluides

---

**Date:** 31 Octobre 2024  
**Version:** 2.1.1+  
**Status:** ✅ Corrections appliquées

**Si un test échoue, consultez la section "Diagnostic des Problèmes" ci-dessus.**
