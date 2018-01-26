<?php
header('Content-Type: text/plain; charset=UTF-8');
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
if(!empty($_POST["no"])&&!empty($_POST["type"])){
	if($_POST["type"]=="evenement"){
		$nomTablePrincipale = "evenement";
		$libelle = "événement";
		$ce = "cet";
		$active = (($_POST["etat"]>0)?"":"dés")."activé";
	}
	else if($_POST["type"]=="editorial"){
		$nomTablePrincipale = "editorial";
		$libelle = "article";
		$ce = "cet";
		$active = (($_POST["etat"]>0)?"":"dés")."activé";
	}
	else if($_POST["type"]=="structure"){
		$nomTablePrincipale = "structure";
		$libelle = "structure";
		$ce = "cette";
		$active = (($_POST["etat"]>0)?"":"dés")."activée";
	}
	else if($_POST["type"]=="forum"){
		$nomTablePrincipale = "forum";
		$libelle = "sujet";
		$ce = "ce";
		$active = (($_POST["etat"]>0)?"":"dés")."activé";
	}
	else{
		$nomTablePrincipale = "petiteannonce";
		$libelle = "annonce";
		$ce = "cette";
		$active = (($_POST["etat"]>0)?"":"dés")."activée";
	}
	$requete_etat = "UPDATE ".$nomTablePrincipale." SET etat=:e WHERE no=:no";
	if(execute_requete($requete_etat,array(":no"=>$_POST["no"],":e"=>(($_POST["etat"]>0)?1:0)))>0)
		$return = array(true,$libelle." ".$active);
	else
		$return = array(false,"Il semblerait que ".$ce." ".$libelle." n'existe plus...");
}
else
	$return = array(false,"Une erreur est survenue...");
echo json_encode($return);
?>
