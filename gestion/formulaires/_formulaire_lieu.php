<?php
$contenu .= '<div class="bloc" id="lieu">';
	$contenu .= '<div>';
		$contenu .= '<h1>Lieu</h1>';
		
		$contenu .= '<table>';
			if($PAGE=="evenement"||$PAGE=="structure"){
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDnomadresse">Nom du lieu : </label></td>';
					$contenu .= '<td><input type="text" name="BDDnomadresse" id="BDDnomadresse" title="nom du lieu" value="'.$nom_lieu.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDadresse">Adresse : </label></td>';
					$contenu .= '<td><input type="text" name="BDDadresse" id="BDDadresse" title="adresse" value="'.$adresse.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
			}
			
			$contenu .= '<tr>';
				$contenu .= '<td><label for="ville">Ville *: </label></td>';
				$contenu .= '<td><input type="text" name="ville" id="ville" title="ville" value="'.$ville.'" class="recherche_ville" /><input type="hidden" name="BDDno_ville" id="BDDno_ville" value="'.$no_ville.'" /><div id="recherche_ville_liste"><div></div></div></td>';
			$contenu .= '</tr>';
			
//			if($PAGE=="evenement"||$PAGE=="structure"){
//				$contenu .= '<tr>';
//					$contenu .= '<td><label for="BDDtelephone">Téléphone : </label></td>';
//					$contenu .= '<td><input type="text" name="BDDtelephone" id="BDDtelephone" title="telephone" value="'.$telephone.'" size="10" /></td>';
//				$contenu .= '</tr>';
//				$contenu .= '<tr>';
//					$contenu .= '<td><label for="BDDtelephone2">Mobile : </label></td>';
//					$contenu .= '<td><input type="text" name="BDDtelephone2" id="BDDtelephone2" title="Mobile" value="'.$telephone2.'" size="10" /></td>';
//				$contenu .= '</tr>';
//				$contenu .= '<tr>';
//					$contenu .= '<td><label for="BDDemail">e-mail : </label></td>';
//					$contenu .= '<td><input type="text" name="BDDemail" id="BDDemail" title="email" value="'.$email.'" class="moyen_input" /></td>';
//				$contenu .= '</tr>';
//			}
		$contenu .= '</table>';
		
	$contenu .= '</div>';
$contenu .= '</div>';
?>
