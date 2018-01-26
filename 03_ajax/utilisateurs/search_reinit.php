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
$tab = array();

if (est_connecte()) {
    $return_code = '2';
}
else {
    $requete_utilisateur = "SELECT no, email FROM utilisateur WHERE code_reinit_mot_de_passe = :code";
    $tab_utilisateur = execute_requete($requete_utilisateur,array(":code"=>$_POST["code"]));

    if (!empty($tab_utilisateur)) {
        $tab['id'] = $tab_utilisateur[0]["no"];
        $tab['email'] = $tab_utilisateur[0]["email"];
    }
    else {
        $return_code = '1';
    }
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
