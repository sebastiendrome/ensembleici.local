<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["l"])&&!empty($_POST["l"])&&isset($_POST["no_l"])&&!empty($_POST["no_l"])){
	$liste = urldecode($_POST["l"]);
	$no_lettre = $_POST["no_l"];
	$etape = urldecode($_POST["e"]);
	//On met à jour la liste
	if($etape=="repertoire"||$etape=="structure")
		$requete_update = "UPDATE lettreinfo_repertoire SET liste_structure=:l WHERE no_lettre=:no";
	else if($etape=="petiteannonce")
		$requete_update = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce=:l WHERE no_lettre=:no";
	else if($etape=="agenda")
		$requete_update = "UPDATE lettreinfo_agenda SET liste_evenement=:l WHERE no_lettre=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_lettre,":l"=>$liste)) or die ("requete ligne 19 : ".$requete_update);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>