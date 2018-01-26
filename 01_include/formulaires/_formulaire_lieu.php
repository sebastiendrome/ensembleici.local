<?php
$contenu .= '<div class="bloc" id="lieu">';
	$contenu .= '<div>';
		$contenu .= '<h1>Lieu</h1>';
		
		$contenu .= '<table>';
			if($PAGE=="evenement"||$PAGE=="structure"){
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDnomadresse">Nom du lieu : </label></td>';
					$contenu .= '<td><input type="text" name="BDDnomadresse" id="BDDnomadresse" placeholder="nom du lieu" value="'.$nom_lieu.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDadresse">Adresse : </label></td>';
					$contenu .= '<td><input type="text" name="BDDadresse" id="BDDadresse" placeholder="adresse" value="'.$adresse.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
			}
			
			$contenu .= '<tr>';
				$contenu .= '<td><label for="ville">Ville *: </label></td>';
				$contenu .= '<td><input type="text" name="ville" id="ville" placeholder="ville" value="'.$ville.'" class="recherche_ville" /><input type="hidden" name="BDDno_ville" id="BDDno_ville" value="'.$no_ville.'" /><div id="recherche_ville_liste"><div></div></div></td>';
			$contenu .= '</tr>';
                        
                        if($PAGE!="petite-annonce"){
                            if($PAGE=="evenement"){
                                $contenu .= '<tr>';
                                    $contenu .= '<td><label for="BDDsite">Site internet : </label></td>';
                                    $contenu .= '<td><input type="text" name="BDDsite" id="BDDsite" placeholder="coller l\'adresse du site dans ce champ" value="'.$site.'" class="grand_input" /></td>';
                                $contenu .= '</tr>';
                            }
                            else {
                                if($PAGE=="structure"){
                                    $contenu .= '<tr>';
                                            $contenu .= '<td><label for="BDDsite_internet">Site internet : </label></td>';
                                            $contenu .= '<td><input type="text" name="BDDsite_internet" id="BDDsite_internet" placeholder="coller l\'adresse du site dans ce champ" value="'.$site.'" class="grand_input" /></td>';
                                    $contenu .= '</tr>';
                                    $contenu .= '<tr>';
                                            $contenu .= '<td><label for="BDDfacebook">Facebook : </label></td>';
                                            $contenu .= '<td><input type="text" name="BDDfacebook" id="BDDfacebook" placeholder="coller l\'adresse de la page facebook dans ce champ" value="'.$facebook.'" class="grand_input" /></td>';
                                    $contenu .= '</tr>';
                                }
                            }
                        }
                        else {
                            $contenu .= '<tr>';
                                    $contenu .= '<td><label for="BDDsite">Site internet : </label></td>';
                                    $contenu .= '<td><input type="text" name="BDDsite" id="BDDsite" placeholder="coller l\'adresse du site dans ce champ" value="'.$site.'" class="grand_input" /></td>';
                            $contenu .= '</tr>';
                        }
			
//			if($PAGE=="evenement"||$PAGE=="structure"){
//				$contenu .= '<tr>';
//					$contenu .= '<td><label for="BDDtelephone">Téléphone du lieu : </label></td>';
//					$contenu .= '<td><input type="text" name="BDDtelephone" id="BDDtelephone" placeholder="telephone" value="'.$telephone.'" size="15" /></td>';
//				$contenu .= '</tr>';
//				$contenu .= '<tr>';
//					$contenu .= '<td><label for="BDDtelephone2">Mobile du lieu : </label></td>';
//					$contenu .= '<td><input type="text" name="BDDtelephone2" id="BDDtelephone2" placeholder="Mobile" value="'.$telephone2.'" size="15" /></td>';
//				$contenu .= '</tr>';
//				$contenu .= '<tr>';
//					$contenu .= '<td><label for="BDDemail">e-mail du lieu : </label></td>';
//					$contenu .= '<td><input type="text" name="BDDemail" id="BDDemail" placeholder="email" value="'.$email.'" class="moyen_input" /></td>';
//				$contenu .= '</tr>';
//			}
		$contenu .= '</table>';
		
	$contenu .= '</div>';
$contenu .= '</div>';
?>
