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
if(est_connecte()){
    $user = $_SESSION["utilisateur"]["email"];
    $id = $_POST['id'];
    $page = $_POST['page'];
    $now = date('Y-m-d H:i:s');
    $motif = filter_var($_POST['motif'], FILTER_SANITIZE_STRING);

    $requete = "INSERT INTO signalements (creation_date, utilisateurs_email, no, donnees, motif) VALUES ('".$now."', '".$user."', $id, '".$page."', '".$motif."')";
    $res = $connexion->prepare($requete);
    $res->execute();
}
else {
    $return_code = '10';
}

$tab = array(); 
$tab['code'] = $return_code; 

$reponse = json_encode($tab); 
echo $reponse; 
?>
