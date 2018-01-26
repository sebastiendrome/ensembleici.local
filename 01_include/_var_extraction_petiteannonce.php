<?php
if(!$ordre)
	$ordre = "ORDER BY petiteannonce.date_creation DESC";
else
	$ordre = "ORDER BY ".$ordre;

$table = "petiteannonce";

if($extraction=="liste"){
	$champs = array(
				array("champ"=>"petiteannonce.no","alias"=>"no"),
				array("champ"=>"petiteannonce.titre","alias"=>"titre"),
				array("champ"=>"petiteannonce.monetaire","alias"=>"monetaire"),
				array("champ"=>"petiteannonce.prix","alias"=>"prix"),
				array("champ"=>"petiteannonce.url_image","alias"=>"image"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));		
	$from = "FROM petiteannonce";
	$join = "JOIN villes ON petiteannonce.no_ville=villes.id";
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
		$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.no_ville=:id_ville";
		$param["id_ville"] = $ID_VILLE;
	}*/
	$where = "WHERE petiteannonce.etat=1 AND petiteannonce.validation>=1".$les_conditions;
$requete .= " ".$where;

if(!empty($limit)&&$limit>0)
	$limit = "LIMIT ".$limit;
$requete .= " ".$ordre." ".$limit;
?>
