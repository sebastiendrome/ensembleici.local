<?php
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
    if (filter_var($_POST['newsletter'], FILTER_VALIDATE_EMAIL)) {
        if (filter_var($_POST['facebook'], FILTER_VALIDATE_URL)) {
            $requete_territoire = "UPDATE territoires SET mail_newsletter = :m, facebook = :f";
            if ($_POST['code_ua'] != '') {
                $requete_territoire .= ", code_ua = '".$_POST['code_ua']."'";
            }
            $requete_territoire .= " WHERE id = :id";
            $param_territoire = array(":m" => $_POST['newsletter'], ":f" => $_POST['facebook'], ":id" => $_SESSION["utilisateur"]["territoire"]);
            execute_requete($requete_territoire,$param_territoire);
        }
        else {
            $return_code = '12';
        }
    }
    else {
        $return_code = '11';
    }
}
else {
    $return_code = '10';
}


$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
