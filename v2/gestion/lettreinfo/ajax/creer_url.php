<?php
include "../../../01_include/_var_ensemble.php";
include "../../../01_include/_connect.php";
$t = urldecode($_POST["t"]);
//Conditionnelles de sécurités
$continuer = true;
if($t!="evenement"&&$t!="structure"&&$t!="petiteannonce"){
	$continuer = !$continuer;
}
else{
	if($t!="structure"){
		$champ = "titre";
	}
	else{
		$champ = "nom";
	}
}
if($continuer){
	$req = "SELECT ".$champ." AS t FROM ".$t." WHERE no=:no";
	$res = $connexion->prepare($req);
	$res->execute(array(":no"=>$_POST["n"]));
	$tab = $res->fetchAll();
	$reponse = array(true,utf8_encode($root_site.$t.".lettreinfo.".url_rewrite(coupe_chaine($tab[0]["t"],130,false)).".[**idv**].".$_POST["n"].".html"));
}
else{
	$reponse = array(false,utf8_encode("une erreur de sécurité est survenue !"));
}
echo json_encode($reponse);
?>