<?php
$tablePrincipale = "structure";
$champ_dateDu = "date_creation";
$champ_titre = "nom";
$champs = array(
				array("champ"=>"structure.no","alias"=>"no"),
				array("champ"=>"structure.nom","alias"=>"titre"),
				array("champ"=>"structure.sous_titre","alias"=>"sous_titre"),
				array("champ"=>"structure.description","alias"=>"description"),
				array("champ"=>"structure.url_logo","alias"=>"image"),
				array("champ"=>"structure.no_statut","alias"=>"no_statut"),
				array("champ"=>"structure.date_creation","alias"=>"date_creation"),
				array("champ"=>"structure.no_utilisateur_creation","alias"=>"no_utilisateur"),
				array("champ"=>"structure.no_ville","alias"=>"no_ville"),
				array("champ"=>"structure.etat","alias"=>"etat"),
				array("champ"=>"structure.validation","alias"=>"validation"),
				array("champ"=>"structure.nb_aime","alias"=>"nb_aime"),
				
				array("champ"=>"statut.libelle","alias"=>"statut"),
				
				//Champs communs des tables externes (Utilisateur & Ville)
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));

$champs_liste = $champs;
$champs_fiche = $champs;
$champs_fiche[] = array("champ"=>"structure.copyright","alias"=>"copyright");
$champs_fiche[] = array("champ"=>"structure.legende","alias"=>"legende");
$champs_fiche[] = array("champ"=>"structure.site_internet","alias"=>"site");
$champs_fiche[] = array("champ"=>"structure.facebook","alias"=>"facebook");
$champs_fiche[] = array("champ"=>"structure.nomadresse","alias"=>"nom_adresse");
$champs_fiche[] = array("champ"=>"structure.adresse","alias"=>"adresse");
$champs_fiche[] = array("champ"=>"structure.adresse_complementaire","alias"=>"adresse_complementaire");
$champs_fiche[] = array("champ"=>"structure.telephone","alias"=>"telephone");
$champs_fiche[] = array("champ"=>"structure.telephone2","alias"=>"telephone2");
$champs_fiche[] = array("champ"=>"structure.fax","alias"=>"fax");
$champs_fiche[] = array("champ"=>"structure.email","alias"=>"email");
$champs_fiche[] = array("champ"=>"villes.code_postal","alias"=>"cp");

$champs_tri = array(	"date_creation"=>"structure.date_creation",
						"date"=>"structure.date_creation",
						"distance"=>"distance",
						"popularite"=>"structure.nb_aime",
						"reputation"=>"structure.nb_aime",
						"titre"=>"LOWER(structure.nom)",
						"statut"=>"LOWER(statut.libelle)",
						"ville"=>"LOWER(villes.nom_ville_maj)");

$tri_defaut = "date_creation";
$distance = 0;
$avec_illustration = false;

if(empty($limite)||$limite<=0)
	$limite = 10;
?>
