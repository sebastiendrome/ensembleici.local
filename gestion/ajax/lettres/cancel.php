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
$tab = array();
$no = $_POST['ref'];

$requete_select = "SELECT * FROM lettreinfo WHERE no = :no";
$res_select = $connexion->prepare($requete_select);
$res_select->execute(array(":no" => $no));
$tab_select = $res_select->fetch();

if (isset($tab_select['no'])) {
    $requete_delete = "DELETE FROM lettreinfo_envoi WHERE no = :no_envoi";
    $res_delete = $connexion->prepare($requete_delete);
    $res_delete->execute(array(":no_envoi" => $tab_select['no_envoi']));
    
    $requete_update_lettre = "UPDATE lettreinfo SET no_envoi = 0 WHERE no = :no";
    $res_update_lettre = $connexion->prepare($requete_update_lettre);
    $res_update_lettre->execute(array(":no" => $no));
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code;
$reponse = json_encode($tab); 
echo $reponse;
?>