<?php
$tablePrincipale = "evenement";
$champ_dateDu = "date_fin";
$champ_titre = "titre";
$champs_liste = array(
				array("champ"=>"evenement.no","alias"=>"no"),
				array("champ"=>"evenement.titre","alias"=>"titre"),
				array("champ"=>"evenement.sous_titre","alias"=>"sous_titre"),
				array("champ"=>"evenement.description","alias"=>"description"),
				array("champ"=>"evenement.url_image","alias"=>"image"),
				array("champ"=>"evenement.date_debut","alias"=>"date_debut"),
				array("champ"=>"evenement.date_fin","alias"=>"date_fin"),
				array("champ"=>"evenement.date_creation","alias"=>"date_creation"),
				array("champ"=>"evenement.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"evenement.nomadresse","alias"=>"lieu"),
				array("champ"=>"evenement.adresse","alias"=>"adresse"),
				array("champ"=>"evenement.telephone","alias"=>"telephone"),
				array("champ"=>"evenement.telephone2","alias"=>"telephone2"),
				array("champ"=>"evenement.site","alias"=>"site"),
				array("champ"=>"evenement.email","alias"=>"email"),
				array("champ"=>"evenement.date_creation","alias"=>"date_creation"),
				
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"villes.id","alias"=>"no_ville"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"),
				array("champ"=>"IF((evenement.date_fin<=DATE_ADD(evenement.date_debut, INTERVAL 31 DAY)),0,1)","alias"=>"evenement_long"));
/*if(!empty($LATITUDE)&&!empty($LONGITUDE))
	$champs_liste[] = array("champ"=>"(acos(sin(".$LATITUDE.")*sin(radians(villes.latitude)) + cos(".$LATITUDE.")*cos(radians(villes.latitude))*cos(radians(villes.longitude)-".$LONGITUDE."))*".$RAYON_TERRE.")","alias"=>"distance");
*/
if($conditions["admin"]){
	$champs_liste[] = array("champ"=>"evenement.etat","alias"=>"etat");
	$champs_liste[] = array("champ"=>"evenement.validation","alias"=>"validation");
}
				
$champs_fiche = array(
				array("champ"=>"evenement.no","alias"=>"no"),
				array("champ"=>"evenement.titre","alias"=>"titre"),
				array("champ"=>"evenement.site","alias"=>"site"),
				array("champ"=>"evenement.sous_titre","alias"=>"sous_titre"),
				array("champ"=>"evenement.description","alias"=>"description"),
				array("champ"=>"evenement.description_complementaire","alias"=>"description_complementaire"),
				array("champ"=>"evenement.url_image","alias"=>"image"),
				array("champ"=>"evenement.copyright","alias"=>"copyright"),
				array("champ"=>"evenement.legende","alias"=>"legende"),
				array("champ"=>"evenement.date_debut","alias"=>"date_debut"),
				array("champ"=>"evenement.date_fin","alias"=>"date_fin"),
				array("champ"=>"evenement.heure_debut","alias"=>"heure_debut"),
				array("champ"=>"evenement.heure_fin","alias"=>"heure_fin"),
				array("champ"=>"evenement.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"evenement.no_ville","alias"=>"no_ville"),
				array("champ"=>"evenement.nomadresse","alias"=>"nom_adresse"),
				array("champ"=>"evenement.adresse","alias"=>"adresse"),
				array("champ"=>"evenement.email","alias"=>"email"),
				array("champ"=>"evenement.date_creation","alias"=>"date_creation"),
				array("champ"=>"evenement.telephone","alias"=>"telephone"),
				array("champ"=>"evenement.telephone2","alias"=>"telephone2"),
				array("champ"=>"evenement.etat","alias"=>"etat"),
				array("champ"=>"evenement.validation","alias"=>"validation"),
				array("champ"=>"evenement.no_utilisateur_creation","alias"=>"no_utilisateur"),
				
				array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"genre.no","alias"=>"no_genre"),
				
				array("champ"=>"utilisateur.no","alias"=>"no_utilisateur"),
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"utilisateur.email","alias"=>"email_utilisateur"),
				
				array("champ"=>"villes.code_postal","alias"=>"cp"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));

if(!empty($LATITUDE)&&!empty($LONGITUDE))
	$champs_fiche[] = array("champ"=>"(acos(sin(".$LATITUDE.")*sin(radians(villes.latitude)) + cos(".$LATITUDE.")*cos(radians(villes.latitude))*cos(radians(villes.longitude)-".$LONGITUDE."))*".$RAYON_TERRE.")","alias"=>"distance");

$champs_tri = array(	"date"=>"evenement.date_debut, evenement.date_fin",
						"distance"=>"distance",
						"popularite"=>"evenement.nb_aime",
						"reputation"=>"evenement.nb_aime",
						"date_creation"=>"evenement.date_creation",
						"titre"=>"LOWER(evenement.titre)",
						"ville"=>"LOWER(villes.nom_ville_maj)");
						//"titre"=>"REPLACE(LOWER(evenement.titre),' ','')",
						//"ville"=>"REPLACE(LOWER(villes.nom_ville_maj),' ','')");

/*if(empty($conditions["du"]))
	$conditions["du"] = */
$du = date("Y-m-d");
$tri_defaut = "date"; //Tri par defaut
//$ordre_defaut = "DESC";
$distance = 0;
$avec_illustration = false;
$courte_duree_seulement = true;
if(empty($conditions["courte_duree_seulement"])) //Agenda
	$conditions["courte_duree_seulement"] = false;

if(empty($limite)||$limite<=0)
	$limite = 10;
?>
