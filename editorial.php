<?php
if(!empty($NO)){ //Affichage d'une fiche
	$tab_edito = extraire_fiche("editorial",$NO);
	
	$contenu = "<h1>".$tab_edito["titre"]."</h1>";
	$contenu .= '<h2>le '.$tab_edito["date_creation"].' par '.$tab_edito["pseudo"].'</h2>';
	$contenu .= '<div style="display:inline-block;width:60%;float:left;margin-right:1em;">';
		$contenu .= '<div class="image 16/9" id="zone_fichiers">';
			$contenu .= '<img src="'.$tab_edito["image"].'" />';
		$contenu .= '</div>';
		$contenu .= '<div style="position:relative;">';
		
		$nb_fichiers_audio = count($tab_edito["fichiers_audio"]);
		if($nb_fichiers_audio>0){
			$url_premier_fichier_audio = $tab_edito["fichiers_audio"][0]["url"];
			$contenu .= '<div class="bouton_media media_audio" id="bouton_audio" onmouseover="ouvrir_liste_fichiers(\'liste_audio\')"><img src="http://www.ensembleici.fr/00_dev_sam/img/ico_file_audio.png" />'.$nb_fichiers_audio.' fichier'.(($nb_fichiers_audio==1)?'':'s').' audio</div>';
			$contenu .= '<div class="liste_media" id="liste_audio" onmouseout="fermer_liste_fichiers(\'liste_audio\')">';
			for($indice_audio=0;$indice_audio<$nb_fichiers_audio;$indice_audio++){
				$contenu .= '<div class="ligne_media" onclick="lancer_fichier_audio(\'zone_fichiers\',\''.$tab_edito["fichiers_audio"][$indice_audio]["url"].'\')">'.$tab_edito["fichiers_audio"][$indice_audio]["titre"].'</div>'; // onmouseover="ouvrir_menu('principal');"
			}
			$contenu .= '</div>';
		}
		else
			$url_premier_fichier_audio = false;
		$contenu .= '</div>';
	$contenu .= '</div>';
	if($url_premier_fichier_audio!=false)
		$contenu .= '<script type="text/javascript">lancer_fichier_audio("zone_fichiers","'.$url_premier_fichier_audio.'")</script>';
	$contenu .= $tab_edito["description"];
	$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>"vie et tag"));
	$lignes = array(array("lignes"=>$ligne1));
}
else{ //Afichage d'une liste
	include "liste.php";
}
?>
