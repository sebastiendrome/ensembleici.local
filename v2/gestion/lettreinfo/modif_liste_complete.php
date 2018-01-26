<?php
require_once('../../../01_include/_connect.php');
function retirer_liste($no,$liste,$separateur=","){
	$l = explode($separateur,$liste);
	$l_return = array();
	for($i=0;$i<count($l);$i++){
		if($no!=$l[$i])
			$l_return[] = $l[$i];
	}
	return implode($separateur,$l_return);
}
function ajouter_liste($no,$liste,$separateur=","){
	if($liste=="")
		$liste = $no;
	else
		$liste .= $separateur.$no;
	return $liste;
}
function taille_liste($liste,$separateur=","){
	$l = explode($separateur,$liste);
	return count($l);
}
if(isset($_POST["no"])&&!empty($_POST["no"])&&isset($_POST["no_l"])&&!empty($_POST["no_l"])){
	$no = $_POST["no"];
	$no_lettre = $_POST["no_l"];
	$etape = urldecode($_POST["e"]);
	//On récupère les liste
		if($etape=="repertoire")
			$requete_liste = "SELECT liste_structure_complete AS lc,liste_structure AS l FROM lettreinfo_repertoire WHERE no_lettre=:no_l";
		else if($etape=="petiteannonce")
			$requete_liste = "SELECT liste_petiteannonce_complete AS l_c,liste_petiteannonce AS l FROM lettreinfo_petiteannonce WHERE no_lettre=:no_l";
		$res_liste = $connexion->prepare($requete_liste);
		$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
		$tab_liste = $res_liste->fetchAll();
	if($_POST["act"]=="add"){
		//Si c'est un ajout
		//On ajoute no à liste_complete
		$liste_complete = ajouter_liste($no,$tab_liste[0]["l_c"]);
		//Si la taille de la liste < 5, on ajoute aussi no à liste_structure
		if(taille_liste($tab_liste[0]["l"])<5){
			$liste = ajouter_liste($no,$tab_liste[0]["l"]);
		}
		else{
			$liste = $tab_liste[0]["l"];
		}
	}
	else if($_POST["act"]=="del"){
		//Si c'est une suppression
		//On retire no de la liste_complete
		$liste_complete = retirer_liste($no,$tab_liste[0]["l_c"]);
		//On retire éventuellement no de la liste
		$liste = retirer_liste($no,$tab_liste[0]["l"]);
	}
	//On update l'entrée
	if($etape=="repertoire")
		$requete_update = "UPDATE lettreinfo_repertoire SET liste_structure_complete=:l_c, liste_structure=:l WHERE no_lettre=:no";
	else if($etape=="petiteannonce")
		$requete_update = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce_complete=:l_c, liste_petiteannonce=:l WHERE no_lettre=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_lettre,":l_c"=>$liste_complete,":l"=>$liste)) or die ("requete ligne 19 : ".$requete_update);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>