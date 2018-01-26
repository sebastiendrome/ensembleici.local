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

if ($_POST['type'] == 1) {
    $table = 'partenaireinstitutionnel';
    $image = "img/".$_POST['image'];
}
else {
    $table = 'collectifs'; 
    $image = $_POST['image'];
}

$requete_statut = "INSERT INTO $table (libelle, image, url, territoires_id) VALUES(:l, :i, :u, :t)";
$param_insert = array(":l"=>$_POST["nom"], ":i" => $image, ':u' => $_POST['site'], ':t' => $_SESSION["utilisateur"]["territoire"]);
execute_requete($requete_statut,$param_insert);


$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
