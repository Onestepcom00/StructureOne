#!/usr/bin/env python3

"""
Script d'installation et de configuration automatique pour le projet API
Auteur: Exaustan Malka
Version Python 3
"""

import os
import sys
import datetime
from pathlib import Path

class ProjectInstaller:
    def __init__(self):
        self.project_name = ""
        self.version = ""
        self.created_date = ""
        self.env_vars = {}
    
    def print_header(self):
        """Afficher l'en-tête du script"""
        print("=" * 60)
        print("INSTALLATEUR AUTOMATIQUE - PROJET API (Python 3)")
        print("=" * 60)
        print()
    
    def print_step(self, message):
        """Afficher une étape en cours"""
        print(f"→ {message}")
    
    def print_success(self, message):
        """Afficher un succès"""
        print(f"✅ {message}")
    
    def print_error(self, message):
        """Afficher une erreur"""
        print(f"❌ {message}")
    
    def print_warning(self, message):
        """Afficher un avertissement"""
        print(f"⚠️  {message}")
    
    def check_required_files(self):
        """Vérifier si les fichiers requis existent"""
        required_files = ['index.php', 'config.php', 'loader.php']
        missing_files = []
        
        for file in required_files:
            if not os.path.exists(file):
                missing_files.append(file)
        
        if missing_files:
            self.print_error(f"Fichiers manquants: {', '.join(missing_files)}")
            self.print_error("Le script ne peut pas continuer sans ces fichiers.")
            return False
        
        self.print_success("Tous les fichiers requis sont présents")
        return True
    
    def question(self, prompt, default_value=""):
        """Obtenir une entrée utilisateur avec valeur par défaut"""
        prompt_text = f"{prompt} [{default_value}]: " if default_value else f"{prompt}: "
        
        try:
            answer = input(prompt_text).strip()
            return answer or default_value
        except (KeyboardInterrupt, EOFError):
            print("\n\n❌ Installation interrompue par l'utilisateur")
            sys.exit(0)
    
    def collect_project_info(self):
        """Collecter les informations du projet"""
        self.print_step("Collecte des informations du projet")
        print()
        
        self.project_name = self.question("Nom du projet", "PROJECT_NAME")
        self.version = self.question("Version du projet", "1.0")
        
        # Date actuelle au format dd/mm/YYYY
        today = datetime.datetime.now()
        self.created_date = today.strftime("%d/%m/%Y")
        
        self.print_success(f"Nom du projet: {self.project_name}")
        self.print_success(f"Version: {self.version}")
        self.print_success(f"Date de création: {self.created_date}")
        print()
    
    def collect_database_info(self):
        """Collecter les informations de la base de données"""
        self.print_step("Configuration de la base de données")
        print()
        
        self.env_vars['DB_HOST'] = self.question("Hôte de la base de données", "localhost")
        self.env_vars['DB_PORT'] = self.question("Port de la base de données", "3306")
        self.env_vars['DB_NAME'] = self.question("Nom de la base de données", "")
        self.env_vars['DB_USER'] = self.question("Utilisateur de la base de données", "")
        self.env_vars['DB_PASS'] = self.question("Mot de passe de la base de données", "")
        
        print()
    
    def collect_smtp_info(self):
        """Collecter les informations SMTP"""
        self.print_step("Configuration SMTP (envoi d'emails)")
        print()
        
        use_smtp = self.question("Activer SMTP? (oui/non)", "non")
        
        if use_smtp.lower() in ['oui', 'yes', 'y', 'o']:
            self.env_vars['SMTP_HOST'] = self.question("Hôte SMTP", "")
            self.env_vars['SMTP_PORT'] = self.question("Port SMTP", "587")
            self.env_vars['SMTP_USER'] = self.question("Utilisateur SMTP", "")
            self.env_vars['SMTP_PASS'] = self.question("Mot de passe SMTP", "")
            self.env_vars['SMTP_SECURE'] = self.question("Sécurité SMTP (tls/ssl)", "tls")
        else:
            self.print_warning("SMTP désactivé")
        
        print()
    
    def collect_debug_info(self):
        """Collecter les informations de debug"""
        self.print_step("Configuration du mode debug")
        print()
        
        debug_mode = self.question("Activer le mode debug? (oui/non)", "non")
        
        if debug_mode.lower() in ['oui', 'yes', 'y', 'o']:
            self.env_vars['DEBUG_MODE'] = "true"
            self.env_vars['LOG_LEVEL'] = self.question("Niveau de log (debug/info/warning/error)", "debug")
        else:
            self.env_vars['DEBUG_MODE'] = "false"
            self.env_vars['LOG_LEVEL'] = "error"
        
        self.print_success(f"Mode debug: {'Activé' if self.env_vars['DEBUG_MODE'] == 'true' else 'Désactivé'}")
        print()
    
    def collect_other_info(self):
        """Collecter d'autres informations"""
        self.print_step("Configuration supplémentaire")
        print()
        
        self.env_vars['APP_URL'] = self.question("URL de l'application", "http://localhost")
        self.env_vars['TIMEZONE'] = self.question("Fuseau horaire", "Africa/Kinshasa")
        
        print()
    
    def replace_in_file(self, filename, replacements):
        """Remplacer du contenu dans un fichier"""
        try:
            with open(filename, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Effectuer tous les remplacements
            for old_value, new_value in replacements.items():
                content = content.replace(old_value, new_value)
            
            with open(filename, 'w', encoding='utf-8') as file:
                file.write(content)
            
            self.print_success(f"Fichier {filename} mis à jour")
            return True
            
        except Exception as error:
            self.print_error(f"Erreur lors de la modification de {filename}: {str(error)}")
            return False
    
    def update_php_files(self):
        """Mettre à jour les fichiers PHP avec les nouvelles informations"""
        self.print_step("Mise à jour des fichiers PHP")
        print()
        
        replacements = {
            'PROJECT_NAME': self.project_name,
            'VERSION': self.version,
            'CREATED_DATE': self.created_date,
            'STACKS': 'PHP, MySQL, API'
        }
        
        files_to_update = ['index.php', 'config.php', 'loader.php']
        all_success = True
        
        for file in files_to_update:
            if not self.replace_in_file(file, replacements):
                all_success = False
        
        print()
        return all_success
    
    def create_env_file(self):
        """Créer le fichier .env"""
        self.print_step("Création du fichier .env")
        print()
        
        try:
            # Vérifier si le fichier .env existe déjà
            if os.path.exists('.env'):
                overwrite = self.question("Le fichier .env existe déjà. Voulez-vous l'écraser? (oui/non)", "non")
                if overwrite.lower() not in ['oui', 'yes', 'y', 'o']:
                    self.print_warning("Fichier .env conservé - aucune modification")
                    return True
            
            env_content = "# Fichier de configuration environnementale\n"
            env_content += "# Généré automatiquement par install.py\n"
            env_content += f"# Projet: {self.project_name}\n"
            env_content += f"# Date: {self.created_date}\n\n"
            
            env_content += "# Configuration de la base de données\n"
            env_content += f"DB_HOST={self.env_vars.get('DB_HOST', '')}\n"
            env_content += f"DB_PORT={self.env_vars.get('DB_PORT', '')}\n"
            env_content += f"DB_NAME={self.env_vars.get('DB_NAME', '')}\n"
            env_content += f"DB_USER={self.env_vars.get('DB_USER', '')}\n"
            env_content += f"DB_PASS={self.env_vars.get('DB_PASS', '')}\n\n"
            
            env_content += "# Configuration SMTP\n"
            if self.env_vars.get('SMTP_HOST'):
                env_content += f"SMTP_HOST={self.env_vars.get('SMTP_HOST', '')}\n"
                env_content += f"SMTP_PORT={self.env_vars.get('SMTP_PORT', '')}\n"
                env_content += f"SMTP_USER={self.env_vars.get('SMTP_USER', '')}\n"
                env_content += f"SMTP_PASS={self.env_vars.get('SMTP_PASS', '')}\n"
                env_content += f"SMTP_SECURE={self.env_vars.get('SMTP_SECURE', 'tls')}\n"
            env_content += "\n"
            
            env_content += "# Configuration application\n"
            env_content += f"APP_URL={self.env_vars.get('APP_URL', '')}\n"
            env_content += f"DEBUG_MODE={self.env_vars.get('DEBUG_MODE', 'false')}\n"
            env_content += f"LOG_LEVEL={self.env_vars.get('LOG_LEVEL', 'error')}\n"
            env_content += f"TIMEZONE={self.env_vars.get('TIMEZONE', 'Africa/Kinshasa')}\n"
            
            with open('.env', 'w', encoding='utf-8') as file:
                file.write(env_content)
            
            self.print_success("Fichier .env créé avec succès")
            print()
            return True
            
        except Exception as error:
            self.print_error(f"Erreur lors de la création du .env: {str(error)}")
            return False
    
    def create_directory_structure(self):
        """Créer la structure de dossiers nécessaire"""
        self.print_step("Création de la structure de dossiers")
        print()
        
        directories = [
            'core',
            'core/routes',
            'core/database',
            'core/uploads',
            'core/cache',
            'core/logs'
        ]
        
        for directory in directories:
            if not os.path.exists(directory):
                try:
                    os.makedirs(directory, exist_ok=True)
                    self.print_success(f"Dossier créé: {directory}")
                except Exception as error:
                    self.print_error(f"Erreur création dossier {directory}: {str(error)}")
                    return False
            else:
                self.print_success(f"Dossier existe déjà: {directory}")
        
        print()
        return True
    
    def create_example_route(self):
        """Créer un exemple de route pour tester"""
        self.print_step("Création d'un exemple de route")
        print()
        
        example_route_dir = 'core/routes/test'
        
        # Vérifier si le dossier existe déjà
        if not os.path.exists(example_route_dir):
            try:
                os.makedirs(example_route_dir, exist_ok=True)
                self.print_success(f"Dossier créé: {example_route_dir}")
            except Exception as error:
                self.print_error(f"Erreur création dossier {example_route_dir}: {str(error)}")
                return False
        else:
            self.print_success(f"Dossier existe déjà: {example_route_dir}")
        
        # Créer functions.php pour l'exemple avec le nouveau code
        functions_content = """<?php

/**
 * Fonctions spécifiques à la route test
 */

function test_function() {
    /**
     * 
     * Extraitre le donnees du fichier json 
     */
}

?>
"""
        
        # Créer index.php pour l'exemple avec le nouveau code
        index_content = """<?php

/**
 * 
 * ceci est un exemple complet qui utilises les fonctions globaux 
 * 
 */


/**
 * 
 * Recuperer les requetes GET 
 * 
 */
if($_SERVER['REQUEST_METHOD'] === 'GET'){

    /**
     * 
     * Generer un token JWT 
     * 
     */
    if(isset($_GET['id'])){
        /**
         * 
         * generer le token 
         * 
         */
        $token = jwt_generate($_GET['id']);

        /**
         * 
         * Creer une reponse HTTP
         * 
         */
        $re = [
            "jwt_token" => $token
        ];

        /**
         * 
         * Afficher le token 
         * 
         */
        echo api_response(200, "Token généré avec succès", $re);
    }
    elseif(isset($_GET['token'])){
        /**
         * 
         * Recuperer le token et verifier sa validiter 
         * 
         */
        $decoded = jwt_validate($_GET['token']);

        if($decoded){
            /**
             * 
             * Creer un tableau 
             * 
             */
            $re = [
                "jwt_decoded" => $decoded
            ];

            /**
             * 
             * Le code sera traiter ici car le token est valide 
             * 
             */
            echo api_response(200, "Token valide", $re);
        }else{
            /**
             * 
             * Le token n'est pas valide 
             * 
             */
            echo api_response(401, "Token invalide", null);
        }
    }
    else{
        /**
         * 
         * Aucun prametre trouver 
         * 
         */
        echo api_response(400, "Aucun paramètre 'id' ou 'token' trouvé", null);
    }
}else{
    /**
     * 
     * Renvoyer une erreur si la methode n'est pas autoriser
     * 
     */
    echo api_response(405, "Méthode non autorisée", null);
}

?>
"""
        
        try:
            # Vérifier si les fichiers existent déjà
            functions_file = os.path.join(example_route_dir, 'functions.php')
            index_file = os.path.join(example_route_dir, 'index.php')
            
            if os.path.exists(functions_file):
                self.print_warning(f"Fichier existe déjà: {functions_file} - conservation du fichier existant")
            else:
                with open(functions_file, 'w', encoding='utf-8') as file:
                    file.write(functions_content)
                self.print_success(f"Fichier créé: {functions_file}")
            
            if os.path.exists(index_file):
                self.print_warning(f"Fichier existe déjà: {index_file} - conservation du fichier existant")
            else:
                with open(index_file, 'w', encoding='utf-8') as file:
                    file.write(index_content)
                self.print_success(f"Fichier créé: {index_file}")
            
            self.print_success("Exemple de route créé: /api/test")
            print()
            return True
            
        except Exception as error:
            self.print_warning(f"Impossible de créer l'exemple de route: {str(error)}")
            return False

    def cleanup_installation_files(self):
        """Supprimer les fichiers d'installation après une installation réussie"""
        self.print_step("Nettoyage des fichiers d'installation")
        print()

        installation_files = ['install.js', 'install.py']
        all_deleted = True

        for file in installation_files:
            if os.path.exists(file):
                try:
                    os.remove(file)
                    self.print_success(f"Fichier supprimé: {file}")
                except Exception as error:
                    self.print_warning(f"Impossible de supprimer {file}: {str(error)}")
                    all_deleted = False
            else:
                self.print_success(f"Fichier non trouvé (déjà supprimé): {file}")

        print()
        return all_deleted

    def run_installation(self):
        """Exécuter l'installation complète"""
        self.print_header()
        
        # Vérifier les fichiers requis
        if not self.check_required_files():
            sys.exit(1)
        
        # Collecter les informations
        self.collect_project_info()
        self.collect_database_info()
        self.collect_smtp_info()
        self.collect_debug_info()
        self.collect_other_info()
        
        # Demander confirmation
        print("=" * 50)
        confirmation = self.question("Confirmer l'installation? (oui/non)", "oui")
        
        if confirmation.lower() not in ['oui', 'yes', 'y', 'o']:
            self.print_warning("Installation annulée")
            return
        
        print()
        self.print_step("Début de l'installation...")
        print()
        
        # Exécuter les étapes d'installation
        steps = [
            {"name": "Mise à jour des fichiers PHP", "func": self.update_php_files},
            {"name": "Création du fichier .env", "func": self.create_env_file},
            {"name": "Création de la structure de dossiers", "func": self.create_directory_structure},
            {"name": "Création d'un exemple de route", "func": self.create_example_route},
            {"name": "Nettoyage des fichiers d'installation", "func": self.cleanup_installation_files}
        ]
        
        installation_success = True
        
        for step in steps:
            self.print_step(step["name"])
            if not step["func"]():
                self.print_error(f"Échec de l'étape: {step['name']}")
                installation_success = False
                # On continue l'exécution même si une étape échoue, sauf pour les étapes critiques
                if step["name"] == "Création de la structure de dossiers":
                    return
        
        if not installation_success:
            self.print_error("Installation terminée avec des erreurs")
            return
        
        # Afficher le résumé
        self.print_success("Installation terminée avec succès!")
        print()
        print("=" * 60)
        print("RÉSUMÉ DE L'INSTALLATION")
        print("=" * 60)
        print(f"Projet: {self.project_name}")
        print(f"Version: {self.version}")
        print(f"Date: {self.created_date}")
        print(f"URL de test: {self.env_vars.get('APP_URL', '')}/api/test")
        print("Structure créée:")
        print("  - core/routes/ (dossier des routes API)")
        print("  - core/database/ (fichiers de base de données)")
        print("  - core/uploads/ (fichiers uploadés)")
        print("  - core/cache/ (fichiers de cache)")
        print("  - core/logs/ (fichiers de log)")
        print()
        print("Prochaines étapes:")
        print("1. Configurer votre serveur web")
        print("2. Créer la base de données si nécessaire")
        print("3. Tester l'API avec l'URL: /api/test")
        print("4. Pour tester JWT:")
        print("   - Générer un token: /api/test?id=123")
        print("   - Valider un token: /api/test?token=VOTRE_TOKEN")
        print()
        print("✅ Les fichiers d'installation ont été supprimés automatiquement")
        print("=" * 60)

def main():
    """Point d'entrée principal"""
    try:
        installer = ProjectInstaller()
        installer.run_installation()
    except KeyboardInterrupt:
        print('\n\n❌ Installation interrompue par l\'utilisateur')
        sys.exit(0)
    except Exception as error:
        print(f'❌ Erreur fatale: {str(error)}')
        sys.exit(1)

if __name__ == "__main__":
    main()