<?php
$contenu .= '<div class="bloc" id="contact">';
	$contenu .= '<div>';
		$contenu .= '<h1>Auteur</h1>';
		
		if($_SESSION["utilisateur"]["no"]==$no_utilisateur||empty($no_utilisateur)){ //Si c'est lutilisateur de création ou si aucun utilisateur de création (nouvelle fiche)
			if(empty($no_utilisateur)&&empty($afficher_signature)){ //C'est une nouvelle fiche, et afficher signature n'est pas renseigné (ce qui devrait toujours être le cas pour afficher_signature dans cette situation)
				$afficher_signature = true;
			}
			$contenu .= '<div>';
				$contenu .= '<input type="checkbox"'.(($afficher_signature)?' checked="checked"':'').' name="BDDafficher_signature" id="BDDafficher_signature" title="Signer '.(($PAGE=="editorial")?'l\'article':'le sujet').'" onclick="click_moiMeme(this)" />';
				$contenu .= '<label for="BDDafficher_signature">&nbsp;Signer '.(($PAGE=="editorial")?'l\'article':'le sujet').' avec votre nom d\'utilisateur</label>';
			$contenu .= '</div>';
			$contenu .= '<div id="moiMeme_ou_autreContact" class="'.(($afficher_signature)?'moi_meme':'autre_contact').'">';
				$contenu .= '<div id="moi_meme">';
					if(!empty($_SESSION["utilisateur"]["pseudo"])){
						$contenu .= '<br />Nom d\'utilisateur : <span id="span_pseudo2">'.$_SESSION["utilisateur"]["pseudo"].'</span>';
						$contenu .= '<span class="lien" onclick="fenetre_pseudo();">modifier mes informations</span><br />';
					}
					else{
						$contenu .= '<input type="text" name="BDDpseudo" id="BDDpseudo" placeholder="nom d\'utilisateur" />';
					}
				$contenu .= '</div>';
				$contenu .= '<div id="autre_contact">';
					$contenu .= '<label for="BDDsignature">Signature pour '.(($PAGE=="editorial")?'cet article':'ce sujet').' : </label><input type="text" id="BDDsignature" name="BDDsignature" value="'.$signature.'" placeholder="signature" />';
				$contenu .= '</div>';
			$contenu .= '</div>';
		}
		else{
			$contenu .= '<div>';
				$contenu .= 'signé par : <b>'.((!empty($signature))?$signature.' ('.$nom_utilisateur.')':$nom_utilisateur).'</b>';
			$contenu .= '</div>';
		}
		
	$contenu .= '</div>';
$contenu .= '</div>';
?>
