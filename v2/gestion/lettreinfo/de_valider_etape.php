<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["e"])&&!empty($_POST["e"])&&isset($_POST["no_l"])&&!empty($_POST["no_l"])){
	$etape = urldecode($_POST["e"]);
	$no_lettre = $_POST["no_l"];
	if($etape=="repertoire")
		$requete_update = "UPDATE lettreinfo_repertoire SET etape_valide=0 WHERE no_lettre=:no";
	else if($etape=="petiteannonce")
		$requete_update = "UPDATE lettreinfo_petiteannonce SET etape_valide=0 WHERE no_lettre=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_lettre)) or die ("requete ligne 19 : ".$requete_update);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>