<?php
/*header('Content-Type: text/html; charset=UTF-8');
require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";*/

$les_vies = get_vies();
$les_tags = get_ficheTags($PAGE,$NO,true);

$liste_tags = "";
for($t=0;$t<count($les_tags);$t++){
	//On créait la liste des tags sélectionnés
	$liste_tags .= ((!empty($liste_tags))?",":"").$les_tags[$t]["no"];
	//On créait le html de cette liste
	$contenu_tag .= '<div class="un_tag'.$les_tags[$t]["class"].'">';
		$contenu_tag .= '<input type="checkbox" checked="checked" id="tag_'.$les_tags[$t]["no"].'" onclick="tag_click(this);" />';
		$contenu_tag .= '<label for="tag_'.$les_tags[$t]["no"].'">'.$les_tags[$t]["titre"].'</label>';
	$contenu_tag .= '</div>';
}

$tous_tags = get_tags("",$liste_tags,"",true);
$liste_tous_tags = "";
for($t=0;$t<count($tous_tags);$t++){
	//On créait la liste des tags dispos
	$liste_tous_tags .= ((!empty($liste_tous_tags))?",":"").$tous_tags[$t]["no"];
	//on créait le html de cette liste
	$contenu_tous_tag .= '<div class="un_tag'.$tous_tags[$t]["class"].'">';
		$contenu_tous_tag .= '<input type="checkbox" id="tag_'.$tous_tags[$t]["no"].'" onclick="tag_click(this);" />';
		$contenu_tous_tag .= '<label for="tag_'.$tous_tags[$t]["no"].'">'.$tous_tags[$t]["titre"].'</label>';
	$contenu_tous_tag .= '</div>';
}


$contenu .= '<div class="bloc" id="tags">';
	$contenu .= '<div>';
		$contenu .= '<h1>Tags</h1>';
		$contenu .= '<div style="text-align:center;">';
			$contenu .= '<div class="infos">';
				$contenu .= 'renseignez les tags si vous souhaitez que votre '.$libelle_item.' soit correctement référencé'.((!$est_feminin)?'':'e');
			$contenu .= '</div>';
		$contenu .= '</div>';
		$contenu .= '<div style="text-align:center;">';
			
			$contenu .= '<div class="vie-toute" id="boite_tag">';
				$contenu .= '<div class="options">';
					$contenu .= '<div>';
						$contenu .= '<select name="vie" id="select_vie" onchange="set_vie(this);">';
							$contenu .= '<option value="vie-toute" selected="selected">Toutes les vies</option>';
						for($v=0;$v<count($les_vies);$v++){
							//$contenu .= '<option value="'.$les_vies[$v]["no"].'"'.(($_POST["v"]!=$les_vies[$v]["no"])?'':' selected="selected"').'>'.$les_vies[$v]["libelle"].'</option>';
							$contenu .= '<option value="'.url_rewrite($les_vies[$v]["libelle"]).'_'.$les_vies[$v]["no"].'">'.$les_vies[$v]["libelle"].'</option>';
						}
						$contenu .= '</select>';
					$contenu .= '</div>';
					$contenu .= '<div>';
						$contenu .= '<input type="text" class="recherche_tag" />';
						$contenu .= '<input type="button" class="recherche_tag" />';
					$contenu .= '</div>';
				$contenu .= '</div>';
				$contenu .= '<div class="tags">';
					$contenu .= '<div>';
						$contenu .= '<div class="libelle">';
						$contenu .= 'Tags disponibles';
						$contenu .= '</div>';
						$contenu .= '<div class="liste_tags" id="tags_dispo">';
							$contenu .= $contenu_tous_tag;
						$contenu .= '</div>';
			
					$contenu .= '</div>';
					$contenu .= '<div>';
						$contenu .= '<div class="libelle">';
						$contenu .= 'Tags sélectionnés';
						$contenu .= '</div>';
						$contenu .= '<div class="liste_tags" id="tags_select">';
							$contenu .= $contenu_tag;
						$contenu .= '</div>';
			
					$contenu .= '</div>';
				$contenu .= '</div>';
			$contenu .= '</div>';
			
		$contenu .= '</div>';
	$contenu .= '</div>';
$contenu .= '</div>';

?>
