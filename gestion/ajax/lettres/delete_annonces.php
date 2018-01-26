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
$no = $_POST['no'];

if (isset($_SESSION["utilisateur"]["territoire"])) {
    $requete = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce_valide = '', date_modification = CURRENT_TIMESTAMP, etape_valide = 0 WHERE no_lettre = :no";
    $res_requete = $connexion->prepare($requete);
    $res_requete->execute(array(":no" => $no));
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
