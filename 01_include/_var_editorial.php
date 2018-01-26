<?php
$tablePrincipale = "editorial";
$champ_dateDu = "date_creation";
$champ_titre = "titre";
$champs_liste = array(
				array("champ"=>"editorial.no","alias"=>"no"),
				array("champ"=>"editorial.titre","alias"=>"titre"),
				array("champ"=>"editorial.sous_titre","alias"=>"sous_titre"),
				array("champ"=>"editorial.chapo","alias"=>"chapo"),
				array("champ"=>"editorial.description","alias"=>"description"),
				array("champ"=>"editorial.notes","alias"=>"notes"),
				array("champ"=>"editorial.description","alias"=>"description"),
				array("champ"=>"editorial.url_image","alias"=>"image"),
				array("champ"=>"editorial.signature","alias"=>"signature"),
				array("champ"=>"editorial.afficher_signature","alias"=>"afficher_signature"),
				array("champ"=>"editorial.copyright","alias"=>"copyright"),
				array("champ"=>"editorial.date_creation","alias"=>"date_creation"),
				array("champ"=>"editorial.date_modification","alias"=>"date_modification"),
				array("champ"=>"editorial.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"editorial.no_utilisateur_creation","alias"=>"no_utilisateur"),
				array("champ"=>"editorial.etat","alias"=>"etat"),
				array("champ"=>"editorial.validation","alias"=>"validation"),
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				//array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));
				/*	
if(!empty($LATITUDE)&&!empty($LONGITUDE))
	$champs_liste[] = array("champ"=>"(acos(sin(".$LATITUDE.")*sin(radians(villes.latitude)) + cos(".$LATITUDE.")*cos(radians(villes.latitude))*cos(radians(villes.longitude)-".$LONGITUDE."))*".$RAYON_TERRE.")","alias"=>"distance");
*/
if($conditions["admin"]){
	$champs_liste[] = array("champ"=>"editorial.etat","alias"=>"etat");
	$champs_liste[] = array("champ"=>"editorial.validation","alias"=>"validation");
	$champs_liste[] = array("champ"=>"villes.id","alias"=>"no_ville");
}
	
$champs_fiche = array(
				array("champ"=>"editorial.no","alias"=>"no"),
				array("champ"=>"editorial.titre","alias"=>"titre"),
				array("champ"=>"editorial.sous_titre","alias"=>"sous_titre"),
				array("champ"=>"editorial.chapo","alias"=>"chapo"),
				array("champ"=>"editorial.description","alias"=>"description"),
				array("champ"=>"editorial.notes","alias"=>"notes"),
				array("champ"=>"editorial.url_image","alias"=>"image"),
				array("champ"=>"editorial.afficher_signature","alias"=>"afficher_signature"),
				array("champ"=>"editorial.signature","alias"=>"signature"),
				array("champ"=>"editorial.copyright","alias"=>"copyright"),
				array("champ"=>"editorial.legende","alias"=>"legende"),
				array("champ"=>"editorial.date_creation","alias"=>"date_creation"),
				array("champ"=>"editorial.date_modification","alias"=>"date_modification"),
				array("champ"=>"editorial.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"editorial.no_utilisateur_creation","alias"=>"no_utilisateur"),
				array("champ"=>"editorial.etat","alias"=>"etat"),
				array("champ"=>"editorial.validation","alias"=>"validation"),
				
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				
				//array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"villes.code_postal","alias"=>"cp"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"villes.id","alias"=>"no_ville"));

$champs_tri = array(	"date_creation"=>"editorial.date_creation",
						"date"=>"editorial.date_modification",
						"distance"=>"distance",
						"popularite"=>"editorial.nb_aime",
						"reputation"=>"editorial.nb_aime",
						"titre"=>"LOWER(editorial.titre)",
						"pseudo"=>"LOWER(utilisateur.pseudo)",
						"ville"=>"LOWER(villes.nom_ville_maj)");

//$du = "2014-09-11";
$tri_defaut = "date";
$ordre_defaut = "DESC";
$distance = -1;
$avec_illustration = false;

if(empty($limite)||$limite<=0)
	$limite = 5;

$requete_fichiers = "SELECT fichier_galerie.no_fichier_type, fichier_type.icone FROM fichier_type JOIN fichier_galerie ON fichier_galerie.no_fichier_type=fichier_type.no JOIN editorial_galerie ON editorial_galerie.no_galerie=fichier_galerie.no_galerie WHERE editorial_galerie.no_editorial=:no GROUP BY fichier_type.no";
$res_fichiers = preparer_requete($requete_fichiers);
?>
