<?php
$contenu .= '<div class="bloc" id="generalites">';
	$contenu .= '<div>';
		$contenu .= '<h1>Généralités</h1>';
	
		$contenu .= '<table>';
			$contenu .= '<tr>';
				$contenu .= '<td class="entete"><label for="BDD'.(($PAGE!="structure")?'titre':'nom').'">'.(($PAGE!="structure")?'Titre':'Nom').' *: </label></td>';
				$contenu .= '<td>';
					if(!empty($no_utilisateur))
						$contenu .= '<input type="hidden" name="BDDno_utilisateur_creation" id="BDDno_utilisateur_creation" value="'.$no_utilisateur.'" />';
					$contenu .= '<input type="hidden" name="BDDtype" id="BDDtype" value="'.$PAGE.'" />';
					$contenu .= '<input type="hidden" name="BDDno" id="BDDno" value="'.$NO.'" />';
					$contenu .= '<input type="text" name="BDD'.(($PAGE!="structure")?'titre':'nom').'" id="BDD'.(($PAGE!="structure")?'titre':'nom').'" title="'.(($PAGE!="structure")?'titre':'nom').'" value="'.$titre.'" class="grand_input" />';
				$contenu .= '</td>';
			$contenu .= '</tr>';
			if($PAGE!="petite-annonce"){
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDsous_titre">Sous-titre : </label></td>';
					$contenu .= '<td><input type="text" name="BDDsous_titre" id="BDDsous_titre" title="sous-titre" value="'.$sous_titre.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
				if($PAGE=="evenement"){
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDno_genre">Genre *: </label></td>';
						$contenu .= '<td>'.(creer_input_genres($no_genre)).'</td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDdate_debut">Date de début *: </label></td>';
						$contenu .= '<td><input type="text" name="BDDdate_debut" id="BDDdate_debut" title="jj/mm/aaaa" value="'.$date_deb.'" class="date" size="10" /><label for="BDDheure_debut"> à </label><input type="text" name="BDDheure_debut" id="BDDheure_debut" title="00h00" value="'.$heure_deb.'" class="heure" size="5" /></td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDdate_fin">Date de fin : </label></td>';
						$contenu .= '<td><input type="text" name="BDDdate_fin" id="BDDdate_fin" title="jj/mm/aaaa" value="'.$date_fin.'" class="date" size="10" /><label for="BDDheure_debut"> à </label><input type="text" name="BDDheure_fin" id="BDDheure_fin" title="00h00" value="'.$heure_fin.'" class="heure" size="5" /></td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDsite">Site internet : </label></td>';
						$contenu .= '<td><input type="text" name="BDDsite" id="BDDsite" title="coller l\'adresse du site dans ce champ" value="'.$site.'" class="grand_input" /></td>';
					$contenu .= '</tr>';
				}
				else if($PAGE=="structure"){
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDno_statut">Statut *: </label></td>';
						$contenu .= '<td>'.(creer_input_statuts($no_statut)).'</td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDsite_internet">Site internet : </label></td>';
						$contenu .= '<td><input type="text" name="BDDsite_internet" id="BDDsite_internet" title="coller l\'adresse du site dans ce champ" value="'.$site.'" class="grand_input" /></td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDfacebook">Facebook : </label></td>';
						$contenu .= '<td><input type="text" name="BDDfacebook" id="BDDfacebook" title="coller l\'adresse de la page facebook dans ce champ" value="'.$facebook.'" class="grand_input" /></td>';
					$contenu .= '</tr>';
				}
				else if($PAGE=="forum"){
					$contenu .= '<tr>';
						$contenu .= '<td><label for="BDDno_forum_type">Type du forum *: </label></td>';
						$contenu .= '<td>'.(creer_input_forumTypes($no_forum_type)).'</td>';
					$contenu .= '</tr>';
				}
//				if($_SESSION["droit"]["no"]==1){
//					$contenu .= '<tr>';
//						$contenu .= '<td><label for="BDDnb_aime">Nombre de like : </label></td>';
//						$contenu .= '<td><input type="text" name="BDDnb_aime" id="BDDnb_aime" title="0" value="'.$nb_aime.'" size="3" /></td>';
//					$contenu .= '</tr>';
//				}
			}
			else{ //Petite annonce
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDno_statut">Type d\'annonce *: </label></td>';
					$contenu .= '<td>'.(creer_input_petiteannonceTypes($no_petiteannonce_type)).'</td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDmonetaire">Monétaire : </label></td>';
					$contenu .= '<td class="'.($monetaire?'monetaire':'').'"><input type="checkbox" name="BDDmonetaire" id="BDDmonetaire" title="monétaire"'.($monetaire?' checked="checked"':'').' onclick="click_monetaire(this);" /><span id="prix"><input type="text" value="'.$prix.'" title="0.00" size="5" id="BDDprix" name="BDDprix" />€</span></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDdate_fin">Date de fin *: </label></td>';
					$contenu .= '<td><input type="text" name="BDDdate_fin" id="BDDdate_fin" title="jj/mm/aaaa" value="'.(!empty($date_fin)?$date_fin:date("d/m/Y" ,(time()+61*24*3600))).'" class="date" size="10" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDrayonmax">Rayon de diffusion : </label></td>';
					$contenu .= '<td><select name="BDDrayonmax" id="BDDrayonmax" title="rayon de diffusion"><option value="10"'.(($rayonmax!=10)?'':' selected="selected"').'> &lsaquo; 10 kms</option><option value="50"'.(($rayonmax!=50)?'':' selected="selected"').'> &lsaquo; 50 kms</option><option value="999"'.(($rayonmax!=999)?'':' selected="selected"').'> &rsaquo; 50 kms</option></select></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td><label for="BDDsite">Site internet : </label></td>';
					$contenu .= '<td><input type="text" name="BDDsite" id="BDDsite" title="coller l\'adresse du site dans ce champ" value="'.$site.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
			}
		$contenu .= '</table>';
	
	$contenu .= '</div>';
$contenu .= '</div>';
?>
