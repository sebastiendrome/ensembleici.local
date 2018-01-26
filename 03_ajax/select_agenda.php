<?php
/*************
Ce fichier permet de générer l'agenda
*****/
/*
$page_home = true;
$de = 0;
$a = 0;
$nb_pages_aff = 3;
$le_tri_evts = "distance";
$le_rayon_evts = 100;
$le_a_partir_du = date("d/m/y");
$id_ville = 9568;*/
//$cond_rayon_sql = " AND acos(sin($lat_ville_age)*sin(radians(latitude)) + cos($lat_ville_age)*cos(radians(latitude))*cos(radians(longitude)-$lon_ville_age))*$rayon_terre <= ".$le_rayon_evts;

function select_evenements($id_ville,$nb_aff=4,$du=false,$au=false){
	//1. On récupère les paramètres
	global $connexion;
	if($du==false)
		$du = date("Y-m-d");

	//2. On créait la requête à partir de ces paramètres
		//2.1 le select
		$select = "SELECT E.no, E.titre, E.date_debut, E.date_fin, E.url_image, G.libelle AS libelle_genre";
		$from = "FROM `evenement` E";
		$join = "JOIN `genre` G ON G.no=E.no_genre";
		$where = "WHERE E.no_ville=:id_ville AND E.url_image<>'' AND E.date_fin>=:du".(($au!=false)?" AND E.date_debut<=:au":"");
		$order = "ORDER BY RAND()";
		$limit = "LIMIT 0,".$nb_aff;
		$requete = $select." ".$from." ".$join." ".$where." ".$ordre." ".$limit;
		$res = $connexion->prepare($requete);
		$res->execute(array(":id_ville"=>$id_ville,":du"=>$du));
		$tab_evenements = $res->fetchAll();
	
	return $tab_evenements;
}

function afficher_evenement_accueil($tab_evenements){
	$les_contenus = array();
	for($i=0;$i<count($tab_evenements);$i++){
		if(substr($tab_evenements[$i]["url_image"],0,7)!="http://")
			$tab_evenements[$i]["url_image"] = "http://www.ensembleici.fr/".$tab_evenements[$i]["url_image"];
		$les_contenus[] = "<div class='image 4/3' style='width:100%'><img src='".$tab_evenements[$i]["url_image"]."' />".$tab_evenements[$i]["titre"]."</div>";
	}
	$retour = '<div id="zone_bloc_evenement_accueil" class="zone_petite">';
		for($i=0;$i<count($les_contenus);$i++){
			if($i<1){
				$retour .= '<div class="bloc_evenement_accueil bloc_gauche"><div>'.$les_contenus[$i].'</div></div>';
			}
			else if($i>2){
				$retour .= '<div class="bloc_evenement_accueil bloc_droite"><div>'.$les_contenus[$i].'</div></div>';
			}
			else{
				$retour .= '<div class="bloc_evenement_accueil bloc_milieu"><div>'.$les_contenus[$i].'</div></div>';
				/*
				if($i==1)
					$retour .= '<div class="bloc_evenement_accueil bloc_milieu">';
					
					$retour .= '<div><div>'.$les_contenus[$i].'</div></div>';
					
				if($i==2)
					$retour .= '</div>';*/
			}
		}
	$retour .= '</div>';
		
		
	return $retour;
}
?>
