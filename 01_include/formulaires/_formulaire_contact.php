<?php
if($PAGE=="evenement") $nom_type="l'événement";
else if($PAGE=="structure") $nom_type="la structure";
else if($PAGE=="petite-annonce") $nom_type="l'annonce";
else if($PAGE=="forum") $nom_type="le sujet";
else if($PAGE=="editorial") $nom_type="l'article";
$contenu .= '<br/><br/><div class="bloc" id="contact">';
	$contenu .= '<div>';
		$contenu .= '<h1>Contact</h1>';
		$table_contact = str_replace("-","",$PAGE);
		//1. On récupère tous les contacts pour l'item courant
		if($PAGE=="evenement"||$PAGE=="structure")
			$requete_contact = "SELECT contact.*, ".$table_contact."_contact.no_role FROM contact JOIN ".$table_contact."_contact ON contact.no=".$table_contact."_contact.no_contact WHERE ".$table_contact."_contact.no_".$table_contact."=:no";
		else
			$requete_contact = "SELECT contact.* FROM contact JOIN ".$table_contact."_contact ON contact.no=".$table_contact."_contact.no_contact WHERE ".$table_contact."_contact.no_".$table_contact."=:no";
		$tab_contact = execute_requete($requete_contact,array(":no"=>$NO));
		//2. Pour chaque contact, on créait le "bloc contact"
		for($i=0;$i<count($tab_contact);$i++){
			$contenu .= '<div class="un_contact" id="contact_'.$tab_contact[$i]["no"].'">';
				$contenu .= '<input type="hidden" value="'.$tab_contact[$i]["no"].'" id="BDDcontact_no_'.$i.'" name="BDDcontact_no_'.$i.'" />';
				$contenu .= '<div onclick="fenetre_creationModificationContact('.$tab_contact[$i]["no"].')">'.$tab_contact[$i]["nom"].'</div>';
				//if(isset($tab_contact[$i]["no_role"])){
					$contenu .= creer_input_contactRole($tab_contact[$i]["no_role"],'BDDcontact_no_role_'.$i,($PAGE!="structure"&&$PAGE!="evenement"));
				//}
				//else
				//	$contenu .= '<input type="hidden" value="0" id="BDDcontact_no_role_'.$i.'" name="BDDcontact_no_role_'.$i.'" />';
				$contenu .= '<span class="fermer" onclick="this.parentNode.parentNode.removeChild(this.parentNode);maj_input_no_contact();" class="infobulle" title="Retirer ce contact"></span>';
			$contenu .= '</div>';
		}
//                $contenu .= '<div style="clear:both;"></div>';
//		$contenu .= '<div class="un_contact nouveau" onclick="fenetre_creationModificationContact(0)">';
//                    $contenu .= "<div>Ajouter un contact pour ".$nom_type."</div>";
//		$contenu .= '</div>';
                $contenu .= '<div class="un_contact nouveau" onclick="fenetre_creationModificationContact(-1)">';
                    $contenu .= "<div>Ajouter un contact pour ".$nom_type."</div>";
		$contenu .= '</div>';
		
		/*
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
							$contenu .= '<td><input type="text" name="BDDnom_contact" id="BDDnom_contact" title="nom de la personne" /></td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>Téléphone : </td>';
							$contenu .= '<td><input type="text" name="BDDtelephone_contact" id="BDDtelephone_contact" title="téléphone" /></td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>Mobile : </td>';
							$contenu .= '<td><input type="text" name="BDDtelephone2_contact" id="BDDtelephone2_contact" title="mobile" /></td>';
						$contenu .= '</tr>';
						$contenu .= '<tr>';
							$contenu .= '<td>email : </td>';
							$contenu .= '<td><input type="text" name="BDDemail_contact" id="BDDemail_contact" title="email" /></td>';
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
					$contenu .= '<td><input type="text" name="BDDnom_contact" id="BDDnom_contact" title="titre" /></td>';
				$contenu .= '</tr>';
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
		}*/
	$contenu .= '</div>';
$contenu .= '</div>';
?>
