<?php
$tablePrincipale = "forum";
$champ_dateDu = "date_creation";
$champ_titre = "titre";
//array("champ"=>"IFNULL(MAX(messageForum.date_modification),forum.date_modification)","alias"=>"date_mod"),
$champs = array(
				array("champ"=>"forum.no","alias"=>"no"),
				array("champ"=>"IF(forum.no_forum_type<>1,forum.titre,'Forum citoyen')","alias"=>"titre"),
				array("champ"=>"IF(forum.no_forum_type<>1,forum.sous_titre,villes.nom_ville_maj)","alias"=>"sous_titre"),
				array("champ"=>"forum.contenu","alias"=>"description"),
				array("champ"=>"forum.url_image","alias"=>"image"),
				//array("champ"=>"IFNULL((SELECT MAX(messageForum.date_modification) FROM messageForum WHERE messageForum.no_forum=forum.no ),forum.date_creation)","alias"=>"date_mod"),
				array("champ"=>"IFNULL((SELECT MAX(message.date_modification) FROM message WHERE message.no_forum=forum.no ),forum.date_creation)","alias"=>"date_mod"),
				array("champ"=>"forum.no_ville","alias"=>"no_ville"),
				array("champ"=>"forum.no_forum_type","alias"=>"no_forum_type"),
				array("champ"=>"forum.date_creation","alias"=>"date_creation"),
				array("champ"=>"forum.no_utilisateur_creation","alias"=>"no_utilisateur"),
				array("champ"=>"forum.signature","alias"=>"signature"),
				array("champ"=>"forum.afficher_signature","alias"=>"afficher_signature"),
				array("champ"=>"forum.etat","alias"=>"etat"),
				array("champ"=>"forum.validation","alias"=>"validation"),
				
				array("champ"=>"forum_type.libelle","alias"=>"type"),
				array("champ"=>"IF(forum_type.no=1,1,0)","alias"=>"est_citoyen"),
				
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
$champs_fiche[] = array("champ"=>"forum.copyright","alias"=>"copyright");
$champs_fiche[] = array("champ"=>"forum.legende","alias"=>"legende");
$champs_fiche[] = array("champ"=>"villes.code_postal","alias"=>"cp");

$champs_tri = array(	"date_creation"=>"forum.date_creation",
						"date"=>"date_mod",
						"distance"=>"distance",
						"reputation"=>"forum.nb_aime",
						"popularite"=>"forum.nb_aime");

//$du = "0000-00-00";
$tri_defaut = "date";
$distance = -1;
$avec_illustration = false;

if(empty($limite)||$limite<=0)
	$limite = 5;
?>
