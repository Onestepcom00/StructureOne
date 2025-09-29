<?php
/**
 * 
 * 
 * ***************
 * Template : Signup Page 
 * ***************
 * 
 * 
 */

/**
 * 
 * Verifier la methode HTTP GET 
 * 
 */
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    /**
     * 
     * Recuperer les parametres 
     * 
     */
    if(isset($_GET['username']) && isset($_GET['password'])){
        /**
         * 
         * Recuperer les parametres 
         * 
         */
        $username = db_escape($_GET['username']);
        $password = db_hash($_GET['password']);

       /**
        * 
        * La fonction pour creer un utilisateur 
        *
        */
       $re = create_user($username,$password);

       /**
        * La reponse sera automatiquement afficher car la fonction create_user le fait deja et contient des erreurs 
        */

      }else{
        /**
         * 
         * Renvoyer une erreur car les parametres sont incomplet
         * 
         */
        echo api_response(400, "Paramètres incomplets", null);
      }      
}else{
    /**
     * 
     * Renvoyer une erreur car la methode n'est pas autoriser
     * 
     */
    echo api_response(405, "Méthode non autorisée", null);
}
?>