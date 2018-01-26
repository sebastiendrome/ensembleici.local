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
$mapage = $_POST['page'];
if ($mapage == 'petite-annonce') {
    $mapage = 'petiteannonce';
}

$requete_fiche = "SELECT no FROM $mapage WHERE no = :no";
$tab_fiche = execute_requete($requete_fiche,array(":no"=>$_POST["id"]));

if (!empty($tab_fiche)) {
    $validation = 1;
    $requete_maj = "UPDATE $mapage SET validation = :validation WHERE no = :no";
    execute_requete($requete_maj,array(":no" => $_POST["id"], ':validation' => $validation));
}
else {
    $return_code = '1';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
