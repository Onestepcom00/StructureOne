<?php
/**
 * 
 * 
 * Ceci est un fichier d'API d'exemple , soyez libre de le tester
 * 
 * 
 */

 // Autoriser la methode GET
 require_method("GET");

 try{
    // afficher une reponse
    echo api_response(200,getHello(),null);
 }catch(Exception $e){
    // Afficher les erreurs en fonction du debug 
    echo getError($e);
 }


?>