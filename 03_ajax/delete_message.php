<?php
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";
//1. On vérifie les paramètres
if(!empty($_POST["no"])){
	$_POST["c"] = urldecode($_POST["c"]);
	//2. On vérifie la connexion
	if(est_connecte()){
		$requete_update = "UPDATE message SET afficher=0 WHERE no=:no";
		execute_requete($requete_update,array(":no"=>$_POST["no"]));
		$return = array(true,"Ce contenu a été supprimé.");
	}
	else
		$return = array(false,"Vous devez être connecté pour supprimer un contenu");
}
else
	$return = array(false,"Une erreur est survenue, veuillez réessayer.");
echo json_encode($return);
?>
