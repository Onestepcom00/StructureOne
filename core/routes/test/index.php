<?php

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
    elseif($_GET['token']){
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
        echo api_response(400, "Aucun paramètre 'id' trouvé", null);
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
