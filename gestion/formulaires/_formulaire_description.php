<?php

$contenu .= '<div class="bloc" id="description">';
	$contenu .= '<div>';
		$contenu .= '<h1>'.(($PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce")?'Description':'Contenu').'</h1>';
		
		if($PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"){
			$contenu .= '<textarea id="BDDdescription" name="BDDdescription" class="editeur height[300px]">'.$description.'</textarea>';
			if($PAGE=="evenement"){
				$contenu .= '<h2>Description complémentaire</h2>';
				$contenu .= '<textarea id="BDDdescription_complementaire" name="BDDdescription_complementaire" class="editeur height[200px]">'.$description_complementaire.'</textarea>';
			}
		}
		else if($PAGE=="forum"){
			$contenu .= '<textarea id="BDDcontenu" name="BDDcontenu" class="editeur height[300px]">'.$description.'</textarea>';
		}
		else{ //edito
			$contenu .= '<h2>Chapô</h2>';
			$contenu .= '<textarea id="BDDchapo" name="BDDchapo" class="editeur height[100px]">'.$chapo.'</textarea>';
			$contenu .= '<h2>Corps de l\'article</h2>';
			$contenu .= '<textarea id="BDDdescription" name="BDDdescription" class="editeur height[400px]">'.$description.'</textarea>';
			$contenu .= '<h2>Notes</h2>';
			$contenu .= '<textarea id="BDDnotes" name="BDDnotes" class="editeur height[100px]">'.$notes.'</textarea>';
		}
		
	$contenu .= '</div>';
$contenu .= '</div>';

?>
