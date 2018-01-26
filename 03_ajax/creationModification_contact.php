<?php
/*
Les paramètres :
	input_contact_nom
	no_contact
	input_contact_[i]
	select_contact_[i]
	afficher_contact_[i]
*/
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. On execute le script
if(est_connecte()){ //Si l'utilisateur est connecté
	if(!empty($_POST["input_contact_nom"])){ //Si le contact est nommé
		if(empty($_POST["no_contact"])){ //C'est un nouveau contact
			//1. On insère dans la base de donnée le nouveau contact
			$requete_insert = "INSERT INTO contact(nom,no_utilisateur_creation) VALUES(:nom,:no)";
			$NO_CONTACT = execute_requete($requete_insert,array(":nom"=>urldecode($_POST["input_contact_nom"]),":no"=>$_SESSION["utilisateur"]["no"]));
		}
		else{
			$NO_CONTACT = $_POST["no_contact"];
			$requete_update = "UPDATE contact SET contact.nom=:nom WHERE contact.no=:no";
			execute_requete($requete_update,array(":nom"=>$_POST["input_contact_nom"],":no"=>$NO_CONTACT));
		}
		//On regarde maintenant s'il s'agit de l'utilisateur
		if($_POST["est_moi"]==1){
			$requete_utilisateur = "SELECT contact.no FROM contact JOIN utilisateur ON utilisateur.no_contact=contact.no WHERE utilisateur.no=:no";
			$tab_utilisateur = execute_requete($requete_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
			if(empty($tab_utilisateur)||empty($tab_utilisateur[0]["no"])){	//L'utilisateur n'est pas lié au contact ...
				$update_utilisateur = "UPDATE utilisateur SET no_contact=:no_contact WHERE no=:no";
				execute_requete($update_utilisateur,array(":no_contact"=>$NO_CONTACT,":no"=>$_SESSION["utilisateur"]["no"]));
			}
		}
		//On regarde maintenant s'il y a un type
		/*if(!empty($_POST["type"])&&!empty($_POST["no"])){
			$requete_contact = "SELECT unContact.no FROM unContact JOIN ".$_POST["type"]."_unContact ON unContact.no=".$_POST["type"]."_unContact.no_contact WHERE ".$_POST["type"]."_unContact.no_contact=:no_contact AND ".$_POST["type"]."_unContact.no_".$_POST["type"]."=:no";
			$tab_contact = execute_requete($requete_contact,array(":no"=>$NO));
			if(empty($tab_utilisateur)||empty($tab_utilisateur[0]["no"])){	//L'item n'est pas lié au contact ...
				if(empty($_POST["no_role"]))
					$insert_item = "INSERT INTO ".$_POST["type"]."_unContact(no_".$_POST["type"].",no_contact) VALUES(:no,:no_contact)";
				else
					$insert_item = "INSERT INTO ".$_POST["type"]."_unContact(no_".$_POST["type"].",no_contact,no_role) VALUES(:no,:no_contact)";
				execute_requete($update_utilisateur,array(":no_contact"=>$NO_CONTACT,":no"=>$_SESSION["utilisateur"]["no"]));
			}
		}*/
		
		$reponse = array(true,array("nom"=>$_POST["input_contact_nom"],"no"=>$NO_CONTACT));
		//Maintenant on vide tout pour no_contact
		$requete_delete = "DELETE FROM contact_contactType WHERE no_contact=:no";
		execute_requete($requete_delete,array(":no"=>$NO_CONTACT));
		//On met les nouvelles valeurs
		$regex_telephone = "#^[0-9]{10,11}#";
		$regex_formate_telephone = "#[^0-9]#";
		$i=0;
		while(isset($_POST["input_contact_".$i])&&!empty($_POST["input_contact_".$i])&&$reponse[0]){
			if($_POST["select_contact_".$i]==2&&!filter_var($_POST["input_contact_".$i],FILTER_VALIDATE_EMAIL)) //Adresse mail
				$reponse = array(false,"L'adresse email n'est pas valide");
			else if($_POST["select_contact_".$i]==1&&!filter_var($_POST["input_contact_".$i],FILTER_VALIDATE_URL)){ //Téléphone
				$_POST["input_contact_".$i] = preg_replace($regex_formate_telephone,'',$_POST["input_contact_".$i]); //On garde que les chiffres
				if(!preg_match($regex_telephone,$_POST["input_contact_".$i]))
					$reponse = array(false,"Le numéro de téléphone n'est pas valide");
			}
			else if($_POST["select_contact_".$i]==3||$_POST["select_contact_".$i]==4){
				if(substr($_POST["input_contact_".$i],0,7)!="http://"||substr($_POST["input_contact_".$i],0,8)!="https://")
					$_POST["input_contact_".$i] = "http://".$_POST["input_contact_".$i];
				if(!filter_var($_POST["input_contact_".$i],FILTER_VALIDATE_URL)) //URL ou réseau social
					$reponse = array(false,"L'adresse internet n'est pas valide");
			}
			$requete_insert = "INSERT INTO contact_contactType(no_contact,no_contactType,valeur,public) VALUES(:no_contact,:no_contactType,:valeur,:public)";
			execute_requete($requete_insert,array(":no_contact"=>$NO_CONTACT,":no_contactType"=>$_POST["select_contact_".$i],":valeur"=>$_POST["input_contact_".$i],":public"=>$_POST["afficher_contact_".$i]));
			$i++;
		}
	}
	else
		$reponse = array(false,"Veuillez donner un nom à ce contact.");
}
else
	$reponse = array(false,"[CONNEXION]");
echo json_encode($reponse);
?>
