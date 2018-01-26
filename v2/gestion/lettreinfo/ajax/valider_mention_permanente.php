<?php
header('Content-Type: text/plain; charset=UTF-8');
require_once('../../../01_include/_connect.php');
if(isset($_POST["a"])&&$_POST["a"]!=null&&isset($_POST["m"])&&$_POST["m"]!=null&&isset($_POST["n"])&&$_POST["n"]!=null){
	//Modification de la mention courante
	$requete_update = "UPDATE lettreinfo_edito SET avant=:a, mention_permanente=:m WHERE no_lettre=:n";
	$res_update = $connexion->prepare($requete_update);
	$res_update->execute(array(":a"=>$_POST["a"],":m"=>$_POST["m"],":n"=>$_POST["n"])) or die("erreur requête ligne 116 : ".$requete_update);
	
	if(isset($_POST["t"])&&$_POST["t"]!=null){ //Modification de la mention par défaut.
		$requete_update_defaut = "UPDATE lettreinfo_edito SET corps=:c, avant=:a, mention_permanente=:m WHERE no_lettre=0 AND territoires_id=:p";
		$res_update_defaut = $connexion->prepare($requete_update_defaut);
		$res_update_defaut->execute(array(":c"=>urldecode($_POST["t"]),":a"=>$_POST["a"],":m"=>$_POST["m"],":p"=>$_POST["p"])) or die("erreur requête ligne 116 : ".$requete_update);
	}
	$reponse = true;
}
else{
	$reponse = false;
}
echo json_encode($reponse)
?>
