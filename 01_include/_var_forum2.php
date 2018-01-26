<?php
$tablePrincipale = "forum";
$champ_dateDu = "date_creation";
$champs = array(
				array("champ"=>"forum.no","alias"=>"no"),
				array("champ"=>"IF(forum_type.no<>1,forum.titre,'Forum citoyen')","alias"=>"titre"),
				//array("champ"=>"IFNULL(MAX(messageForum.date_modification),forum.date_modification)","alias"=>"date_mod"),
				array("champ"=>"IFNULL((SELECT MAX(messageForum.date_modification) FROM messageForum WHERE messageForum.no_forum=forum.no ),forum.date_modification)","alias"=>"date_mod"),
				array("champ"=>"forum.url_image","alias"=>"image"),
				array("champ"=>"forum.etat","alias"=>"etat"),
				array("champ"=>"forum.validation","alias"=>"validation"),
				array("champ"=>"forum.no_ville","alias"=>"no_ville"),
				array("champ"=>"forum.date_creation","alias"=>"date_creation"),
				array("champ"=>"forum.no_utilisateur_creation","alias"=>"no_utilisateur"),
				
				array("champ"=>"forum_type.libelle","alias"=>"type"),
				array("champ"=>"IF(forum_type.no=1,0,1)","alias"=>"est_citoyen"),
				
				//Champs communs des tables externes (Utilisateur & Ville)
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));
			/*	
$champs_fiche = array(
				array("champ"=>"forum.no","alias"=>"no"),
				array("champ"=>"forum.titre","alias"=>"titre"),
				array("champ"=>"IFNULL(MAX(messageForum.date_modification),forum.date_modification)","alias"=>"date_mod"),
				array("champ"=>"forum.url_image","alias"=>"image"),
				array("champ"=>"forum.validation","alias"=>"validation"),
				array("champ"=>"forum.no_ville","alias"=>"no_ville"),
				array("champ"=>"forum.date_creation","alias"=>"date_creation"),
				array("champ"=>"forum.no_utilisateur_creation","alias"=>"no_utilisateur"),
				
				array("champ"=>"forum_type.libelle","alias"=>"type"),
				array("champ"=>"IF(forum_type.no=1,0,1)","alias"=>"est_citoyen"),
				
				//Champs communs des tables externes (Utilisateur & Ville)
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));*/
$champs_liste = $champs;
$champs_fiche = $champs;

$champs_tri = array(	"date_creation"=>"forum.date_creation",
						"distance"=>"",
						"popularite"=>"forum.nb_aime");

$du = "0000-00-00";
$tri_defaut = "date_creation";
$distance = -1;
$avec_illustration = false;

if(empty($limite)||$limite<=0)
	$limite = 5;
?>
