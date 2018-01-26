<?php
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

$return_code = '0';
$tab = array();

$table = $_POST['page_origine'].'_';
switch ($_POST['type']) {
    case 1 : $table .= 'evenement'; $type = 'Evénement'; break;
    case 2 : $table .= 'petiteannonce'; $type = 'Petite annonce'; break;
    case 3 : $table .= 'structure'; $type = 'Structure'; break;
}

$liste = array('evenement_evenement', 'evenement_petiteannonce', 'evenement_structure', 'petiteannonce_petiteannonce', 'petiteannonce_structure', 'structure_structure');
if (!in_array($table, $liste)) {
    $inver = explode('_', $table); 
    $table = $inver[1].'_'.$inver[0];
    $no2 = $_POST['no_origine']; $no1 = $_POST['no_lie'];
}
else {
    $no1 = $_POST['no_origine']; $no2 = $_POST['no_lie'];
}

switch ($table) {
    case 'evenement_evenement' : $fields = 'no1, no2'; break;
    case 'evenement_petiteannonce' : $fields = 'no_evenement, no_petiteannonce'; break;
    case 'evenement_structure' : $fields = 'no_evenement, no_structure'; break;
    case 'petiteannonce_petiteannonce' : $fields = 'no1, no2'; break;
    case 'petiteannonce_structure' : $fields = 'no_petiteannonce, no_structure'; break;
    case 'structure_structure' : $fields = 'no1, no2'; break;
    default: break;
}

$req_insert = "INSERT INTO $table ($fields) VALUES (:no1, :no2)";
$param = array(':no1' => $no1, ':no2' => $no2);
execute_requete($req_insert,$param);


$tab['code'] = $return_code; 
$tab['type'] = $type;
$reponse = json_encode($tab); 
echo $reponse; 
?>
