<?php
$contenu_droite = contenu_colonne_droite("recherche");
$param = array("recherche"=>$_POST["q"],"distance"=>-1);
function affiche_liste($tab_item,$type){
	if($type=="editorial"){
		$FORMAT_IMAGE = "16/9";
		
	}
	else{
		$FORMAT_IMAGE = "carre";
		
	}
	$contenu = '';
	for($i_item=0;$i_item<count($tab_item);$i_item++){
		global $root_site, $NOM_VILLE, $ID_VILLE;
		$contenu .= '<div class="liste_ligne'.(($type!="forum"||!$tab_item[$i_item]["est_citoyen"])?'':' citoyen').'">';
			$contenu .= '<div class="genre_ville noSmartphone">';
				$contenu .= '<span class="genre">'.$tab_item[$i_item]["genre"].'</span>';
				$contenu .= '<span class="ville">'.$tab_item[$i_item]["ville"].'</span>';
			$contenu .= '</div>';
			$contenu .= '<h3>';
				$contenu .= '<a href="'.$root_site.$type.'.'.url_rewrite($NOM_VILLE).'.'.url_rewrite(strip_tags($tab_item[$i_item]["titre"])).'.'.$ID_VILLE.'.'.$tab_item[$i_item]["no"].'.html">';
					$contenu .= $tab_item[$i_item]["titre"];
				$contenu .= '</a>';
			$contenu .= '</h3>';
			//Editorial
			if($type=="editorial"){
				$contenu .= '<div class="source">';
					$contenu .= '<div>le '.$tab_item[$i_item]["date_creation"].' par '.$tab_item[$i_item]["pseudo"].'</div>';
				$contenu .= '</div>';
			}
			//Agenda
			else if($type=="agenda"){
				if($tab_item[$i_item]["date_debut"]!=$tab_item[$i_item]["date_fin"])
					$date = "du <b>".$tab_item[$i_item]["date_debut"]."</b> au <b>".$tab_item[$i_item]["date_fin"]."</b>";
				else
					$date = "le <b>".$tab_item[$i_item]["date_debut"]."</b>";
				$contenu .= '<div class="source">';
					$contenu .= '<div>'.$date.'</div>';
				$contenu .= '</div>';
			}
			//Petite annonce
			else if($type=="petite-annonce"){
				$date = "valable jusqu'au <b>".$tab_item[$i_item]["date_fin"]."</b>";
				$contenu .= '<div class="source">';
					$contenu .= '<div>'.$date.'</div>';
				$contenu .= '</div>';
			}
			$contenu .= '<div class="genre_ville smartphone">';
				$contenu .= '<span class="genre">'.$tab_item[$i_item]["genre"].'</span>';
				$contenu .= '<span class="separateur_genre_ville"> - </span>';
				$contenu .= '<span class="ville">'.$tab_item[$i_item]["ville"].'</span>';
			$contenu .= '</div>';
			if(!empty($tab_item[$i_item]["image"])){
				$contenu .= '<a href="'.$root_site.$type.'.'.url_rewrite($NOM_VILLE).'.'.url_rewrite(strip_tags($tab_item[$i_item]["titre"])).'.'.$ID_VILLE.'.'.$tab_item[$i_item]["no"].'.html">';
					$contenu .= '<div class="image '.$FORMAT_IMAGE.' invisible">';
						$contenu .= '<img src="'.$tab_item[$i_item]["image"].'" />';
						$contenu .= $tab_item[$i_item]["div_fichier"];
					$contenu .= '</div>';
				$contenu .= '</a>';
			}
			$contenu .= '<div class="archiver noSmartphone infobulle" title="Archiver">&nbsp;</div>'.(($type!="petite-annonce")?'<div class="coupdecoeur noSmartphone infobulle" title="Coup de coeur">&nbsp;</div>':'');
			if($type=="agenda"){
				$contenu .= '<h4>'.$tab_item[$i_item]["sous_titresub"].'</h4>';
			}
			$contenu .= '<div class="description">'.$tab_item[$i_item]["descriptionsub"].'</div>';
			$contenu .= '<div class="coupdecoeur smartphone">'.(($type!="petite-annonce")?'<div>Coup de coeur</div>':'').'</div><div class="archiver smartphone"><div>Archiver</div></div>';
			$contenu .= '<div style="clear:both;"></div>';
		$contenu .= '</div>';
	}
	return $contenu;
}
$tab_edito = extraire_liste("editorial",30,1,$param);
	$liste_edito = $tab_edito["liste"];
$tab_agenda = extraire_liste("agenda",30,1,$param);
	$liste_agenda = $tab_agenda["liste"];
$tab_annonce = extraire_liste("petite-annonce",30,1,$param);
	$liste_annonce = $tab_annonce["liste"];
$tab_repertoire = extraire_liste("repertoire",30,1,$param);
	$liste_repertoire = $tab_repertoire["liste"];
$tab_forum = extraire_liste("forum",30,1,$param);
	$liste_forum = $tab_forum["liste"];

$nb_resultats = count($liste_edito)+count($liste_agenda)+count($liste_annonce)+count($liste_repertoire)+count($liste_forum);
$contenu = '<a onclick="window.history.go(-1)"><input type="button" value="Retour" class="ico fleche_gauche" style="margin-left:0em;" /></a>';
if(!empty($liste_edito)){
	$contenu .= '<div class="editorial">';
		$contenu .= '<h1>Éditorial ('.count($liste_edito).')</h1>';
		$contenu .= affiche_liste($liste_edito,"editorial");
	$contenu .= '</div>';
}
if(!empty($liste_agenda)){
	$contenu .= '<div class="agenda">';
		$contenu .= '<h1>Agenda ('.count($liste_agenda).')</h1>';
		$contenu .= affiche_liste($liste_agenda,"agenda");
	$contenu .= '</div>';
}
if(!empty($liste_annonce)){
	$contenu .= '<div class="petite-annonce">';
		$contenu .= '<h1>Petites annonces ('.count($liste_annonce).')</h1>';
		$contenu .= affiche_liste($liste_annonce,"petite-annonce");
	$contenu .= '</div>';
}
if(!empty($liste_repertoire)){
	$contenu .= '<div class="structure">';
		$contenu .= '<h1>Répertoire ('.count($liste_repertoire).')</h1>';
		$contenu .= affiche_liste($liste_repertoire,"structure");
	$contenu .= '</div>';
}
if(!empty($liste_forum)){
	$contenu .= '<div class="forum">';
		$contenu .= '<h1>Forums ('.count($liste_forum).')</h1>';
		$contenu .= affiche_liste($liste_forum,"forum");
	$contenu .= '</div>';
}

if(empty($nb_resultats)){
	$contenu .= '<p>Aucun résultat sur ensembleici.fr ne correspond à la recherche : <b>'.$_POST["q"].'</b></p>';
}

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
