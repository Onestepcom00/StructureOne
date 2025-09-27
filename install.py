#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script d'installation et de configuration automatique pour le projet API
Auteur: Exaustan Malka
"""

import os
import re
import datetime
import sys

class ProjectInstaller:
    def __init__(self):
        self.project_name = ""
        self.version = ""
        self.created_date = ""
        self.env_vars = {}
        
    def print_header(self):
        """Afficher l'en-tête du script"""
        print("=" * 60)
        print("INSTALLATEUR AUTOMATIQUE - PROJET API")
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
    
    def get_user_input(self, prompt, default=""):
        """Obtenir une entrée utilisateur avec valeur par défaut"""
        if default:
            user_input = input(f"{prompt} [{default}]: ").strip()
        else:
            user_input = input(f"{prompt}: ").strip()
        
        return user_input if user_input else default
    
    def collect_project_info(self):
        """Collecter les informations du projet"""
        self.print_step("Collecte des informations du projet")
        print()
        
        self.project_name = self.get_user_input("Nom du projet", "PROJECT_NAME")
        self.version = self.get_user_input("Version du projet", "1.0")
        
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
        
        self.env_vars['DB_HOST'] = self.get_user_input("Hôte de la base de données", "localhost")
        self.env_vars['DB_PORT'] = self.get_user_input("Port de la base de données", "3306")
        self.env_vars['DB_NAME'] = self.get_user_input("Nom de la base de données", "")
        self.env_vars['DB_USER'] = self.get_user_input("Utilisateur de la base de données", "")
        self.env_vars['DB_PASS'] = self.get_user_input("Mot de passe de la base de données", "")
        
        print()
    
    def collect_smtp_info(self):
        """Collecter les informations SMTP"""
        self.print_step("Configuration SMTP (envoi d'emails)")
        print()
        
        use_smtp = self.get_user_input("Activer SMTP? (oui/non)", "non").lower()
        
        if use_smtp in ['oui', 'yes', 'y', 'o']:
            self.env_vars['SMTP_HOST'] = self.get_user_input("Hôte SMTP", "")
            self.env_vars['SMTP_PORT'] = self.get_user_input("Port SMTP", "587")
            self.env_vars['SMTP_USER'] = self.get_user_input("Utilisateur SMTP", "")
            self.env_vars['SMTP_PASS'] = self.get_user_input("Mot de passe SMTP", "")
            self.env_vars['SMTP_SECURE'] = self.get_user_input("Sécurité SMTP (tls/ssl)", "tls")
        else:
            self.print_warning("SMTP désactivé")
        
        print()
    
    def collect_debug_info(self):
        """Collecter les informations de debug"""
        self.print_step("Configuration du mode debug")
        print()
        
        debug_mode = self.get_user_input("Activer le mode debug? (oui/non)", "non").lower()
        
        if debug_mode in ['oui', 'yes', 'y', 'o']:
            self.env_vars['DEBUG_MODE'] = "true"
            self.env_vars['LOG_LEVEL'] = self.get_user_input("Niveau de log (debug/info/warning/error)", "debug")
        else:
            self.env_vars['DEBUG_MODE'] = "false"
            self.env_vars['LOG_LEVEL'] = "error"
        
        self.print_success(f"Mode debug: {'Activé' if self.env_vars['DEBUG_MODE'] == 'true' else 'Désactivé'}")
        print()
    
    def collect_other_info(self):
        """Collecter d'autres informations"""
        self.print_step("Configuration supplémentaire")
        print()
        
        self.env_vars['APP_URL'] = self.get_user_input("URL de l'application", "http://localhost")
        self.env_vars['TIMEZONE'] = self.get_user_input("Fuseau horaire", "Africa/Kinshasa")
        
        print()
    
    def replace_in_file(self, filename, replacements):
        """Remplacer du contenu dans un fichier"""
        try:
            with open(filename, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Effectuer tous les remplacements
            for old, new in replacements.items():
                content = content.replace(old, new)
            
            with open(filename, 'w', encoding='utf-8') as file:
                file.write(content)
            
            self.print_success(f"Fichier {filename} mis à jour")
            return True
            
        except Exception as e:
            self.print_error(f"Erreur lors de la modification de {filename}: {str(e)}")
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
        
        for file in files_to_update:
            if not self.replace_in_file(file, replacements):
                return False
        
        print()
        return True
    
    def create_env_file(self):
        """Créer le fichier .env"""
        self.print_step("Création du fichier .env")
        print()
        
        try:
            with open('.env', 'w', encoding='utf-8') as f:
                f.write("# Fichier de configuration environnementale\n")
                f.write("# Généré automatiquement par install.py\n")
                f.write(f"# Projet: {self.project_name}\n")
                f.write(f"# Date: {self.created_date}\n\n")
                
                f.write("# Configuration de la base de données\n")
                f.write(f"DB_HOST={self.env_vars.get('DB_HOST', '')}\n")
                f.write(f"DB_PORT={self.env_vars.get('DB_PORT', '')}\n")
                f.write(f"DB_NAME={self.env_vars.get('DB_NAME', '')}\n")
                f.write(f"DB_USER={self.env_vars.get('DB_USER', '')}\n")
                f.write(f"DB_PASS={self.env_vars.get('DB_PASS', '')}\n\n")
                
                f.write("# Configuration SMTP\n")
                if 'SMTP_HOST' in self.env_vars:
                    f.write(f"SMTP_HOST={self.env_vars.get('SMTP_HOST', '')}\n")
                    f.write(f"SMTP_PORT={self.env_vars.get('SMTP_PORT', '')}\n")
                    f.write(f"SMTP_USER={self.env_vars.get('SMTP_USER', '')}\n")
                    f.write(f"SMTP_PASS={self.env_vars.get('SMTP_PASS', '')}\n")
                    f.write(f"SMTP_SECURE={self.env_vars.get('SMTP_SECURE', '')}\n")
                f.write("\n")
                
                f.write("# Configuration application\n")
                f.write(f"APP_URL={self.env_vars.get('APP_URL', '')}\n")
                f.write(f"DEBUG_MODE={self.env_vars.get('DEBUG_MODE', 'false')}\n")
                f.write(f"LOG_LEVEL={self.env_vars.get('LOG_LEVEL', 'error')}\n")
                f.write(f"TIMEZONE={self.env_vars.get('TIMEZONE', 'Africa/Kinshasa')}\n")
            
            self.print_success("Fichier .env créé avec succès")
            print()
            return True
            
        except Exception as e:
            self.print_error(f"Erreur lors de la création du .env: {str(e)}")
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
                    os.makedirs(directory)
                    self.print_success(f"Dossier créé: {directory}")
                except Exception as e:
                    self.print_error(f"Erreur création dossier {directory}: {str(e)}")
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
        
        if not os.path.exists(example_route_dir):
            os.makedirs(example_route_dir)
        
        # Créer functions.php pour l'exemple
        functions_content = """<?php

/**
 * Fonctions spécifiques à la route test
 */

function test_function() {
    return "Fonction de test exécutée avec succès";
}

?>
"""
        
        # Créer index.php pour l'exemple
        index_content = """<?php

/**
 * Route de test - Exemple d'implémentation
 */

// Inclure les fonctions spécifiques à cette route
require 'functions.php';

// Exemple de traitement de requête
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = test_function();
    echo api_response(200, "Route test fonctionne", [
        'message' => $result,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    echo api_response(405, "Méthode non autorisée");
}

?>
"""
        
        try:
            with open(f'{example_route_dir}/functions.php', 'w') as f:
                f.write(functions_content)
            
            with open(f'{example_route_dir}/index.php', 'w') as f:
                f.write(index_content)
            
            self.print_success("Exemple de route créé: /api/test")
            print()
            return True
            
        except Exception as e:
            self.print_warning(f"Impossible de créer l'exemple de route: {str(e)}")
            return False
    
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
        confirmation = input("Confirmer l'installation? (oui/non): ").lower()
        
        if confirmation not in ['oui', 'yes', 'y', 'o']:
            self.print_warning("Installation annulée")
            return
        
        print()
        self.print_step("Début de l'installation...")
        print()
        
        # Exécuter les étapes d'installation
        steps = [
            ("Mise à jour des fichiers PHP", self.update_php_files),
            ("Création du fichier .env", self.create_env_file),
            ("Création de la structure de dossiers", self.create_directory_structure),
            ("Création d'un exemple de route", self.create_example_route)
        ]
        
        for step_name, step_function in steps:
            self.print_step(step_name)
            if not step_function():
                self.print_error(f"Échec de l'étape: {step_name}")
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
        print("=" * 60)

# Point d'entrée principal
if __name__ == "__main__":
    installer = ProjectInstaller()
    installer.run_installation()