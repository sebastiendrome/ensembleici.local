<?php
/**
Paramétres d'entrée :
	- no -> le numéro de la fiche
	- type(agenda|evenement|structure|repertoire|forum[petite-annonce|petiteannonce|forum|editorial) -> par sécurité on accepte tous les types possibles
	- action(favori|aime) -> Le type d'action que l'utilisateur effectue
**/
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

if($_POST["type"]=="petite-annonce"||$_POST["type"]=="petiteannonce")
	$table = "petiteannonce";
else if($_POST["type"]=="evenement"||$_POST["type"]=="agenda")
	$table = "evenement";
else if($table=="structure"||$table=="repertoire")
	$table = "structure";
else if($_POST["type"]=="forum")
	$table = "forum";
else if($_POST["type"]=="editorial")
	$table = "editorial";
else
	$table = "";
if(!empty($table)&&!empty($_POST["no"])){
	if($_POST["action"]=="favori"){ //S'il s'agit d'un favoris
		if(est_connecte()){ //On vérifie que l'utilisateur est connecté
			//Si c'est le cas, on regarde si l'item est déjà present dans la table des favoris pour cet utilisateur
			$requete_favoris = "SELECT * FROM ".$table."_favoris WHERE ".$table."_favoris.no_utilisateur=:no_utilisateur AND ".$table."_favoris.no_".$table."=:no";
			$tab_favori = execute_requete($requete_favoris,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
			if(!empty($tab_favori)){ //On supprime alors l'entrée
				$requete_supprime = "DELETE FROM ".$table."_favoris WHERE ".$table."_favoris.no_utilisateur=:no_utilisateur AND ".$table."_favoris.no_".$table."=:no";
				execute_requete($requete_supprime,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
				$reponse = array(true,"");
			}
			else{ //Sinon On insère l'entrée
				$requete_insert = "INSERT INTO ".$table."_favoris(no_".$table.",no_utilisateur) VALUES(:no,:no_utilisateur)";
				execute_requete($requete_insert,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
				$reponse = array(true,"actif");
			}
		}
		else{ //Sinon on envoi le code erreur : connexion
			$reponse = array(false,"[CONNEXION]");
		}
	}
	else if($_POST["action"]=="coupdecoeur"){ //Si c'est un coup de coeur
		//Si l'utilisateur est connecté, on récupère son ip dans la table utilisateur
		if(est_connecte()){
			$requete_ip = "SELECT utilisateur_connexions.IP as ip FROM utilisateur_connexions WHERE utilisateur_connexions.email=:email ORDER BY utilisateur_connexions.quand DESC";
			$tab_ip = execute_requete($requete_ip,array(":email"=>$_SESSION["utilisateur"]["email"]));
			if(count($tab_ip)>0&&!empty($tab_ip[0]["ip"]))
				$IP = $tab_ip[0]["ip"];
			else
				$IP = $_SERVER["REMOTE_ADDR"];
			$NO_UTILISATEUR = $_SESSION["utilisateur"]["no"];
		}
		else{
			$IP = $_SERVER["REMOTE_ADDR"];
			$NO_UTILISATEUR = 0;
		}
		//On regarde maintenant dans le table des coupedecoeur si une entrée correspond déjà pour cet ip, item, et utilisateur
		$requete_coupdecoeur = "SELECT ".$table."_coupdecoeur.no FROM ".$table."_coupdecoeur WHERE ".$table."_coupdecoeur.no_utilisateur=:no AND ".$table."_coupdecoeur.IP=:ip";
		$tab_coupdecoeur = execute_requete($requete_coupdecoeur,array(":ip"=>$IP,":no"=>$NO_UTILISATEUR));
		if(!empty($tab_coupdecoeur)&&!empty($tab_coupdecoeur[0]["no"])){ //On supprime alors l'entrée
			$requete_supprime = "DELETE FROM ".$table."_coupdecoeur WHERE ".$table."_coupdecoeur.no=:no";
			execute_requete($requete_supprime,array(":no"=>$tab_coupdecoeur[0]["no"]));
			$reponse = array(true,"");
		}
		else{ //Sinon on ajoute l'entrée pour cet item, utilisateur et ip
			$requete_insert = "INSERT INTO ".$table."_coupdecoeur(no_".$table.",IP,no_utilisateur) VALUES(:no,:ip,:no_utilisateur)";
			execute_requete($requete_insert,array(":no"=>$_POST["no"],":ip"=>$IP,":no_utilisateur"=>$NO_UTILISATEUR));
			$reponse = array(true,"actif");
		}
	}
	else{
		if(est_connecte()){ //On vérifie que l'utilisateur est connecté
			//Si c'est le cas, on regarde si l'item est déjà present dans la table des notifications pour cet utilisateur
			$requete_notification = "SELECT ".$table."_notification.etat FROM ".$table."_notification WHERE ".$table."_notification.no_utilisateur=:no_utilisateur AND ".$table."_notification.no_".$table."=:no";
			$tab_notification = execute_requete($requete_notification,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
			if(!empty($tab_notification)){ //On update alors l'entrée
				if($tab_notification[0]["etat"]==1){
					$etat = 0;
					$chaine_retour = "";
				}
				else{
					$etat = 1;
					$chaine_retour = "actif";
				}
				$requete_update = "UPDATE ".$table."_notification SET etat=:etat WHERE ".$table."_notification.no_utilisateur=:no_utilisateur AND ".$table."_notification.no_".$table."=:no";
				execute_requete($requete_update,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"],":etat"=>$etat));
				$reponse = array(true,$chaine_retour);
			}
			else{ //Sinon On insère l'entrée
				$requete_insert = "INSERT INTO ".$table."_notification(no_".$table.",no_utilisateur,etat,date_creation) VALUES(:no,:no_utilisateur,1,:date)";
				execute_requete($requete_insert,array(":no"=>$_POST["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"],":date"=>date("Y-m-d H:i:s")));
				$reponse = array(true,"actif");
			}
		}
		else{ //Sinon on envoi le code erreur : connexion
			$reponse = array(false,"[CONNEXION]");
		}
	}
}
else{
	$reponse = array(false,"Une erreur est survenue");
}
echo json_encode($reponse);
?>
