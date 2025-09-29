<?php

function create_user($username, $password) {
    /**
     * 
     * Verifier si l'utilisateur existe deja 
     * 
     */
    $exist = db_find("SELECT * FROM users WHERE username = ?", [
        $username
    ]);

    /**
     * 
     * Conditions 
     * 
     */
    if ($exist) {
        /**
         * 
         * Renvoyer une erreur car l'utilisateur existe deja 
         * 
         */
        echo api_response(409, "L'utilisateur existe déjà", null);
        return; // Important: arrêter l'exécution ici
    } else {
        /**
         * 
         * Executer la requete d'insertion
         * 
         */
        db_execute(
            "INSERT INTO users (username, password_test) VALUES (?, ?)",
            [$username, $password]
        );

        /**
         * 
         * Recuperer la derniere ID 
         * 
         */
        $id = db_last_id();

        /**
         * 
         * Creer un jeton JWT 
         * 
         */
        $token = jwt_generate($id);

        /**
         * 
         * Creer une reponse 
         * 
         */
        $re = [
            "jwt_token" => $token
        ];

        /**
         * 
         * Afficher la reponse 
         * 
         */
        echo api_response(201, "Utilisateur créé avec succès", $re);
    }
}

?>