<?php
$contenu .= '<div class="bloc" id="contact">';
	$contenu .= '<div>';
		$contenu .= '<h1>Contact</h1>';
		
		if(empty($no_utilisateur)||$_SESSION["utilisateur"]["no"]==$no_utilisateur){
		
			if($PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"){
				$contenu .= '<div>';
					$contenu .= '<input type="checkbox" checked="checked" name="BDDmoi_meme" id="BDDmoi_meme" title="Utiliser mes coordonées" onclick="click_moiMeme(this)" />';
					$contenu .= '<label for="BDDmoi_meme">&nbsp;Utiliser mes coordonnées.</label>';
				$contenu .= '</div>';
			}
			else{
				$contenu .= '<div>';
					$contenu .= '<input type="checkbox" checked="checked" name="BDDafficher_signature" id="BDDafficher_signature" title="Signer l\'article" onclick="click_moiMeme(this)" />';
					$contenu .= '<label for="BDDafficher_signature">&nbsp;Signer l\'article avec votre nom d\'utilisateur</label>';
				$contenu .= '</div>';
			}
			
			$contenu .= '<div id="moiMeme_ou_autreContact" class="moi_meme">';
			
				$contenu .= '<div id="moi_meme">';
					if($PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"){
						//TODO : vérifier les coordonnées de l'utilisateur
					}
					else{
						if(!empty($_SESSION["utilisateur"]["pseudo"])){
							$contenu .= '<br />Nom d\'utilisateur : <span id="span_pseudo2">'.$_SESSION["utilisateur"]["pseudo"].'</span>';
							$contenu .= '<span class="lien" onclick="fenetre_pseudo();">modifier mes informations</span><br />';
						}
						else{
							$contenu .= '<input type="text" name="BDDpseudo" id="BDDpseudo" title="nom d\'utilisateur" />';
						}
					}
				$contenu .= '</div>';
			
				$contenu .= '<div id="autre_contact">';
					$contenu .= '<br/>';
					if($PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"){
						$contenu .= '<table>';
							$contenu .= '<tr>';
								$contenu .= '<td>Personne à contacter : </td>';
								$contenu .= '<td><input type="text" name="BDDnom_contact" id="BDDnom_contact" title="titre" /></td>';
							$contenu .= '</tr>';
							if($PAGE=="evenement"||$PAGE=="structure"){
								$contenu .= '<tr>';
									$contenu .= '<td>Rôle : </td>';
									$contenu .= '<td><input type="text" name="BDDno_role" id="BBDno_role" title="Rôle" /></td>';
								$contenu .= '</tr>';
							}
							$contenu .= '<tr>';
								$contenu .= '<td>Téléphone : </td>';
								$contenu .= '<td><input type="text" name="BDDtelephone_contact" id="BDDtelephone_contact" title="téléphone" /></td>';
							$contenu .= '</tr>';
							$contenu .= '<tr>';
								$contenu .= '<td>Mobile : </td>';
								$contenu .= '<td><input type="text" name="BDDtelephone2_contact" id="BDDtelephone2_contact" title="Mobile" /></td>';
							$contenu .= '</tr>';
							$contenu .= '<tr>';
								$contenu .= '<td>email : </td>';
								$contenu .= '<td><input type="text" name="BDDemail_contact" id="BDDemail_contact" title="email" /></td>';
							$contenu .= '</tr>';
						$contenu .= '</table>';
					}
					else{
						$contenu .= '<label for="BDDsignature">Signature pour cet article : </label><input type="text" id="BDDsignature" value="" />';
					}
				$contenu .= '</div>';
				
			$contenu .= '</div>';
			
		}
		else{
			if($PAGE=="evenement"||$PAGE=="structure"||$PAGE=="petite-annonce"){
		
				$contenu .= '<table>';
					$contenu .= '<tr>';
						$contenu .= '<td>Personne à contacter : </td>';
						$contenu .= '<td><input type="text" name="BDDnom_contact" id="BDDnom_contact" title="titre" /></td>';
					$contenu .= '</tr>';
					if($PAGE=="evenement"||$PAGE=="structure"){
						$contenu .= '<tr>';
							$contenu .= '<td>Rôle : </td>';
							$contenu .= '<td><input type="text" name="BDDno_role" id="BBDno_role" title="Rôle" /></td>';
						$contenu .= '</tr>';
					}
					$contenu .= '<tr>';
						$contenu .= '<td>Téléphone : </td>';
						$contenu .= '<td><input type="text" name="BDDtelephone_contact" id="BDDtelephone_contact" title="téléphone" /></td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td>Mobile : </td>';
						$contenu .= '<td><input type="text" name="BDDtelephone2_contact" id="BDDtelephone2_contact" title="Mobile" /></td>';
					$contenu .= '</tr>';
					$contenu .= '<tr>';
						$contenu .= '<td>email : </td>';
						$contenu .= '<td><input type="text" name="BDDemail_contact" id="BDDemail_contact" title="email" /></td>';
					$contenu .= '</tr>';
				$contenu .= '</table>';
		
			}
			else{
				
				$contenu .= '<div>Auteur : '.$pseudo.'</div>';
			}
		}
		
	$contenu .= '</div>';
$contenu .= '</div>';
?>
