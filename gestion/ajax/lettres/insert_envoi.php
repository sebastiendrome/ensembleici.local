<?php
session_start();
//1. Initialisation de la session
include "../../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../../01_include/_init_var.php";

$return_code = '0';
$requete_lettre = "SELECT * FROM lettreinfo WHERE no=:no_l";
$res_lettre = $connexion->prepare($requete_lettre);
$res_lettre->execute(array(":no_l" => $_POST['ref']));
$tab_lettre = $res_lettre->fetch();

if (isset($tab_lettre['no'])) {
    // insertion dans la table de cron
    $requete_lettre = "INSERT INTO envoilettres (no_envoi) VALUES (:no_envoi)";
    $res_lettre = $connexion->prepare($requete_lettre);
    $res_lettre->execute(array(":no_envoi" => $tab_lettre['no_envoi']));
} 
else {
    $return_code = '1';
}

$tab = array();
$tab['code'] = $return_code;
$reponse = json_encode($tab); 
echo $reponse;
?>
