#!/usr/bin/env node

/**
 * Script d'installation et de configuration automatique pour le projet API
 * Auteur: Exaustan Malka
 * Version Node.js
 */

const fs = require('fs');
const path = require('path');
const readline = require('readline');

class ProjectInstaller {
    constructor() {
        this.project_name = "";
        this.version = "";
        this.created_date = "";
        this.env_vars = {};
        this.rl = readline.createInterface({
            input: process.stdin,
            output: process.stdout
        });
    }

    printHeader() {
        /** Afficher l'en-tête du script */
        console.log("=".repeat(60));
        console.log("INSTALLATEUR AUTOMATIQUE - PROJET API (Node.js)");
        console.log("=".repeat(60));
        console.log();
    }

    printStep(message) {
        /** Afficher une étape en cours */
        console.log(`→ ${message}`);
    }

    printSuccess(message) {
        /** Afficher un succès */
        console.log(`✅ ${message}`);
    }

    printError(message) {
        /** Afficher une erreur */
        console.log(`❌ ${message}`);
    }

    printWarning(message) {
        /** Afficher un avertissement */
        console.log(`⚠️  ${message}`);
    }

    async checkRequiredFiles() {
        /** Vérifier si les fichiers requis existent */
        const requiredFiles = ['index.php', 'config.php', 'loader.php'];
        const missingFiles = [];

        for (const file of requiredFiles) {
            if (!fs.existsSync(file)) {
                missingFiles.push(file);
            }
        }

        if (missingFiles.length > 0) {
            this.printError(`Fichiers manquants: ${missingFiles.join(', ')}`);
            this.printError("Le script ne peut pas continuer sans ces fichiers.");
            return false;
        }

        this.printSuccess("Tous les fichiers requis sont présents");
        return true;
    }

    question(prompt, defaultValue = "") {
        /** Obtenir une entrée utilisateur avec valeur par défaut */
        return new Promise((resolve) => {
            const questionText = defaultValue ? 
                `${prompt} [${defaultValue}]: ` : 
                `${prompt}: `;
            
            this.rl.question(questionText, (answer) => {
                resolve(answer.trim() || defaultValue);
            });
        });
    }

    async collectProjectInfo() {
        /** Collecter les informations du projet */
        this.printStep("Collecte des informations du projet");
        console.log();

        this.project_name = await this.question("Nom du projet", "PROJECT_NAME");
        this.version = await this.question("Version du projet", "1.0");

        // Date actuelle au format dd/mm/YYYY
        const today = new Date();
        this.created_date = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;

        this.printSuccess(`Nom du projet: ${this.project_name}`);
        this.printSuccess(`Version: ${this.version}`);
        this.printSuccess(`Date de création: ${this.created_date}`);
        console.log();
    }

    async collectDatabaseInfo() {
        /** Collecter les informations de la base de données */
        this.printStep("Configuration de la base de données");
        console.log();

        this.env_vars['DB_HOST'] = await this.question("Hôte de la base de données", "localhost");
        this.env_vars['DB_PORT'] = await this.question("Port de la base de données", "3306");
        this.env_vars['DB_NAME'] = await this.question("Nom de la base de données", "");
        this.env_vars['DB_USER'] = await this.question("Utilisateur de la base de données", "");
        this.env_vars['DB_PASS'] = await this.question("Mot de passe de la base de données", "");

        console.log();
    }

    async collectSmtpInfo() {
        /** Collecter les informations SMTP */
        this.printStep("Configuration SMTP (envoi d'emails)");
        console.log();

        const useSmtp = await this.question("Activer SMTP? (oui/non)", "non");
        
        if (['oui', 'yes', 'y', 'o'].includes(useSmtp.toLowerCase())) {
            this.env_vars['SMTP_HOST'] = await this.question("Hôte SMTP", "");
            this.env_vars['SMTP_PORT'] = await this.question("Port SMTP", "587");
            this.env_vars['SMTP_USER'] = await this.question("Utilisateur SMTP", "");
            this.env_vars['SMTP_PASS'] = await this.question("Mot de passe SMTP", "");
            this.env_vars['SMTP_SECURE'] = await this.question("Sécurité SMTP (tls/ssl)", "tls");
        } else {
            this.printWarning("SMTP désactivé");
        }

        console.log();
    }

    async collectDebugInfo() {
        /** Collecter les informations de debug */
        this.printStep("Configuration du mode debug");
        console.log();

        const debugMode = await this.question("Activer le mode debug? (oui/non)", "non");
        
        if (['oui', 'yes', 'y', 'o'].includes(debugMode.toLowerCase())) {
            this.env_vars['DEBUG_MODE'] = "true";
            this.env_vars['LOG_LEVEL'] = await this.question("Niveau de log (debug/info/warning/error)", "debug");
        } else {
            this.env_vars['DEBUG_MODE'] = "false";
            this.env_vars['LOG_LEVEL'] = "error";
        }

        this.printSuccess(`Mode debug: ${this.env_vars['DEBUG_MODE'] === 'true' ? 'Activé' : 'Désactivé'}`);
        console.log();
    }

    async collectOtherInfo() {
        /** Collecter d'autres informations */
        this.printStep("Configuration supplémentaire");
        console.log();

        this.env_vars['APP_URL'] = await this.question("URL de l'application", "http://localhost");
        this.env_vars['TIMEZONE'] = await this.question("Fuseau horaire", "Africa/Kinshasa");

        console.log();
    }

    replaceInFile(filename, replacements) {
        /** Remplacer du contenu dans un fichier */
        try {
            let content = fs.readFileSync(filename, 'utf8');
            
            // Effectuer tous les remplacements
            for (const [oldValue, newValue] of Object.entries(replacements)) {
                const regex = new RegExp(oldValue, 'g');
                content = content.replace(regex, newValue);
            }
            
            fs.writeFileSync(filename, content, 'utf8');
            this.printSuccess(`Fichier ${filename} mis à jour`);
            return true;
            
        } catch (error) {
            this.printError(`Erreur lors de la modification de ${filename}: ${error.message}`);
            return false;
        }
    }

    updatePhpFiles() {
        /** Mettre à jour les fichiers PHP avec les nouvelles informations */
        this.printStep("Mise à jour des fichiers PHP");
        console.log();

        const replacements = {
            'PROJECT_NAME': this.project_name,
            'VERSION': this.version,
            'CREATED_DATE': this.created_date,
            'STACKS': 'PHP, MySQL, API'
        };

        const filesToUpdate = ['index.php', 'config.php', 'loader.php'];
        let allSuccess = true;

        for (const file of filesToUpdate) {
            if (!this.replaceInFile(file, replacements)) {
                allSuccess = false;
            }
        }

        console.log();
        return allSuccess;
    }

    createEnvFile() {
        /** Créer le fichier .env */
        this.printStep("Création du fichier .env");
        console.log();

        try {
            // Vérifier si le fichier .env existe déjà
            if (fs.existsSync('.env')) {
                const overwrite = this.question("Le fichier .env existe déjà. Voulez-vous l'écraser? (oui/non)", "non");
                if (!['oui', 'yes', 'y', 'o'].includes(overwrite.toLowerCase())) {
                    this.printWarning("Fichier .env conservé - aucune modification");
                    return true;
                }
            }

            let envContent = "# Fichier de configuration environnementale\n";
            envContent += "# Généré automatiquement par install.js\n";
            envContent += `# Projet: ${this.project_name}\n`;
            envContent += `# Date: ${this.created_date}\n\n`;
            
            envContent += "# Configuration de la base de données\n";
            envContent += `DB_HOST=${this.env_vars['DB_HOST'] || ''}\n`;
            envContent += `DB_PORT=${this.env_vars['DB_PORT'] || ''}\n`;
            envContent += `DB_NAME=${this.env_vars['DB_NAME'] || ''}\n`;
            envContent += `DB_USER=${this.env_vars['DB_USER'] || ''}\n`;
            envContent += `DB_PASS=${this.env_vars['DB_PASS'] || ''}\n\n`;
            
            envContent += "# Configuration SMTP\n";
            if (this.env_vars['SMTP_HOST']) {
                envContent += `SMTP_HOST=${this.env_vars['SMTP_HOST'] || ''}\n`;
                envContent += `SMTP_PORT=${this.env_vars['SMTP_PORT'] || ''}\n`;
                envContent += `SMTP_USER=${this.env_vars['SMTP_USER'] || ''}\n`;
                envContent += `SMTP_PASS=${this.env_vars['SMTP_PASS'] || ''}\n`;
                envContent += `SMTP_SECURE=${this.env_vars['SMTP_SECURE'] || 'tls'}\n`;
            }
            envContent += "\n";
            
            envContent += "# Configuration application\n";
            envContent += `APP_URL=${this.env_vars['APP_URL'] || ''}\n`;
            envContent += `DEBUG_MODE=${this.env_vars['DEBUG_MODE'] || 'false'}\n`;
            envContent += `LOG_LEVEL=${this.env_vars['LOG_LEVEL'] || 'error'}\n`;
            envContent += `TIMEZONE=${this.env_vars['TIMEZONE'] || 'Africa/Kinshasa'}\n`;

            fs.writeFileSync('.env', envContent, 'utf8');
            this.printSuccess("Fichier .env créé avec succès");
            console.log();
            return true;
            
        } catch (error) {
            this.printError(`Erreur lors de la création du .env: ${error.message}`);
            return false;
        }
    }

    createDirectoryStructure() {
        /** Créer la structure de dossiers nécessaire */
        this.printStep("Création de la structure de dossiers");
        console.log();

        const directories = [
            'core',
            'core/routes',
            'core/database',
            'core/uploads',
            'core/cache',
            'core/logs'
        ];

        for (const directory of directories) {
            if (!fs.existsSync(directory)) {
                try {
                    fs.mkdirSync(directory, { recursive: true });
                    this.printSuccess(`Dossier créé: ${directory}`);
                } catch (error) {
                    this.printError(`Erreur création dossier ${directory}: ${error.message}`);
                    return false;
                }
            } else {
                this.printSuccess(`Dossier existe déjà: ${directory}`);
            }
        }

        console.log();
        return true;
    }

    createExampleRoute() {
        /** Créer un exemple de route pour tester */
        this.printStep("Création d'un exemple de route");
        console.log();

        const exampleRouteDir = 'core/routes/test';

        // Vérifier si le dossier existe déjà
        if (!fs.existsSync(exampleRouteDir)) {
            try {
                fs.mkdirSync(exampleRouteDir, { recursive: true });
                this.printSuccess(`Dossier créé: ${exampleRouteDir}`);
            } catch (error) {
                this.printError(`Erreur création dossier ${exampleRouteDir}: ${error.message}`);
                return false;
            }
        } else {
            this.printSuccess(`Dossier existe déjà: ${exampleRouteDir}`);
        }

        // Créer functions.php pour l'exemple avec le nouveau code
        const functionsContent = `<?php

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
`;

        // Créer index.php pour l'exemple avec le nouveau code
        const indexContent = `<?php

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
`;

        try {
            // Vérifier si les fichiers existent déjà
            const functionsFile = path.join(exampleRouteDir, 'functions.php');
            const indexFile = path.join(exampleRouteDir, 'index.php');

            if (fs.existsSync(functionsFile)) {
                this.printWarning(`Fichier existe déjà: ${functionsFile} - conservation du fichier existant`);
            } else {
                fs.writeFileSync(functionsFile, functionsContent);
                this.printSuccess(`Fichier créé: ${functionsFile}`);
            }

            if (fs.existsSync(indexFile)) {
                this.printWarning(`Fichier existe déjà: ${indexFile} - conservation du fichier existant`);
            } else {
                fs.writeFileSync(indexFile, indexContent);
                this.printSuccess(`Fichier créé: ${indexFile}`);
            }
            
            this.printSuccess("Exemple de route créé: /api/test");
            console.log();
            return true;
            
        } catch (error) {
            this.printWarning(`Impossible de créer l'exemple de route: ${error.message}`);
            return false;
        }
    }

    cleanupInstallationFiles() {
        /** Supprimer les fichiers d'installation après une installation réussie */
        this.printStep("Nettoyage des fichiers d'installation");
        console.log();

        const installationFiles = ['install.js', 'install.py'];
        let allDeleted = true;

        for (const file of installationFiles) {
            if (fs.existsSync(file)) {
                try {
                    fs.unlinkSync(file);
                    this.printSuccess(`Fichier supprimé: ${file}`);
                } catch (error) {
                    this.printWarning(`Impossible de supprimer ${file}: ${error.message}`);
                    allDeleted = false;
                }
            } else {
                this.printSuccess(`Fichier non trouvé (déjà supprimé): ${file}`);
            }
        }

        console.log();
        return allDeleted;
    }

    async runInstallation() {
        /** Exécuter l'installation complète */
        this.printHeader();

        // Vérifier les fichiers requis
        if (!(await this.checkRequiredFiles())) {
            process.exit(1);
        }

        // Collecter les informations
        await this.collectProjectInfo();
        await this.collectDatabaseInfo();
        await this.collectSmtpInfo();
        await this.collectDebugInfo();
        await this.collectOtherInfo();

        // Demander confirmation
        console.log("=".repeat(50));
        const confirmation = await this.question("Confirmer l'installation? (oui/non)", "oui");
        
        if (!['oui', 'yes', 'y', 'o'].includes(confirmation.toLowerCase())) {
            this.printWarning("Installation annulée");
            this.rl.close();
            return;
        }

        console.log();
        this.printStep("Début de l'installation...");
        console.log();

        // Exécuter les étapes d'installation
        const steps = [
            { name: "Mise à jour des fichiers PHP", func: () => this.updatePhpFiles() },
            { name: "Création du fichier .env", func: () => this.createEnvFile() },
            { name: "Création de la structure de dossiers", func: () => this.createDirectoryStructure() },
            { name: "Création d'un exemple de route", func: () => this.createExampleRoute() },
            { name: "Nettoyage des fichiers d'installation", func: () => this.cleanupInstallationFiles() }
        ];

        let installationSuccess = true;

        for (const step of steps) {
            this.printStep(step.name);
            if (!step.func()) {
                this.printError(`Échec de l'étape: ${step.name}`);
                installationSuccess = false;
                // On continue l'exécution même si une étape échoue, sauf pour les étapes critiques
                if (step.name === "Création de la structure de dossiers") {
                    this.rl.close();
                    return;
                }
            }
        }

        if (!installationSuccess) {
            this.printError("Installation terminée avec des erreurs");
            this.rl.close();
            return;
        }

        // Afficher le résumé
        this.printSuccess("Installation terminée avec succès!");
        console.log();
        console.log("=".repeat(60));
        console.log("RÉSUMÉ DE L'INSTALLATION");
        console.log("=".repeat(60));
        console.log(`Projet: ${this.project_name}`);
        console.log(`Version: ${this.version}`);
        console.log(`Date: ${this.created_date}`);
        console.log(`URL de test: ${this.env_vars['APP_URL'] || ''}/api/test`);
        console.log("Structure créée:");
        console.log("  - core/routes/ (dossier des routes API)");
        console.log("  - core/database/ (fichiers de base de données)");
        console.log("  - core/uploads/ (fichiers uploadés)");
        console.log("  - core/cache/ (fichiers de cache)");
        console.log("  - core/logs/ (fichiers de log)");
        console.log();
        console.log("Prochaines étapes:");
        console.log("1. Configurer votre serveur web");
        console.log("2. Créer la base de données si nécessaire");
        console.log("3. Tester l'API avec l'URL: /api/test");
        console.log("4. Pour tester JWT:");
        console.log("   - Générer un token: /api/test?id=123");
        console.log("   - Valider un token: /api/test?token=VOTRE_TOKEN");
        console.log();
        console.log("✅ Les fichiers d'installation ont été supprimés automatiquement");
        console.log("=".repeat(60));

        this.rl.close();
    }
}

// Point d'entrée principal
async function main() {
    try {
        const installer = new ProjectInstaller();
        await installer.runInstallation();
    } catch (error) {
        console.error('❌ Erreur fatale:', error.message);
        process.exit(1);
    }
}

// Gestion propre de la sortie
process.on('SIGINT', () => {
    console.log('\n\n❌ Installation interrompue par l\'utilisateur');
    process.exit(0);
});

// Lancer l'application
if (require.main === module) {
    main();
}

module.exports = ProjectInstaller;