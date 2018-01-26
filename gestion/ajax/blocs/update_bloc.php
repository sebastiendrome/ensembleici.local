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

$requete_bloc = "UPDATE contenu_blocs SET titre = :t, contenu = :c WHERE no = :no";
$param_insert = array(":t" => $_POST["titre"], ":c" => $_POST["contenu"], ":no" => $_POST['no']);
execute_requete($requete_bloc,$param_insert);


$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
