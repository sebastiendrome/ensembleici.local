<?php
$tablePrincipale = "structure";
if(!$ordre)
	$ordre = "ORDER BY structure.date_creation DESC";
else
	$ordre = "ORDER BY ".$ordre;

$table = "petiteannonce";

if($extraction=="liste"){
	$champs = array(
				array("champ"=>"structure.no","alias"=>"no"),
				array("champ"=>"structure.nom","alias"=>"titre"),
				array("champ"=>"structure.description","alias"=>"description"),
				array("champ"=>"structure.url_logo","alias"=>"image"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));		
	$from = "FROM structure";
	$join = "JOIN villes ON structure.no_ville=villes.id";
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
	}*/
	if(!empty($conditions["ville_seulement"])&&$conditions["ville_seulement"]){ //On n'élargie pas la recherche à la BDD, mais seulement à la ville
		$les_conditions .= (($les_conditions!="")?" AND ":"")."structure.no_ville=:id_ville";
		$param["id_ville"] = $ID_VILLE;
	}
	$where = "WHERE structure.etat=1 AND structure.validation>=1 AND ".$les_conditions;
$requete .= " ".$where;

if(!empty($limit)&&$limit>0)
	$limit = "LIMIT ".$limit;
$requete .= " ".$ordre." ".$limit;
?>
