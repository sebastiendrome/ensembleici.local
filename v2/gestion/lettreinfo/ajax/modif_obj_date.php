<?php
require_once('../../../01_include/_connect.php');
if(isset($_POST["no"])&&$_POST["no"]!=""&&$_POST["no"]!=0){
	if(isset($_POST["o"])&&$_POST["o"]!=""){
		$requete = "UPDATE lettreinfo SET objet=:o WHERE lettreinfo.no=:no";
		$res = $connexion->prepare($requete);
		$res->execute(array(":o"=>urldecode($_POST["o"]),":no"=>$_POST["no"]));
		$reponse = array(true,utf8_encode("Objet modifié."));
	}
	else if(isset($_POST["d"])&&$_POST["d"]!=""){
		$requete = "UPDATE lettreinfo SET date_debut=:d WHERE lettreinfo.no=:no";
		$res = $connexion->prepare($requete);
		$res->execute(array(":d"=>date('Y-m-d',urldecode($_POST["d"])),":no"=>$_POST["no"]));
		$reponse = array(true,utf8_encode("Date de début modifiée."));
	}
	else{
		$reponse = array(false,utf8_encode("une erreur est survenue, veuillez réessayer !"));
	}
}
else{
	$reponse = array(false,utf8_encode("une erreur est survenue, veuillez réessayer !"));
}
echo json_encode($reponse);
?>