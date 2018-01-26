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

$table = $_POST['table'];
$no1 = $_POST['no_origine']; 
$no2 = $_POST['no_lie'];

switch ($table) {
    case 'evenement_evenement' : $field1 = 'no1'; $field2 = 'no2'; break;
    case 'evenement_petiteannonce' :  $field1 = 'no_evenement'; $field2 = 'no_petiteannonce'; break;
    case 'evenement_structure' :  $field1 = 'no_evenement'; $field2 = 'no_structure'; break;
    case 'petiteannonce_petiteannonce' :  $field1 = 'no1'; $field2 = 'no2'; break;
    case 'petiteannonce_structure' :  $field1 = 'no_petiteannonce'; $field2 = 'no_structure'; break;
    case 'structure_structure' :  $field1 = 'no1'; $field2 = 'no2'; break;
    default: break;
}

$req_delete = "DELETE FROM $table WHERE $field1 = :no1 AND $field2 = :no2";
$param = array(':no1' => $no1, ':no2' => $no2);
execute_requete($req_delete,$param);

$param2 = array(':no1' => $no2, ':no2' => $no1);
execute_requete($req_delete,$param2);

$tab['code'] = $return_code; 
$tab['type'] = $type;
$reponse = json_encode($tab); 
echo $reponse; 
?>
