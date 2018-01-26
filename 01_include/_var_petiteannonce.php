<?php
$tablePrincipale = "petiteannonce";
$champ_dateDu = "date_fin";
$champ_titre = "titre";
$champs = array(
				array("champ"=>"petiteannonce.no","alias"=>"no"),
				array("champ"=>"petiteannonce.titre","alias"=>"titre"),
				array("champ"=>"petiteannonce.description","alias"=>"description"),
				array("champ"=>"petiteannonce.url_image","alias"=>"image"),
				array("champ"=>"petiteannonce.monetaire","alias"=>"monetaire"),
				array("champ"=>"petiteannonce.prix","alias"=>"prix"),
				array("champ"=>"petiteannonce.date_creation","alias"=>"date_creation"),
				array("champ"=>"petiteannonce.no_utilisateur_creation","alias"=>"no_utilisateur"),
				array("champ"=>"petiteannonce.date_fin","alias"=>"date_fin"),
				array("champ"=>"petiteannonce.no_petiteannonce_type","alias"=>"no_petiteannonce_type"),
				array("champ"=>"petiteannonce.no_ville","alias"=>"no_ville"),
				array("champ"=>"petiteannonce.etat","alias"=>"rayonmax"),
				array("champ"=>"petiteannonce.etat","alias"=>"etat"),
				array("champ"=>"petiteannonce.validation","alias"=>"validation"),
				
				array("champ"=>"petiteannonce_type.libelle","alias"=>"type"),
				
				//Champs communs des tables externes (Utilisateur & Ville)
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));

$champs_liste = $champs;
$champs_fiche = $champs;
$champs_fiche[] = array("champ"=>"petiteannonce.site","alias"=>"site");
$champs_fiche[] = array("champ"=>"petiteannonce.afficher_tel","alias"=>"afficher_tel");
$champs_fiche[] = array("champ"=>"petiteannonce.afficher_mob","alias"=>"afficher_mob");
$champs_fiche[] = array("champ"=>"petiteannonce.rayonmax","alias"=>"rayonmax");
$champs_fiche[] = array("champ"=>"petiteannonce.legende","alias"=>"legende");

/*$champs_fiche[] = array("champ"=>"contact.no","alias"=>"no_contact");
$champs_fiche[] = array("champ"=>"contact.nom","alias"=>"nom_contat");
$champs_fiche[] = array("champ"=>"contact.email","alias"=>"email");
$champs_fiche[] = array("champ"=>"IF(petiteannonce.afficher_tel=1,contact.telephone,'')","alias"=>"telephone");
$champs_fiche[] = array("champ"=>"IF(petiteannonce.afficher_mob=1,contact.mobile,'')","alias"=>"telephone2");*/

$champs_fiche[] = array("champ"=>"villes.code_postal","alias"=>"cp");

$champs_tri = array(	"date"=>"petiteannonce.date_creation",
						"date_creation"=>"petiteannonce.date_creation",
						"distance"=>"distance",
						"date_fin"=>"petiteannonce.date_fin",
						"titre"=>"LOWER(petiteannonce.titre)",
						"ville"=>"LOWER(villes.nom_ville_maj)");

$du = date("Y-m-d");
$tri_defaut = "date_creation";
//$ordre_defaut = "DESC";
$distance = -1;
$avec_illustration = false;

if(empty($limite)||$limite<=0)
	$limite = 20;
?>
