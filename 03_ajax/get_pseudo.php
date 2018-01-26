<?php
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";
if(empty($_POST["no"])&&est_connecte())
	$_POST["no"] = $_SESSION["utilisateur"]["no"];
if(!empty($_POST["no"])){
	$requete_pseudo = "SELECT pseudo FROM utilisateur WHERE no=:no";
	$tab_pseudo = execute_requete($requete_pseudo,array(":no"=>$_POST["no"]));
	$return = $tab_pseudo[0]["pseudo"];
}
else
	$return = "";
echo $return;
?>

