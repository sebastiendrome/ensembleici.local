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

$requete_genre = "UPDATE genre SET libelle = :l, type_genre = :t WHERE no = :no";
$param_insert = array(":l" => $_POST["libelle"], ":no" => $_POST['no'], ":t" => $_POST['type']);
execute_requete($requete_genre,$param_insert);


$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
