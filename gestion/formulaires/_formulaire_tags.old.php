<?php
$contenu .= '<div class="bloc" id="tags">';
	$contenu .= '<div>';
		$contenu .= '<h1>Tags</h1>';
		$contenu .= '<div style="text-align:center;">';
			$contenu .= '<div class="infos">';
				$contenu .= 'renseignez les tags si vous souhaitez que votre '.$libelle_item.' soit correctement référencé'.((!$est_feminin)?'':'e');
			$contenu .= '</div>';
		$contenu .= '</div>';
		$contenu .= '<div><input type="button" value="Ajouter" /></div>';
	$contenu .= '</div>';
$contenu .= '</div>';
?>
