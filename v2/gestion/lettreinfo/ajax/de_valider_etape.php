<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["e"])&&!empty($_POST["e"])&&isset($_POST["no_l"])&&!empty($_POST["no_l"])){
	$etape = urldecode($_POST["e"]);
	$no_lettre = $_POST["no_l"];
	if($etape=="repertoire"||$etape=="structure")
		$requete_liste = "SELECT liste_structure_valide AS l FROM lettreinfo_repertoire WHERE no_lettre=:no_l";
	else if($etape=="petiteannonce")
		$requete_liste = "SELECT liste_petiteannonce_valide AS l FROM lettreinfo_petiteannonce WHERE no_lettre=:no_l";
	else if($etape=="agenda")
		$requete_liste = "SELECT liste_evenement_valide AS l FROM lettreinfo_agenda WHERE no_lettre=:no_l";
	if(isset($requete_liste)&&!empty($requete_liste)){
		$res_liste = $connexion->prepare($requete_liste);
		$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
		$tab_liste = $res_liste->fetchAll();
		$liste_valide = $tab_liste[0]["l"];
	}
	
	if($etape=="repertoire"||$etape=="structure"){
		$requete_update = "UPDATE lettreinfo_repertoire SET etape_valide=0 WHERE no_lettre=:no";
		if($liste_valide!="")
			$requete_boite = "UPDATE structure SET apparition_lettre=apparition_lettre-1 WHERE no IN (".$liste_valide.")";
	}
	else if($etape=="petiteannonce"){
		$requete_update = "UPDATE lettreinfo_petiteannonce SET etape_valide=0 WHERE no_lettre=:no";
		if($liste_valide!="")
			$requete_boite = "UPDATE petiteannonce SET apparition_lettre=apparition_lettre-1 WHERE no IN (".$liste_valide.")";
	}
	else if($etape=="agenda"){
		$requete_update = "UPDATE lettreinfo_agenda SET etape_valide=0 WHERE no_lettre=:no";
		if($liste_valide!="")
			$requete_boite = "UPDATE evenement SET apparition_lettre=apparition_lettre-1 WHERE no IN (".$liste_valide.")";
	}
	else if($etape=="edito")
		$requete_update = "UPDATE lettreinfo_edito SET etape_valide=0 WHERE no_lettre=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_update);
	if(isset($requete_boite)&&!empty($requete_boite)){
		$res_boite = $connexion->prepare($requete_boite);
		$res_boite->execute() or die ("requete ligne 19 : ".$requete_boite);
	}
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>