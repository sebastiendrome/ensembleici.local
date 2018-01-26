<?php
/*********************
Ce fichier contient les informations des champs
**********************/
//Les champs de chaque table communs à ceux des autres tables
$champs = array(
			array("champ"=>$tablePrincipale.".no","alias"=>"no"),
			array("champ"=>$tablePrincipale.".url_image","alias"=>"image"),
			array("champ"=>$tablePrincipale.".signature","alias"=>"signature"),
			array("champ"=>$tablePrincipale.".copyright","alias"=>"copyright"),
			array("champ"=>$tablePrincipale.".date_creation","alias"=>"date_creation"),
			array("champ"=>$tablePrincipale.".nb_aime","alias"=>"nb_aime"),
			array("champ"=>$tablePrincipale.".no_utilisateur_creation","alias"=>"no_utilisateur"),
			array("champ"=>$tablePrincipale.".etat","alias"=>"etat"),
			array("champ"=>$tablePrincipale.".validation","alias"=>"validation"));
			
//Editorial
array("champ"=>$tablePrincipale.".titre","alias"=>"titre"),
array("champ"=>$tablePrincipale.".sous_titre","alias"=>"sous_titre"),
array("champ"=>$tablePrincipale.".chapo","alias"=>"chapo"),
array("champ"=>$tablePrincipale.".description","alias"=>"description"),
array("champ"=>$tablePrincipale.".notes","alias"=>"notes"),

//evenement
array("champ"=>$tablePrincipale.".titre","alias"=>"titre"),
array("champ"=>$tablePrincipale.".sous_titre","alias"=>"sous_titre"),
array("champ"=>$tablePrincipale.".description","alias"=>"description"),
array("champ"=>"evenement.description_complementaire","alias"=>"description_complementaire"),
			
			
//Les champs communs à toutes les tables (Ville, distance & utilisateur)
$champs[] = array("champ"=>"utilisateur.pseudo","alias"=>"pseudo");
$champs[] = array("champ"=>"villes.nom_ville_maj","alias"=>"ville");
$champs[] = array("champ"=>"villes.id","alias"=>"no_ville");
if(!empty($LATITUDE)&&!empty($LONGITUDE))
	$champs[] = array("champ"=>"(acos(sin(".$LATITUDE.")*sin(radians(villes.latitude)) + cos(".$LATITUDE.")*cos(radians(villes.latitude))*cos(radians(villes.longitude)-".$LONGITUDE."))*".$RAYON_TERRE.")","alias"=>"distance");
if($conditions["admin"]){
	$champs[] = array("champ"=>"editorial.etat","alias"=>"etat");
	$champs[] = array("champ"=>"editorial.validation","alias"=>"validation");
	$champs[] = array("champ"=>"villes.id","alias"=>"no_ville");
}

//Les champs en fonction du type


?>
