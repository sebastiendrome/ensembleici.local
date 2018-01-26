<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["e"])&&!empty($_POST["e"])&&isset($_POST["no_l"])&&!empty($_POST["no_l"])){
	$etape = urldecode($_POST["e"]);
	$no_lettre = $_POST["no_l"];
	//On récupère la liste
	if($etape=="repertoire")
		$requete_liste = "SELECT liste_structure FROM lettreinfo_repertoire WHERE no_lettre=:no_l";
	else if($etape=="petiteannonce")
		$requete_liste = "SELECT liste_petiteannonce FROM lettreinfo_petiteannonce WHERE no_lettre=:no_l";
		$res_liste = $connexion->prepare($requete_liste);
		$res_liste->execute(array(":no_l"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_liste);
		$tab_liste = $res_liste->fetchAll();
		$liste_valide = $tab_liste[0]["liste_structure"];
	//On met à jour la liste
	if($etape=="repertoire")
		$requete_update = "UPDATE lettreinfo_repertoire SET liste_structure_valide=:l, etape_valide=1 WHERE no_lettre=:no";
	else if($etape=="petiteannonce")
		$requete_update = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce_valide=:l, etape_valide=1 WHERE no_lettre=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_lettre,":l"=>$liste_valide)) or die ("requete ligne 19 : ".$requete_update);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>