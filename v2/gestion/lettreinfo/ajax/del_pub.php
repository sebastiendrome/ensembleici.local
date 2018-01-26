<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["no_lettre"])&&!empty($_POST["no_lettre"])&&isset($_POST["pos"])&&!empty($_POST["pos"])){
	$no_pub = $_POST["no_pub"];
	$no_lettre = $_POST["no_lettre"];
	$position = $_POST["pos"];
	$requete_del = "DELETE FROM lettreinfo_publicite WHERE no_lettre=:no_lettre AND position=:pos";
	$res_del = $connexion->prepare($requete_del);
	$res_del->execute(array(":no_lettre"=>$no_lettre,":pos"=>$pos)) or die ("requete ligne 19 : ".$requete_del);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>