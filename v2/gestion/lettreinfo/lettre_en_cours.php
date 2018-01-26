<?php
	session_name("EspacePerso");
	session_start();
	include "config.php";
	// require_once('../../../01_include/_connect.php');
	$no_lettre = $_REQUEST["no_lettre"];
	//On regarde pour chaque étape, si elle existe pour no_lettre, et si elle est validée
	$requete_edito = "SELECT etape_valide AS e FROM lettreinfo_edito WHERE no_lettre=:no";
	$requete_repertoire = "SELECT etape_valide AS e FROM lettreinfo_repertoire WHERE no_lettre=:no";
	$requete_agenda = "SELECT etape_valide AS e FROM lettreinfo_agenda WHERE no_lettre=:no";
	$requete_petiteannonce = "SELECT etape_valide AS e FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
	
	$res_edito = $connexion->prepare($requete_edito);
	$res_repertoire = $connexion->prepare($requete_repertoire);
	$res_agenda = $connexion->prepare($requete_agenda);
	$res_petiteannonce = $connexion->prepare($requete_petiteannonce);
	
	$res_edito->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_edito);
	$res_repertoire->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_repertoire);
	$res_agenda->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_agenda);
	$res_petiteannonce->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_petiteannonce);
	
	$tab_edito = $res_edito->fetchAll();
	$tab_repertoire = $res_repertoire->fetchAll();
	$tab_agenda = $res_agenda->fetchAll();
	$tab_petiteannonce = $res_petiteannonce->fetchAll();
	
	if(count($tab_edito)>0)
		$valide_edito = (bool)$tab_edito[0]["e"];
	else
		$valide_edito = false;
	if(count($tab_repertoire)>0)
		$valide_repertoire = (bool)$tab_repertoire[0]["e"];
	else
		$valide_repertoire = false;
	if(count($tab_agenda)>0)
		$valide_agenda = (bool)$tab_agenda[0]["e"];
	else
		$valide_agenda = false;
	if(count($tab_petiteannonce)>0)
		$valide_petiteannonce = (bool)$tab_petiteannonce[0]["e"];
	else
		$valide_petiteannonce = false;
	
	if($valide_edito){
		include "lettre_en_cours/edito.php";
	}
	if($valide_agenda){
		include "lettre_en_cours/agenda.php";
	}
	if($valide_repertoire){
		include "lettre_en_cours/repertoire.php";
	}
	if($valide_petiteannonce){
		include "lettre_en_cours/petiteannonce.php";
	}
?>