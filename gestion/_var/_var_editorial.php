<?php
/*********************
Ce fichier contient toutes les variables concernant les pages éditoriales.
	-Table concernée
	-Liste des champs
	-
**********************/
//1. Table
	$tablePrincipale = "editorial";
$champ_dateDu = "date_creation";
//2. Les champs
	//2.1. Ceux toujours appellés
	$champs = array(
				array("champ"=>$tablePrincipale.".no","alias"=>"no"),
				array("champ"=>$tablePrincipale.".titre","alias"=>"titre"),
				array("champ"=>$tablePrincipale.".sous_titre","alias"=>"sous_titre"),
				array("champ"=>$tablePrincipale.".chapo","alias"=>"chapo"),
				array("champ"=>$tablePrincipale.".description","alias"=>"description"),
				array("champ"=>$tablePrincipale.".notes","alias"=>"notes"),
				array("champ"=>$tablePrincipale.".description","alias"=>"description"),
				array("champ"=>$tablePrincipale.".url_image","alias"=>"image"),
				array("champ"=>$tablePrincipale.".afficher_signature","alias"=>"afficher_signature"),
				array("champ"=>$tablePrincipale.".signature","alias"=>"signature"),
				array("champ"=>$tablePrincipale.".copyright","alias"=>"copyright"),
				array("champ"=>$tablePrincipale.".legende","alias"=>"legende"),
				array("champ"=>$tablePrincipale.".date_creation","alias"=>"date_creation"),
				array("champ"=>$tablePrincipale.".nb_aime","alias"=>"nb_aime"),
				array("champ"=>$tablePrincipale.".no_utilisateur_creation","alias"=>"no_utilisateur"),
				array("champ"=>$tablePrincipale.".etat","alias"=>"etat"),
				array("champ"=>$tablePrincipale.".validation","alias"=>"validation"),
				
				//Champs communs des tables externes (Utilisateur & Ville)
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));

if(!empty($LATITUDE)&&!empty($LONGITUDE))
	$champs[] = array("champ"=>"(acos(sin(".$LATITUDE.")*sin(radians(villes.latitude)) + cos(".$LATITUDE.")*cos(radians(villes.latitude))*cos(radians(villes.longitude)-".$LONGITUDE."))*".$RAYON_TERRE.")","alias"=>"distance");

$champs_tri = array(	"date_creation"=>"editorial.date_creation",
						"distance"=>"distance",
						"popularite"=>"editorial.nb_aime",
						"titre"=>"LOWER(editorial.titre)",
						"pseudo"=>"LOWER(utilisateur.pseudo)",
						"ville"=>"LOWER(villes.nom_ville_maj)");

$du = "2014-09-11";
$tri_defaut = "date_creation";
$ordre_defaut = "DESC";
$distance = -1;
$avec_illustration = false;

if(empty($limite)||$limite<=0)
	$limite = 5;

$requete_fichiers = "SELECT fichier_galerie.no_fichier_type, fichier_type.icone FROM fichier_type JOIN fichier_galerie ON fichier_galerie.no_fichier_type=fichier_type.no JOIN editorial_galerie ON editorial_galerie.no_galerie=fichier_galerie.no_galerie WHERE editorial_galerie.no_editorial=:no GROUP BY fichier_type.no";
$res_fichiers = preparer_requete($requete_fichiers);
?>
