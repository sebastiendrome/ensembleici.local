<?php
// Affichage des pages villes
session_name("EspacePerso");
session_start();
//if(!isset($_SESSION['date_pa']) || ($_SESSION['date_pa']=="")) $_SESSION['date_pa']=1;
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
if(est_connecte()){
	//On regarde soit que l'utilisateur est admin.
	$continuer = est_admin();
	if(!$continuer){
		//Sinon on regarde que l'utilisateur modifie un message qui lui appartient.
		$requete_user = "SELECT no FROM messageForum WHERE no_utilisateur_creation=:nou AND no=:no";
		$res_user = $connexion->prepare($requete_user);
		$res_user->execute(array(":no"=>$no,":nou"=>$_SESSION["UserConnecte_id"]));
		$tab_user = $res_user->fetchAll();
		$continuer = (count($tab_user)>0);
	}
	
	if($continuer){
		$no = $_POST["no"];
		if($no>0){
			$requete_update = "UPDATE messageForum SET afficher=0 WHERE no=:no";	
			$res_update = $connexion->prepare($requete_update);
			$res_update->execute(array(":no"=>$no));
			$reponse = array(true,"");
		}
		else
			$reponse = array(false,"Une erreur est survenue, veuillez réessayer...");
	}
	else
		$reponse = array(false, "vous n'avez pas les droits nescessaires");
}
else{
	$reponse = array(false,"Vous n'êtes plus connecté");
}
echo json_encode($reponse);
?>
