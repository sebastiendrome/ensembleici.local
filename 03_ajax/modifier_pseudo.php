<?php
header('Content-type: text/plain; charset=utf-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";
//On vérifie que l'utilisateur est connecté
if(est_connecte()){
	//On vérifie que le pseudo n'est pas vide
	if(!empty($_POST["input_pseudo"])){
		$_POST["input_pseudo"] = urldecode($_POST["input_pseudo"]);
		$param_requete = array(":p"=>$_POST["input_pseudo"],":no"=>$_SESSION["utilisateur"]["no"]);
		$requete_existe = "SELECT no FROM utilisateur WHERE pseudo=:p AND no<>:no";
		$tab_existe = execute_requete($requete_existe,$param_requete);
		if(empty($tab_existe)){
			//On modifie le pseudo
			$requete_update = "UPDATE utilisateur SET pseudo=:p WHERE no=:no";
			execute_requete($requete_update,$param_requete);
			$_SESSION["utilisateur"]["pseudo"] = $_POST["input_pseudo"];
			$return = array(true,"Votre nom d'utilisateur est à présent ".$_POST["input_pseudo"].". Vous pouvez le modifier depuis votre espace personnel.");
		}
		else{
			$return = array(false,"Ce nom d'utilisateur est déjà utilisé");
		}
	}
	else{
		$return = array(false,"Veuillez saisir un nom d'utilisateur");
	}
}
else{
	$return = array(false,"Vous devez être connecté pour modifier votre nom d'utilisateur");
}
echo json_encode($return);
?>
