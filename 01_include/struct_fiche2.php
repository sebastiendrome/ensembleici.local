<?php
if($PAGE_COURANTE=="editorial"){
	$FORMAT_IMAGE = "16/9";
	$table = "editorial";
}
else{
	$FORMAT_IMAGE = "carre";
	if($PAGE_COURANTE=="agenda"||$PAGE_COURANTE=="evenement")
		$table = "evenement";
	else if($PAGE_COURANTE=="structure")
		$table = "structure";
	else if($PAGE_COURANTE=="petiteannonce"||$PAGE_COURANTE=="petite-annonce")
		$table = "petiteannonce";
	else
		$table = "forum";
}

//if(!$PREVISUALISATION)
	//$tab_item = extraire_fiche($PAGE_COURANTE,$NO);
//else
	$tab_item = extraire_fiche($PAGE_COURANTE,$NO);
	
$contenu .= '<div class="en_tete_fiche">';
	if(!$PREVISUALISATION){
		$contenu .= '<a onclick="window.history.go(-1)"><input type="button" value="Retour" class="ico fleche_gauche couleur" style="margin-left:0em;" /></a>';
		/*if($PAGE_COURANTE=='agenda'||$PAGE_COURANTE=='structure'){
			$contenu .= '<a href="http://www.ensembleici.fr/00_dev_sam/espace-personnel.'.$PAGE_COURANTE.'.'.$tab_item["no"].'.generalites.html"><input type="button" value="Modifier" class="ico editer couleur" style="float:right;margin-right:0em;" /></a>';
		}*/
		//AJOUT FAVORIS, COUP DE COEUR
		//$contenu .= '<div class="ajout_coupdecoeur infobulle[coup de coeur|bas]">'.((!empty($tab_item["nb_aime"]))?'<div class="nb_aime">'.$tab_item["nb_aime"].'</div>':'').'</div>';
		//$contenu .= '<div class="ajout_favoris infobulle[favori|bas]"></div>';
	}
	else{
		/*$contenu .= '<p>Ceci est un apperçu de la fiche</p>';
		$contenu .= '<a onclick="window.close()"><input type="button" value="Fermer" class="ico fleche_gauche couleur" style="margin-left:0em;" /></a>';
		if($_SESSION["droits"]["no"]>0||$PAGE_COURANTE=='agenda'||$PAGE_COURANTE=='structure'||$tab_item["no_utilisateur"]==$_SESSION["utilisateur"]["no"]){
			$contenu .= '<a href="'.$root_site.'espace-personnel.'.$PAGE_COURANTE.'.'.$tab_item["no"].'.generalites.html"><input type="button" value="Modifier" class="ico editer couleur" style="float:right;margin-right:0em;" /></a>';
		}*/
		null;
	}
$contenu .= '</div>';
$contenu .= '<div class="fiche">';
	//1. TITRE
	if($PAGE_COURANTE!="forum"||!$tab_item["est_citoyen"]){
		$contenu .= "<h1>".$tab_item["titre"]."</h1>";
		//2. J'aime, favoris et sous titre
		if(!empty($tab_item["sous_titre"]))
			$contenu .= '<h2>'.$tab_item["sous_titre"].'</h2>';
	}
	else{
		$contenu .= '<div class="bandeau_citoyen">';
			$contenu .= "<h1>".$tab_item["titre"]."</h1>";
			//2. SOUS TITRE
			if(!empty($tab_item["sous_titre"]))
				$contenu .= '<h2>'.$tab_item["sous_titre"].'</h2>';
			$contenu .= '<p>Vie locale, informations à partager, actions collectives, idées...<br />Un espace neutre et indépendant pour échanger en liberté !<br /><br />Cet espace de libre expression est ouvert à tous, dans le respect de quelques règles élémentaires:<br /><br /><ol><li>Tout propos puni par la loi, discriminatoire ou diffamatoire sera supprimé.</li><li>Les publicités commerciales sont interdites.</li><li>La courtoisie est de rigueur : toute critique doit se faire dans le respect de la personne.</li><li>Tout acte de modération sera justifié par un message privé envoyé à l’auteur.</li></ol></p>';
		$contenu .= '</div>';
	}
	//3. MENTION PARTICULIÈRE, J'AIME ET FAVORIS
	$contenu .= '<h4>';
		if(!$PREVISUALISATION){
			$contenu .= '<div class="ajout_coupdecoeur'.(($EST_COUPDECOEUR)?' actif':'').' infobulle[coup de coeur|bas]" onclick="favori_coupdecoeur(this,'.$NO.',\''.$PAGE_COURANTE.'\')">'.((!empty($tab_item["nb_aime"]))?'<div>'.$tab_item["nb_aime"].'</div>':'').'</div>';
			$contenu .= '<div class="ajout_favoris infobulle[favori|bas]'.(($EST_FAVORI)?' actif':'').'" onclick="favori_coupdecoeur(this,'.$NO.',\''.$PAGE_COURANTE.'\')"></div>';
			$contenu .= '<div id="cloche_notification" class="ajout_notification'.(($NOTIFICATION_ACTIVE)?' actif':'').' infobulle[recevoir un courriel lorsqu\'un message est ajouté|bas]" onclick="favori_coupdecoeur(this,'.$NO.',\''.$PAGE_COURANTE.'\')"></div>';
		}
		if($PAGE_COURANTE=="editorial")
			$contenu .= 'le '.$tab_item["date_mise_en_ligne"].' par '.$tab_item["pseudo"];
			//$contenu .= 'le '.$tab_item["date_creation"].' par '.$tab_item["pseudo"];
		else if($PAGE_COURANTE=="agenda")
			$contenu .= $tab_item["date_du_au_precise"];
	$contenu .= '</h4>';
	//On ouvre la div permettant de remettre la fiche en dessous du bouton j'aime, favoris, etc;
	$contenu .= '<div style="clear:both;">';
		//4. IMAGE
		if($tab_item["image"]!=""&&$tab_item["image"]!=$root_site){
			$contenu .= '<div class="illustration_fichiers">';
				$contenu .= '<div class="image '.$FORMAT_IMAGE.'" id="zone_fichiers">';
					$contenu .= '<img src="'.$tab_item["image"].'" onclick="afficher_image(this);" />';
					if(!empty($tab_item["copyright"])){
						$contenu .= '<div class="copyright">';
							$contenu .= '©&nbsp;'.$tab_item["copyright"];
						$contenu .= '</div>';
					}
				
							if($PAGE_COURANTE=="editorial"){
								$contenu .= '<div style="position:absolute;bottom:0px;left:0px;width:100%;">';
								$nb_fichiers_audio = count($tab_item["fichiers_audio"]);
								if($nb_fichiers_audio>0){
									$url_premier_fichier_audio = $tab_item["fichiers_audio"][0]["url"];
									$contenu .= '<div class="bouton_media media_audio" id="bouton_audio" onmouseover="ouvrir_liste_fichiers(\'liste_audio\')"><img src="'.$root_site.'img/ico_file_audio.png" />'.$nb_fichiers_audio.' fichier'.(($nb_fichiers_audio==1)?'':'s').' audio</div>';
									$contenu .= '<div class="liste_media" id="liste_audio" onmouseout="fermer_liste_fichiers(\'liste_audio\')">';
									for($indice_audio=0;$indice_audio<$nb_fichiers_audio;$indice_audio++){
										$contenu .= '<div class="ligne_media" onclick="lancer_fichier_audio(\'zone_fichiers\',\''.$tab_item["fichiers_audio"][$indice_audio]["url"].'\')">'.$tab_item["fichiers_audio"][$indice_audio]["titre"].'<input type="hidden" value="'.$tab_item["fichiers_audio"][$indice_audio]["url"].'" /></div>'; // onmouseover="ouvrir_menu('principal');"
									}
									$contenu .= '</div>';
								}
								else
									$url_premier_fichier_audio = false;
								/*<iframe width="100%" height="166" scrolling="no" frameborder="no" 
								src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/161780438&amp;color=0066cc&amp;auto_play=true&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
									for($indice_fichier=0;$indice_fichier<count($tab_item["fichiers_audio"]);$indice_fichier++){
										$contenu .= '<div class="bouton_media">'.$tab_item["fichiers_audio"][$indice_fichier]["titre"].'</div>'; // onmouseover="ouvrir_menu('principal');"
									}*/
								$contenu .= '</div>';
							}
						
				$contenu .= '</div>';
				if(!empty($tab_item["legende"])){
					$contenu .= '<div class="legende">';
						$contenu .= $tab_item["legende"];
					$contenu .= '</div>';
				}
			
			$contenu .= '</div>';
		}
		if($PAGE_COURANTE=="petite-annonce"&&$tab_item["monetaire"])
			$contenu .= '<div class="ligne_info_fiche"><b>Prix :</b> '.$tab_item["prix"].' €</div>';
		//5. SITE
		if(!empty($tab_item["site"]))
			$contenu .= '<div class="ligne_info_fiche"><a href="'.$tab_item["site"].'">'.$tab_item["site"].'</a></div>';
		//6. TÉLÉPHONE
		if(!empty($tab_item["telephone"]))
			$contenu .= '<div class="ligne_info_fiche"><b>Tél. :</b> '.$tab_item["telephone"].((!empty($tab_item["telephone2"]))?' ou '.$tab_item["telephone2"]:'').'</div>';
		else if(!empty($tab_item["telephone2"]))
			$contenu .= '<div class="ligne_info_fiche"><b>Tél. :</b> '.$tab_item["telephone2"].'</div>';
		//7. CONTACT EMAIL, etc.
		$requete_contact = "SELECT contact.no, contact.nom FROM contact JOIN ".$table."_contact ON ".$table."_contact.no_contact=contact.no WHERE ".$table."_contact.no_".$table."=:no ORDER BY contact.nom";
		$tab_contact = execute_requete($requete_contact,array(":no"=>$NO));
		if(!empty($tab_contact)){
			$requete_verif_que_telephone = "SELECT IF(GROUP_CONCAT(contact_contactType.no_contactType)='1',1,0) AS qu_un_telephone FROM contact_contactType WHERE no_contact=:no";
			$contenu .= '<div class="ligne_info_fiche">';
				//On récupère les contacts.
				$contenu .= '<b>Contact'.((count($tab_contact)>1)?'s':'').' :</b> ';
				$contenu_contact = '';
				//On affiche leur nom avec le lien vers leur fenêtre de contact.
				for($i_contact=0;$i_contact<count($tab_contact);$i_contact++){
					if(!empty($contenu_contact)) $contenu_contact .= ', ';
					$tab_verif_que_telephone = execute_requete($requete_verif_que_telephone,array(":no"=>$tab_contact[$i_contact]["no"]));
					$qu_un_telephone = (bool)$tab_verif_que_telephone[0]["qu_un_telephone"];
					$contenu_contact .= '<span class="'.((!$qu_un_telephone)?'span_courriel':'span_telephone').'" onclick="fenetre_contact('.$tab_contact[$i_contact]["no"].');">'.$tab_contact[$i_contact]["nom"].'</span>';
				}
				$contenu .= $contenu_contact;
			$contenu .= '</div>';
		}
		//7. PERSONNE CONTACT
		if(!empty($tab_item["personne_contact"]))
			$contenu .= '<div class="ligne_info_fiche"><b>Personne à contacter :</b>'.$tab_item["personne_contact"].'</div>';
		//7. LIEU
		if(!empty($tab_item["nom_adresse"]))
			$contenu .= '<div class="ligne_info_fiche"><b>Lieu :</b> '.$tab_item["nom_adresse"].'</div>';
		//8. ADRESSE
		if($PAGE_COURANTE!="editorial"&&$PAGE_COURANTE!="forum"){
			if(!empty($tab_item["adresse"]))
				$contenu .= '<div class="ligne_info_fiche"><b>Adresse :</b> '.$tab_item["adresse"].'<br />'.$tab_item["cp"].' - '.$tab_item["ville"].'</div>';
			else
				$contenu .= '<div class="ligne_info_fiche"><b>Adresse :</b> '.$tab_item["cp"].' - '.$tab_item["ville"].'</div>';
		}
	/*
	if($PAGE_COURANTE=="agenda"){
		$contenu .= $tab_item["coordonnees"];
	}*/
		//CHAPO
		if(!empty($tab_item["chapo"]))
			$contenu .= '<div class="chapo">'.$tab_item["chapo"].'</div>';
		//DESCRIPTION
		$contenu .= '<div class="description">'.$tab_item["description"].'</div>';
		//DESCRIPTION COMPLEMENTAIRE OU NOTES
		if(!empty($tab_item["notes"]))
			$contenu .= '<div class="notes">'.$tab_item["notes"].'</div>';
		else if(!empty($tab_item["description_complementaire"]))
			$contenu .= '<div class="description_complementaire">'.$tab_item["description_complementaire"].'</div>';
	
	$contenu .= '</div>'; //On ferme la div clear:both
	
$contenu .= '</div>'; //On ferme la fiche

//On récupère la liste des tags
$les_tags = get_ficheTags($PAGE_COURANTE,$NO);
if(count($les_tags)>0){
	$contenu .= '<div class="les_tags_fiche"><h4>Thématique'.((count($les_tags)>1)?'s':'').' : </h4>';
	for($t=0;$t<count($les_tags);$t++){
		$contenu .= '<a href="'.$NOM_VILLE_URL.'.'.$ID_VILLE.'.'.$PAGE_COURANTE.'.tag'.$les_tags[$t]["no"].'.tous.html" class="un_tag">'.$les_tags[$t]["titre"].'</a>';
	}
	$contenu .= '</div>';
}


if(!$PREVISUALISATION){
	//Le bloc actions
	$contenu .= '<div id="bloc_actions">';
		//Modifier (pour événement, structure)
		if($_SESSION["droit"]["no"]>0||$PAGE_COURANTE=='agenda'||$PAGE_COURANTE=='structure'||$tab_item["no_utilisateur"]==$_SESSION["utilisateur"]["no"]){
			$contenu .= '<a href="'.$root_site.'espace-personnel.'.$PAGE_COURANTE.'.'.$tab_item["no"].'.generalites.html"><input type="button" value="Modifier" class="ico editer couleur" /></a>';
		}
		//TODO J'aime facebook
		
		//TODO bouton commentaire
	$contenu .= '</div>';
}
else{
	/*
	$contenu .= '<div>';
	$contenu .= '<p>Ceci est un apperçu de la fiche</p>';
	$contenu .= '<a onclick="window.close()"><input type="button" value="Fermer" class="ico fleche_gauche couleur" style="margin-left:0em;" /></a>';
	if($PAGE_COURANTE=='agenda'||$PAGE_COURANTE=='structure'){
		$contenu .= '<a href="'.$root_site.'espace-personnel.'.$PAGE_COURANTE.'.'.$tab_item["no"].'.generalites.html"><input type="button" value="Modifier" class="ico editer couleur" style="float:right;margin-right:0em;" /></a>';
	}
	$contenu .= '</div>';*/
	null;
}
?>
