<?php
$tablePrincipale = "forum";
if(!$ordre)
	$ordre = "ORDER BY date_mod DESC";
else
	$ordre = "ORDER BY ".$ordre;

$table = "forum";

if($extraction=="liste"){
	$champs = array(
				array("champ"=>"forum.no","alias"=>"no"),
				array("champ"=>"forum.titre","alias"=>"titre"),
				array("champ"=>"IFNULL(MAX(messageForum.date_modification),forum.date_modification)","alias"=>"date_mod"),
				array("champ"=>"forum.url_image","alias"=>"image"),
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));		
	$from = "FROM forum";
	$join = "LEFT JOIN villes ON forum.no_ville=villes.id";
	$join .= " JOIN utilisateur ON utilisateur.no=forum.no_utilisateur_creation";
	$join .= " JOIN messageForum ON messageForum.no_forum=forum.no";
	
	$group_by = "GROUP BY forum.no";
}

//On construit maintenant la requête avec les champs, from et join.
$requete = "SELECT ";
	$les_champs = "";
	for($r=0;$r<count($champs);$r++){
		$les_champs .= (($les_champs!="")?", ":"").$champs[$r]["champ"]." AS ".$champs[$r]["alias"];
	}
$requete .= $les_champs;
$requete .= " ".$from." ".$join;

//On crait maintenant la requête avec les conditions
	/*$les_conditions = "";
	if(!empty($conditions["du"])){ //Date de départ
		$les_conditions = (($les_conditions!="")?", ":"")."editorial.date";
	}
	for($c=0;$c<count($conditions);$c++){
		
	}*/
	$les_conditions = "";
	$param = array();
	/*if(!empty($conditions["du"])){ //Date de limite inférieure
		$les_conditions .= (($les_conditions!="")?" AND ":"")."petiteannonce.date_fin>=:du";
		$param["du"] = $conditions["du"];
	}
	if(!empty($conditions["au"])){ //Date de limite supérieure
		$les_conditions .= (($les_conditions!="")?" AND ":"")."petiteannonce.date_fin<=:au";
		$param["au"] = $conditions["au"];
	}
	if(!empty($conditions["ville_seulement"])&&$conditions["ville_seulement"]){ //On n'élargie pas la recherche à la BDD, mais seulement à la ville
		$les_conditions .= (($les_conditions!="")?" AND ":"")."structure.no_ville=:id_ville";
		$param["id_ville"] = $ID_VILLE;
	}*/
	$where = "WHERE forum.etat=1 AND forum.validation>=1".$les_conditions;
$requete .= " ".$where." ".$group_by;

if(!empty($limit)&&$limit>0)
	$limit = "LIMIT ".$limit;
$requete .= " ".$ordre." ".$limit;
?>
