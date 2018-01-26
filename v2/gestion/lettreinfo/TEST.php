<?php
require_once('../../01_include/_connect.php');
//Récupération des utilisateurs
$cle_cryptage="JjEJb5eV30EBNLFtm2wNrk9afjz612B6fxVfo7jQ86ZybNoXuQ"; // = Salt
$req = "SELECT no,email FROM newsletter";
$res = $connexion->prepare($req);
$res->execute() or die("erreur requete générale");
$usrs = $res->fetchAll();
//Boucle sur les utilisateur : remplissage du champs code_desinscription_nl
for($i=0;$i<count($usrs);$i++){
	$cde = md5($usrs[$i]["email"].$cle_cryptage);
	//On update le champ
	$req_up = "UPDATE newsletter SET code_desinscription_nl=:c WHERE no=:no";
	$res_up = $connexion->prepare($req_up);
	$res_up->execute(array(":no"=>$usrs[$i]["no"],":c"=>$cde)) or die("erreur usr : ".$usrs[$i]["no"]."<br/>");
	echo "utilisateur ".$usrs[$i]["no"]." traité<br/>";
}
?>