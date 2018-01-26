<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["no_lettre"])&&!empty($_POST["no_lettre"])&&isset($_POST["no_pub"])&&!empty($_POST["no_pub"])&&isset($_POST["pos"])&&!empty($_POST["pos"])){
	$no_pub = $_POST["no_pub"];
	$no_lettre = $_POST["no_lettre"];
	$position = $_POST["pos"];
	if(isset($_POST["ajout"])&&$_POST["ajout"]==1){
		$requete_insert = "INSERT INTO lettreinfo_publicite(no_lettre,no_publicite,position) VALUES(:no_lettre,:no_pub,:pos)";
		$res = $connexion->prepare($requete_insert);
		$r = $requete_insert;
	}
	else{
		$requete_update = "UPDATE lettreinfo_publicite SET no_publicite=:no_pub WHERE no_lettre=:no_lettre AND position=:pos";
		$res = $connexion->prepare($requete_update);
		$r = $requete_update;
	}
	$res->execute(array(":no_lettre"=>$no_lettre,":no_pub"=>$no_pub,":pos"=>$pos)) or die ("requete ligne 19 : ".$r);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>