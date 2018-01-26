<?php
$contenu .= '<div class="bloc" id="illustration">';
	$contenu .= '<div>';
		$contenu .= '<h1>Illustration</h1>';
		
			if($PAGE=="evenement"||$PAGE=="editorial"){
				$contenu .= '<div>';
					$contenu .= '<div class="infos">';
						$contenu .= 'N\'oubliez pas de choisir une image si vous souhaitez que votre  '.$libelle_item.' soit mis'.((!$est_feminin)?'':'e').' en avant';
					$contenu .= '</div>';
				$contenu .= '</div>';
			}
                        $contenu .= "<div style='color:#CC0000;'>Vous pouvez charger des illustrations au format image (jpg, png) et d'une taille maximale de 5 Mo. Format carré conseillé.</div>";
			$contenu .= '<table>';
				$contenu .= '<tr>';
					$contenu .= '<td rowspan="2" style="text-align:right;">';
						$contenu .= '<input type="file" id="BDD'.(($PAGE!="structure")?'url_image':'url_logo').'" name="BDD'.(($PAGE!="structure")?'url_image':'url_logo').'" class="fichier poids[0|500] type[image] url['.(($url_image!=false)?str_replace("http://www.ensembleici.fr/","",$url_image):'').']" />';
					$contenu .= '</td>';
					$contenu .= '<td style="text-align: left;">';
						$contenu .= '<input type="text" id="BDDcopyright" name="BDDcopyright" value="'.$copyright.'" placeholder="copyright" class="moyen_input" />';
					$contenu .= '</td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td style="text-align: left;">';
						$contenu .= '<textarea type="text" maxlength="255" id="BDDlegende" name="BDDlegende" placeholder="légende de l\'image" class="moyen_input">'.$legende.'</textarea>';
					$contenu .= '</td>';
				$contenu .= '</tr>';
			$contenu .= '</table>';
		
	$contenu .= '</div>';
$contenu .= '</div>';
?>
