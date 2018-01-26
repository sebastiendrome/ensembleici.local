<?php
	//function inscriptionDesinscriptionDesAlerts($no_utilisateur, $no_ville, $checked){
		
	require ('_connect.php');
	//verifica si una persona ya ha escrito en un ville
	
	$no_utilisateur = $_POST['no_utilisateur'];
	$no_ville =  $_POST['no_ville'];
	$checked = (bool)$_POST['checked'];
	
	// Suppresion   
	$sqlp = "DELETE FROM `message_utilisateur`    
		WHERE no_sujet = :no_sujet
		AND no_utilisateur = :no_utilisateur";
		$sup= $connexion->prepare($sqlp);
		$sup->execute(array(
		':no_sujet'=>$no_ville,
		':no_utilisateur'=>$no_utilisateur	
		)) or die ("Erreur ".__LINE__." : ".$sqlp);    
	
	if ($checked){
		//echo "<script>console.log('".$sqlp."');</script>";
		// on a déjà écrit quelque chose        

		// insertion		
		$sql = "INSERT INTO `message_utilisateur` (
			`no_sujet`,
			`no_utilisateur`
			) VALUES (
			:no_sujet,
			:no_utilisateur
			)";
			
		$insert = $connexion->prepare($sql);
		$insert->execute(array(
			':no_sujet'=>$no_ville,
			':no_utilisateur'=>$no_utilisateur			
		)) or die ("Erreur case de verif : ".$sql."<br/>".print_r($insert->errorInfo()));
	}
	// else{
		// suppression
/*		
		$sql = "INSERT INTO `message_utilisateur` (
			`no_sujet`,
			`no_utilisateur`
			) VALUES (
			:no_sujet,
			:no_utilisateur
			)";
			
		$insert = $connexion->prepare($sql);
		$insert->execute(array(
			':no_sujet'=>$no_ville,
			':no_utilisateur'=>$no_utilisateur			
		)) or die ("Erreur case de verif : ".$sql."<br/>".print_r($insert->errorInfo()));*/
	// }
?>