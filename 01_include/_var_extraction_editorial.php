<?php

	
$table = "editorial";
$date_tri = "date_creation";
$param = array();

if($extraction=="liste"){
	$champs = array(
				array("champ"=>"editorial.no","alias"=>"no"),
				array("champ"=>"editorial.titre","alias"=>"titre"),
				array("champ"=>"editorial.description","alias"=>"description"),
				array("champ"=>"editorial.url_image","alias"=>"image"),
				array("champ"=>"editorial.source","alias"=>"source"),
				array("champ"=>"editorial.copyright","alias"=>"copyright"),
				array("champ"=>"editorial.date_creation","alias"=>"date_creation"),
				array("champ"=>"editorial.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));
	
	//On crait maintenant la requête avec les conditions
		/*$les_conditions = "";
		if(!empty($conditions["du"])){ //Date de départ
			$les_conditions = (($les_conditions!="")?", ":"")."editorial.date";
		}*/
		$where = "WHERE editorial.etat=1 AND editorial.validation>=1 AND editorial.url_image<>''";
	if(!$ordre)
		$ordre = "ORDER BY editorial.date_creation DESC";
	else
		$ordre = "ORDER BY ".$ordre;
	if(!empty($limit)&&$limit>0)
		$limit = "LIMIT ".$limit;
}
else{
	$champs = array(
				array("champ"=>"editorial.no","alias"=>"no"),
				array("champ"=>"editorial.titre","alias"=>"titre"),
				array("champ"=>"editorial.description","alias"=>"description"),
				array("champ"=>"editorial.url_image","alias"=>"image"),
				array("champ"=>"editorial.source","alias"=>"source"),
				array("champ"=>"editorial.copyright","alias"=>"copyright"),
				array("champ"=>"editorial.date_creation","alias"=>"date_creation"),
				array("champ"=>"editorial.nb_aime","alias"=>"nb_aime"),
				array("champ"=>"utilisateur.pseudo","alias"=>"pseudo"),
				array("champ"=>"genre.libelle","alias"=>"genre"),
				array("champ"=>"villes.nom_ville_maj","alias"=>"ville"));
				
	$param[":no"] = $no;
	$where = "WHERE editorial.no=:no";
}

$from = "FROM editorial";
$join = "JOIN utilisateur ON utilisateur.no=editorial.no_utilisateur_creation";
$join .= " JOIN genre ON genre.no=editorial.no_genre";
$join .= " JOIN villes ON villes.id=editorial.no_ville";
//On construit maintenant la requête avec les champs, from et join.
$requete = "SELECT ";
	$les_champs = "";
	for($r=0;$r<count($champs);$r++){
		$les_champs .= (($les_champs!="")?", ":"").$champs[$r]["champ"]." AS ".$champs[$r]["alias"];
	}
$requete .= $les_champs;
$requete .= " ".$from." ".$join;
$requete .= " ".$where;
if(!empty($ordre))
	$requete .= " ".$ordre;
if(!empty($limit))
	$requete .= " ".$limit;

//On créait maintenant la requête fichier
$requete_fichiers = "SELECT fichier_galerie.no_fichier_type, fichier_type.icone FROM fichier_type JOIN fichier_galerie ON fichier_galerie.no_fichier_type=fichier_type.no JOIN editorial_galerie ON editorial_galerie.no_galerie=fichier_galerie.no_galerie WHERE editorial_galerie.no_editorial=:no GROUP BY fichier_type.no";
$res_fichiers = preparer_requete($requete_fichiers);

$requete_tous_fichiers = "SELECT galerie.titre, galerie.description, fichier_galerie.no_fichier, fichier_type.libelle AS libelle_type, fichier_type.icone AS icone_type, utilisateur.pseudo
FROM editorial_galerie
JOIN galerie ON editorial_galerie.no_galerie=galerie.no
JOIN utilisateur ON galerie.no_utilisateur=utilisateur.no
JOIN fichier_galerie ON fichier_galerie.no_galerie=galerie.no
JOIN fichier_type ON fichier_type.no=fichier_galerie.no_fichier_type
WHERE editorial_galerie.no_editorial=:no
ORDER BY galerie.titre,fichier_galerie.position";
$res_tous_fichiers = preparer_requete($requete_tous_fichiers);

/*
$requete_fichiersaudio = "SELECT fichiersaudio.no, fichiersaudio.url, fichiersaudio.titre, fichiersaudio.description, fichiersaudio.auteur, fichiersaudio.date_creation FROM fichiersaudio JOIN fichiers_galerie
$requete_type_fichiers = "SELECT fichier_type.libelle, fichier_type.icone FROM fichier_type JOIN fichier_galerie ON fichier_galerie.no_fichier_type=fichier_type.no JOIN editorial_galerie ON editorial_galerie.no_galerie=fichier_galerie.no_galerie WHERE editorial_galerie.no_editorial=:no GROUP BY fichier_type.no";
$res_type_fichiers = preparer_requete($requete_type_fichiers);*/
?>
