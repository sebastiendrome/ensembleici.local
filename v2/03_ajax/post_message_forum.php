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
	$commentaire = ($no_message!=0);
	$contenu = urldecode($_POST["contenu"]);
	$no_utilisateur = $_SESSION["UserConnecte_id"];
	
	$requete_post = "INSERT INTO messageForum(contenu,no_utilisateur_creation,date_creation,no_forum,no_message) VALUES(:c,:nou,NOW(),:nof,:nom)";	
	$res_post = $connexion->prepare($requete_post);
	$res_post->execute(array(":c"=>$contenu,":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message));
	$no_message_poste = $connexion->lastInsertId();
	
	//Inscription automatique au fil du message
	if($commentaire){ //C'est un commentaire, on inscrit l'utilisateur au fil du message commenté.
		$no_message_fil = $no_message;
	}
	else{ //C'est un message, on inscrit l'utilisateur au fil de son propre message.
		$no_message_fil = $no_message_poste;
	}
	$requete_inscrit = "SELECT * FROM forum_inscription WHERE no_message=:nom AND no_forum=:nof AND no_utilisateur=:nou";
	$res_inscrit = $connexion->prepare($requete_inscrit);
	$res_inscrit->execute(array(":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message));
	$tab_inscrit = $res_inscrit->fetchAll();
	if(count($tab_inscrit)==0){
		$requete_inscription = "INSERT INTO forum_inscription VALUES(:nou,:nof,:nom,1)";
		$res_inscription = $connexion->prepare($requete_inscription);
		$res_inscription->execute(array(":nou"=>$no_utilisateur,":nof"=>$no_forum,":nom"=>$no_message_file));
	}
	
	//On envoi la notification aux abonnés et aux admins.
	include "envoyer_notifications.php";
	
	$reponse = array("no_message"=>$no_message_poste,"date_modification"=>datefr(date("Y-m-d H:i:s"),true,false),"no_utilisateur"=>$no_utilisateur);
}
else{
	$reponse = false;
}
echo json_encode($reponse);
?>
