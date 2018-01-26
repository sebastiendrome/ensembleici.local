<?php
//1. Initialisation de la session
include "../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../01_include/_init_var.php";

$return_code = '0';

if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    // recherche des infos utilisateur
    $requete_utilisateur = "SELECT * FROM utilisateur WHERE email = :email";
    $tab_utilisateur = execute_requete($requete_utilisateur,array(":email"=>$_POST["email"]));
    
    if (sizeof($tab_utilisateur) > 0) {
        // l'adresse email est dans la table utilisateur
        $requete_update_utilisateur = "UPDATE utilisateur SET newsletter = 0 WHERE email = :email";
        $param_update = array(":email" => $_POST["email"]);
        $mareq = execute_requete($requete_update_utilisateur,$param_update);
    }
    else {
        $requete_newsletter = "SELECT * FROM newsletter WHERE email = :email";
        $tab_newsletter = execute_requete($requete_newsletter,array(":email"=>$_POST["email"]));
        if (sizeof($tab_newsletter) > 0) {
            // l'adresse email est dans la table newsletter
            $requete_update_newsletter = "UPDATE newsletter SET etat = 0 WHERE email = :email";
            $param_update = array(":email" => $_POST["email"]);
            $mareq = execute_requete($requete_update_newsletter,$param_update);
        }
        else {
            $return_code = '2';
        }
    }
    
    
}
else {
    $return_code = '1';
}

$tab = array(); 
$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
