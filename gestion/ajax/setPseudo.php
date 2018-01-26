<?php
header('Content-Type: text/plain; charset=UTF-8');
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
if(!empty($_POST["p"])){ //Il faut alors renseigner le pseudo de l'utilisateur
	//On vérifie d'abord que le pseudo sélectionné n'existe pas déjà pour quelqu'un
	$requete_existe_pseudo = "SELECT pseudo FROM utilisateur WHERE pseudo=:p";
	if(count_requete($requete_existe_pseudo,array(":p"=>urldecode($_POST["p"])))==0){ //On peut utiliser ce pseudo
		$requete_pseudo = "UPDATE utilisateur SET pseudo=:p WHERE no=:no";
		execute_requete($requete_pseudo,array(":p"=>urldecode($_POST["p"]),":no"=>$_SESSION["utilisateur"]["no"]));
		$_SESSION["utilisateur"]["pseudo"] = $_POST["p"];
		$reponse = array(true,"Votre nom d'utilisateur a été modifié avec succés ".$_POST["p"]);
	}
	else //Ce pseudo est déjà utilisé
		$reponse = array(false,"Oups, ce nom d'utilisateur est déjà pris...");
}
else
	$reponse = array(false,"Une erreur est survenue...");
echo json_encode($reponse);
?>