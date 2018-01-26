<?php
$table = "evenement";
$date_du = "date_fin";
$date_au = "date_debut";

if($extraction=="liste"){
	$champs = array(
				array("champ"=>"evenement.no","alias"=>"no"),
				array("champ"=>"evenement.titre","alias"=>"titre"),
				array("champ"=>"evenement.sous_titre","alias"=>"sous_titre"),
				array("champ"=>"evenement.description","alias"=>"description"),
				array("champ"=>"evenement.url_image","alias"=>"image"),
				array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"evenement.date_debut","alias"=>"date_debut"),
				array("champ"=>"evenement.date_fin","alias"=>"date_fin"),
				array("champ"=>"evenement.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"utilisateur.pseudo","alias"=>"auteur"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));		
	$from = "FROM evenement";
	$join = "JOIN utilisateur ON utilisateur.no=evenement.no_utilisateur_creation";
	$join .= " JOIN genre ON genre.no=evenement.no_genre";
	$join .= " JOIN villes ON villes.id=evenement.no_ville";
	
	if(!$ordre)
		$ordre = "ORDER BY evenement.date_debut, evenement.date_fin";
	else
		$ordre = "ORDER BY ".$ordre;
	if(!empty($limit)&&$limit>0)
		$limit = "LIMIT ".$limit;
		
	//On règle les filtres par défaut
	if(empty($conditions["du"]))
		$conditions["du"] = date("Y-m-d");
	if(empty($conditions["courte_duree_seulement"]))
		$conditions["courte_duree_seulement"] = true;
	if(empty($conditions["ville_seulement"]))
		$conditions["ville_seulement"] = true;
	if(empty($conditions["etat"]))
		$conditions["etat"] = 1;
	if(empty($conditions["validation"]))
		$conditions["validation"] = 1;
	if(empty($conditions["illustre_seulement"]))
		$conditions["illustre_seulement"] = true;
		
}
else{
	$champs = array(
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
				array("champ"=>"evenement.telephone","alias"=>"telephone"),
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));
	$from = "FROM evenement";
	$join = "JOIN utilisateur ON utilisateur.no=evenement.no_utilisateur_creation";
	$join .= " JOIN genre ON genre.no=evenement.no_genre";
	$join .= " JOIN villes ON villes.id=evenement.no_ville";
	
	$conditions["no"] = $no;
}

//On construit maintenant la requête avec les champs, from et join.
$requete = "SELECT ";
	$les_champs = "";
	for($r=0;$r<count($champs);$r++){
		$les_champs .= (($les_champs!="")?", ":"").$champs[$r]["champ"].((!empty($champs[$r]["alias"]))?(" AS ".$champs[$r]["alias"]):"");
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
	if(!empty($conditions["no"])){
		$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.no=:no";
		$param[":no"] = $conditions["no"];
	}
	else{
		if(!empty($conditions["etat"])){
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.etat=:etat";
			$param[":etat"] = $conditions["etat"];
		}
		if(!empty($conditions["validation"])){
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.validation=:validation";
			$param[":validation"] = $conditions["validation"];
		}
		if(!empty($conditions["illustre_seulement"])&&$conditions["illustre_seulement"]){
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.url_image<>''";
		}
		if(!empty($conditions["du"])){ //Date de limite inférieure
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.date_fin>=:du";
			$param[":du"] = $conditions["du"];
		}
		if(!empty($conditions["au"])){ //Date de limite supérieure
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.date_debut<=:au";
			$param[":au"] = $conditions["au"];
		}
		if(!empty($conditions["courte_duree_seulement"])&&$conditions["courte_duree_seulement"]){ //Evenement de plus d'un mois à bannir
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.date_fin<=DATE_ADD(evenement.date_debut, INTERVAL 31 DAY)";
		}
		if(!empty($conditions["ville_seulement"])&&$conditions["ville_seulement"]){ //On n'élargie pas la recherche à la BDD, mais seulement à la ville
			$les_conditions .= (($les_conditions!="")?" AND ":"")."evenement.no_ville=:id_ville";
			$param[":id_ville"] = $ID_VILLE;
		}
	}
	
	if(!empty($les_conditions))
		$requete .= " WHERE ".$les_conditions;


if(!empty($ordre))
	$requete .= " ".$ordre;
if(!empty($limit))
	$requete .= " ".$limit;
?>
