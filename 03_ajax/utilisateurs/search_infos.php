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

$requete_utilisateur = "SELECT U.no, U.email, U.pseudo, U.no_ville, U.newsletter, V.nom_ville_maj, V.code_postal FROM utilisateur U, villes V WHERE U.no_ville = V.id AND U.no = :iduser";
$tab_utilisateur = execute_requete($requete_utilisateur,array(":iduser"=>$_POST["id"]));

if (!empty($tab_utilisateur)) {
    $tab['id'] = $tab_utilisateur[0]["no"];
    $tab['email'] = $tab_utilisateur[0]["email"];
    $tab['pseudo'] = $tab_utilisateur[0]["pseudo"];
    $tab['no_ville'] = $tab_utilisateur[0]["no_ville"];
    $tab['nom_ville'] = $tab_utilisateur[0]["code_postal"].' '.$tab_utilisateur[0]["nom_ville_maj"];
    $tab['newsletter'] = $tab_utilisateur[0]["newsletter"];
}
else {
    $return_code = '1';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
