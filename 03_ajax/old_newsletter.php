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
//0. On vérifie que toutes les variables nescessaires sont renseignées
if(!empty($_POST["input_email"])&&!empty($_POST["no_ville"])&&!empty($_POST["input_captcha"])){
	$_POST["input_email"] = urldecode($_POST["input_email"]);
	$_POST["input_captcha"] = md5(urldecode($_POST["input_captcha"]));
	//1. On vérifie le format de l'adresse mail
	if(filter_var($_POST["input_email"], FILTER_VALIDATE_EMAIL)){
		//2. On vérifie qu'un utilisateur n'existe pas déjà avec cette adresse mail
		$requete_utilisateur = "SELECT no FROM utilisateur WHERE email=:e";
		$tab_utilisateur = execute_requete($requete_utilisateur,array(":e"=>$_POST["input_email"]));
		if(empty($tab_utilisateur)){
			//3. On vérifie que l'adresse mail n'est pas déjà dans la liste de diffusion ou est désabonné
			$requete_newsletter = "SELECT no,etat FROM newsletter WHERE email=:e";
			$tab_newsletter = execute_requete($requete_newsletter,array(":e"=>$_POST["input_email"]));
			if(empty($tab_newsletter)||$tab_newsletter[0]["etat"]==0){
				//4. On vérifie le captcha
				if($_POST["input_captcha"]==$_SESSION["sysCaptchaCode"]){
					$_SESSION["sysCaptchaCode"] = ""; //Si on vide pas, ça sert à rien
					$code_desinscription_nl = id_aleatoire();
					if(empty($tab_newsletter)){
						//4. On insère l'adresse dans la liste de diffusion
						$requete_insert = "INSERT INTO newsletter(email,no_ville,etat,code_desinscription_nl,date_inscription) VALUES(:e,:v,1,:c,CURRENT_TIMESTAMP)";
						execute_requete($requete_insert,array(":e"=>$_POST["input_email"],":v"=>$_POST["no_ville"],":c"=>$code_desinscription_nl));
					}
					else{
						//4.1 Ou on passe l'abonnement à 1
						$requete_update = "UPDATE newsletter SET etat=1,code_desinscription_nl=:c WHERE no=:no"; 
						execute_requete($requete_update,array(":c"=>$code_desinscription_nl,":no"=>$tab_newsletter[0]["no"]));
					}
					$return = array(true,"L'adresse ".$_POST["input_email"]." est maintenant abonnée à notre lettre d'information");
				}
				else{
					$return = array(false,"Le code de sécurité n'est pas valide.");
				}
			}
			else{
				$return = array(false,$_POST["input_email"]." est déjà inscrite à notre lettre d'information");
			}
		}
		else{
			//2.1. On regarde si l'adresse correspond à celle de l'utilisateur pour lui indiquer où modifier son abonnement
			if(est_connecte()&&$_SESSION["utilisateur"]["email"]==$_POST["input_email"]){
				$return = array(false,"Modifiez votre abonnement à notre lettre d'information depuis votre espace personnel.");
			}
			else{
				$return = array(false,$_POST["input_email"]." est déjà utlisée en tant que profil sur ensemble ici");
			}
		}
	}
	else{
		$return = array(false,"Veuillez saisir une adresse mail valide");
	}
}
else{
	$return = array(false,"Veuillez remplir tous les champs");
}
echo json_encode($return);
?>
