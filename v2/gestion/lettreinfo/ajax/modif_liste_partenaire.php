<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["n"])&&!empty($_POST["n"])){
	$liste = urldecode($_POST["l"]);
	$no_lettre = $_POST["n"];
	$requete_update = "UPDATE lettreinfo_partenaireinstitutionnel SET liste=:l WHERE no_lettre=:no";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":no"=>$no_lettre,":l"=>$liste)) or die ("requete ligne 19 : ".$requete_update);
	echo json_encode(true);
}
else{
	echo json_encode(false);
}
?>