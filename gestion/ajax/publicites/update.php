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
if (isset($_SESSION["utilisateur"]["territoire"])) {
    if (($_POST['site'] != '') && !filter_var($_POST['site'], FILTER_VALIDATE_URL)) {
        $return_code = '11';
    }  
    else {
        if (checkdate(substr($_POST['debut'], 3, 2), substr($_POST['debut'], 0, 2), substr($_POST['debut'], 6, 4)) && 
                checkdate(substr($_POST['fin'], 3, 2), substr($_POST['fin'], 0, 2), substr($_POST['fin'], 6, 4))) {
            $date_debut = substr($_POST['debut'], 6, 4).'-'.substr($_POST['debut'], 3, 2).'-'.substr($_POST['debut'], 0, 2);
            $date_fin = substr($_POST['fin'], 6, 4).'-'.substr($_POST['fin'], 3, 2).'-'.substr($_POST['fin'], 0, 2);
            $requete_publicite= "UPDATE publicites SET titre = :titre, validite_du = :debut, validite_au = :fin, site = :site WHERE no = :no";
            $param_insert = array(":titre" => $_POST['titre'], ":debut" => $date_debut, ":fin" => $date_fin, ":site" => $_POST['site'], ":no" => $_POST['no']);
            execute_requete($requete_publicite,$param_insert);
        }
        else {
            $return_code = '12';
        }
        
    }
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
