<?php
header('Content-type: text/plain; charset=utf-8');
//require_once('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include_once("/home/ensemble/www/00_dev_sam/01_include/_fonctions.php");
include_once("/home/ensemble/01_include/_session_start.php");
include_once('/home/ensemble/01_include/_var_ensemble.php');
include_once("/home/ensemble/01_include/_fonctions.php");
include_once("/home/ensemble/01_include/_init_var.php");

$connect = connexion($_POST["email"],$_POST["mdp"],true);
if($connect[0]){
	$return = array(true,array());
	$requete_menu = "SELECT * FROM  administrationMenu JOIN droit_administrationMenu ON droit_administrationMenu.no_administrationMenu=administrationMenu.no WHERE droit_administrationMenu.no_droit=:no_droit";
	$return[1]["menu"] = execute_requete($requete_menu,array(":no_droit"=>$_SESSION["droit"]["no"]));
	$return[1]["message"] = $connect[1];
	$return[1]["fonction"] = $_SESSION["droit"]["libelle"];
	$return[1]["pseudo"] = (!empty($_SESSION["utilisateur"]["pseudo"]))?$_SESSION["utilisateur"]["pseudo"]:$_SESSION["utilisateur"]["email"];
}
else
	$return = $connect;
echo json_encode($return);
?>
