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
    $requete_select = "SELECT MAX(ordre) as  max FROM diaporamas WHERE territoires_id = :t";
    $tab_select = execute_requete($requete_select,array(":t" => $_SESSION["utilisateur"]["territoire"]));
    $ordre = $tab_select[0]['max'] + 1;

    
    $requete_diapo = "INSERT INTO diaporamas (nom, valide, territoires_id, ordre) VALUES (:n, 1, :t, :o)";
    $param_diapo = array(":n" => $_POST['nom'], ":t" => $_SESSION["utilisateur"]["territoire"], ":o" => $ordre);
    execute_requete($requete_diapo,$param_diapo);
}
else {
    $return_code = '10';
}


$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
