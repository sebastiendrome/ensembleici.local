<?php
// Affichage des pages villes
session_name("EspacePerso");
session_start();
//if(!isset($_SESSION['date_pa']) || ($_SESSION['date_pa']=="")) $_SESSION['date_pa']=1;
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
if(est_connecte()){
	$no_forum = $_POST["no_forum"];
	$no_message = $_POST["no_message"];
	$no_utilisateur = $_SESSION["UserConnecte_id"];
	$inscrit = $_POST["inscrit"];
	
	$requete_existe = "SELECT inscrit FROM forum_inscription WHERE no_utilisateur=:nou AND no_forum=:nof AND no_message=:nom";
	$res_existe = $connexion->prepare($requete_existe);
	$res_existe->execute(array(":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message));
	$tab_existe = $res_existe->fetchAll();
	if(count($tab_existe)>0){
		$requete_update = "UPDATE forum_inscription SET inscrit=:i WHERE no_utilisateur=:nou AND no_forum=:nof AND no_message=:nom";
		$res_update = $connexion->prepare($requete_update);
		$res_update->execute(array(":i"=>$inscrit,":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message));
	}
	else{
		$requete_insert = "INSERT INTO forum_inscription VALUES(:nou,:nof,:nom,:i)";
		$res_insert = $connexion->prepare($requete_insert);
		$res_insert->execute(array(":i"=>$inscrit,":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message));
	}
	$reponse = true;
}
else{
	$reponse = false;
}
echo json_encode($reponse);
?>
