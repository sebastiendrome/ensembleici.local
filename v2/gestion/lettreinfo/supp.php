<?php
session_name("EspacePerso");
session_start();
if($_POST["no"]!=""){
	require_once "config.php";
	//On supprime toutes les étapes de la lettre :
	$requete_agenda = "DELETE FROM lettreinfo_agenda WHERE no_lettre=:no";
	$requete_edito = "DELETE FROM lettreinfo_edito WHERE no_lettre=:no";
	$requete_petiteannonce = "DELETE FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
	$requete_publicite = "DELETE FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
	$requete_repertoire = "DELETE FROM lettreinfo_repertoire WHERE no_lettre=:no";
	$requete_partenaireinstitutionnel = "DELETE FROM lettreinfo_partenaireinstitutionnel WHERE no_lettre=:no";
	$requete_principale = "DELETE FROM lettreinfo WHERE no=:no";
	
	$res_agenda = $connexion->prepare($requete_agenda);
	$res_edito = $connexion->prepare($requete_edito);
	$res_petiteannonce = $connexion->prepare($requete_petiteannonce);
	$res_publicite = $connexion->prepare($requete_publicite);
	$res_repertoire = $connexion->prepare($requete_repertoire);
	$res_partenaireinstitutionnel = $connexion->prepare($requete_partenaireinstitutionnel);
	$res_principale = $connexion->prepare($requete_principale);
	
	$res_agenda->execute(array(":no"=>$_POST["no"]));
	$res_edito->execute(array(":no"=>$_POST["no"]));
	$res_petiteannonce->execute(array(":no"=>$_POST["no"]));
	$res_publicite->execute(array(":no"=>$_POST["no"]));
	$res_repertoire->execute(array(":no"=>$_POST["no"]));
	$res_partenaireinstitutionnel->execute(array(":no"=>$_POST["no"]));
	$res_principale->execute(array(":no"=>$_POST["no"]));
}
?>