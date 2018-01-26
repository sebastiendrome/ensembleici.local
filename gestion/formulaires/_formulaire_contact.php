<?php
$contenu .= '<div class="bloc" id="contact">';
	$contenu .= '<div>';
		$contenu .= '<h1>Contact</h1>';
		if($_SESSION["utilisateur"]["no"]==$no_utilisateur||empty($no_utilisateur)){
			$contenu .= '<div>';
				$contenu .= '<input type="checkbox"'.(($mes_coordonnees)?' checked="checked"':'').' name="BDDcontact_mes_coordonnees" id="BDDcontact_mes_coordonnees" title="Utiliser mes coordonnées" onclick="click_moiMeme(this)" />';
				$contenu .= '<label for="BDDcontact_mes_coordonnees">&nbsp;Utiliser mes propre coordonnées</label>';
			$contenu .= '</div>';
		
			$contenu .= '<div id="moiMeme_ou_autreContact" class="'.(($mes_coordonnees)?'moi_meme':'autre_contact').'">';
				$contenu .= '<div id="moi_meme">';
					$contenu .= '<table>';
						$contenu .= '<tr>';
							$contenu .= '<td>Personne à contacter : </td>';
							$contenu .= '<td>'.$_SESSION["utilisateur"]["pseudo"].'</td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>Téléphone : </td>';
							$contenu .= '<td>'.$_SESSION["utilisateur"]["telephone"].'</td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>Mobile : </td>';
							$contenu .= '<td>'.$_SESSION["utilisateur"]["mobile"].'</td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>email : </td>';
							$contenu .= '<td>'.$_SESSION["utilisateur"]["email"].'</td>';
						$contenu .= '</tr>';
					$contenu .= '</table>';
					$contenu .= '<span class="lien" onclick="fenetre_pseudo();">modifier mes informations</span>';
				$contenu .= '</div>';
				$contenu .= '<div id="autre_contact">';
					$contenu .= '<table>';
						$contenu .= '<tr>';
							$contenu .= '<td>Personne à contacter : </td>';
							$contenu .= '<td><input type="text" name="BDDnom_contact" id="BDDnom_contact" title="nom de la personne" value="'.$nom_contact.'" />';
                                                        $contenu .= '<div id="recherche_contact_liste"></div><span class="hide" id="BDDno_contact">'.$no_contact.'</span></td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>Téléphone : </td>';
							$contenu .= '<td><input type="text" name="BDDtelephone_contact" id="BDDtelephone_contact" title="téléphone" value="'.$telephone_contact.'" /></td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>Mobile : </td>';
							$contenu .= '<td><input type="text" name="BDDtelephone2_contact" id="BDDtelephone2_contact" title="mobile" value="'.$mobile_contact.'" /></td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>email : </td>';
							$contenu .= '<td><input type="text" name="BDDemail_contact" id="BDDemail_contact" title="email" value="'.$email_contact.'" /></td>';
						$contenu .= '</tr>';
					$contenu .= '</table>';
				$contenu .= '</div>';
			$contenu .= '</div>';
			//TODO ajouter le rôle pour les deux situations
		}
		else{
			$contenu .= '<table>';
				$contenu .= '<tr>';
					$contenu .= '<td>Personne à contacter : </td>';
					$contenu .= '<td><input type="text" name="BDDnom_contact" id="BDDnom_contact" title="titre" value="'.$nom_contact.'" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td>Téléphone : </td>';
					$contenu .= '<td><input type="text" name="BDDtelephone_contact" id="BDDtelephone_contact" title="téléphone" value="'.$telephone_contact.'" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td>Mobile : </td>';
					$contenu .= '<td><input type="text" name="BDDtelephone2_contact" id="BDDtelephone2_contact" title="Mobile" value="'.$mobile_contact.'" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td>email : </td>';
					$contenu .= '<td><input type="text" name="BDDemail_contact" id="BDDemail_contact" title="email" value="'.$email_contact.'" /></td>';
				$contenu .= '</tr>';
			$contenu .= '</table>';
		}
	$contenu .= '</div>';
$contenu .= '</div>';
?>
