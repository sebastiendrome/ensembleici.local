<?php
if($PAGE_COURANTE=="editorial"){
	$FORMAT_IMAGE = "16/9";
}
else{
	$FORMAT_IMAGE = "carre";
}
$tab_item = extraire_fiche($PAGE_COURANTE,$NO);
$contenu = '<div class="fiche">';
	$contenu .= "<h1>".$tab_item["titre"]."</h1>";
	if($PAGE_COURANTE=="editorial")
		$contenu .= '<h2>le '.$tab_item["date_creation"].' par '.$tab_item["pseudo"].'</h2>';
	else if($PAGE_COURANTE=="agenda"){
		$contenu .= '<h2>'.$tab_item["sous_titre"].'</h2>';
		$contenu .= $tab_item["date_du_au"];
	}
	
	$contenu .= '<div class="illustration_fichiers">';
		$contenu .= '<div class="image '.$FORMAT_IMAGE.'" id="zone_fichiers">';
			$contenu .= '<img src="'.$tab_item["image"].'" />';
		$contenu .= '</div>';
	
		if($PAGE_COURANTE=="editorial"){
			$contenu .= '<div style="position:relative;">';
			$nb_fichiers_audio = count($tab_item["fichiers_audio"]);
			if($nb_fichiers_audio>0){
				$url_premier_fichier_audio = $tab_item["fichiers_audio"][0]["url"];
				$contenu .= '<div class="bouton_media media_audio" id="bouton_audio" onmouseover="ouvrir_liste_fichiers(\'liste_audio\')"><img src="http://www.ensembleici.fr/00_dev_sam/img/ico_file_audio.png" />'.$nb_fichiers_audio.' fichier'.(($nb_fichiers_audio==1)?'':'s').' audio</div>';
				$contenu .= '<div class="liste_media" id="liste_audio" onmouseout="fermer_liste_fichiers(\'liste_audio\')">';
				for($indice_audio=0;$indice_audio<$nb_fichiers_audio;$indice_audio++){
					$contenu .= '<div class="ligne_media" onclick="lancer_fichier_audio(\'zone_fichiers\',\''.$tab_item["fichiers_audio"][$indice_audio]["url"].'\')">'.$tab_item["fichiers_audio"][$indice_audio]["titre"].'</div>'; // onmouseover="ouvrir_menu('principal');"
				}
				$contenu .= '</div>';
			}
			else
				$url_premier_fichier_audio = false;
			/*<iframe width="100%" height="166" scrolling="no" frameborder="no" 
			src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/161780438&amp;color=0066cc&amp;auto_play=true&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
				for($indice_fichier=0;$indice_fichier<count($tab_item["fichiers_audio"]);$indice_fichier++){
					$contenu .= '<div class="bouton_media">'.$tab_item["fichiers_audio"][$indice_fichier]["titre"].'</div>'; // onmouseover="ouvrir_menu('principal');"
				}*/
			$contenu .= '</div>';
		}
	$contenu .= '</div>';
	if($PAGE_COURANTE=="agenda"){
		$contenu .= $tab_item["coordonnees"];
	}
	
	$contenu .= $tab_item["description"];
$contenu .= '</div>';
/*
if($url_premier_fichier_audio!=false)
	$contenu .= '<script type="text/javascript">lancer_fichier_audio("zone_fichiers","'.$url_premier_fichier_audio.'")</script>';
*/

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>extraire_editorial_ei()));
$lignes = array(array("lignes"=>$ligne1));
?>
