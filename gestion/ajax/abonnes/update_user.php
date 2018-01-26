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

if ($_POST['droits'] == 'U') {
    $droits = '';
}
else {
    $droits = $_POST['droits'];
}

if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $requete_utilisateur = "UPDATE newsletter SET email = :e, no_ville = :no_ville WHERE no = :no";
    $param_insert = array(":e" => $_POST["email"], ":no_ville" => $_POST["ville"], ":no" => $_POST['no']);
    execute_requete($requete_utilisateur,$param_insert);
}
else {
    $return_code = '2';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
