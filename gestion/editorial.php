<?php
if(!empty($NO)){
	$menu .= '<div class="option_modification">';
		$menu .= '<input type="button" value="Enregistrer" class="enregistrer" onclick="enregistrer()" />';
		$menu .= '<input type="button" value="Voir" class="voirFiche" />';
		$menu .= '<input type="button" value="Créer une copie" class="copier" />';
		if($_SESSION["droit"]["no"]==1)
			$menu .= '<input type="button" value="Supprimer" class="suppression" />';
	$menu .= '</div>';
	$menu .= '<div class="option_modification">';
		$menu .= '<a href="#generalites" class="item_sous_menu">Généralités</a>';
		$menu .= '<a href="#tags" class="item_sous_menu">Tags</a>';
		$menu .= '<a href="#lieu" class="item_sous_menu">Lieu</a>';
		//$menu .= '<a href="#contact" class="item_sous_menu">Contact</a>';
		$menu .= '<a href="#description" class="item_sous_menu">Déscription</a>';
		$menu .= '<a href="#illustration" class="item_sous_menu">Illustration</a>';
		$menu .= '<a href="#liaisons" class="item_sous_menu">Liaisons</a>';
	$menu .= '</div>';
	
	if($NO!=-1){
		$tab = extraire_fiche("editorial",$NO);
		if(count($tab)>0){
			//Infos générales
			$titre = $tab["titre"];
			$sous_titre = $tab["sous_titre"];
			$date_creation = $tab["date_creation"];
			$date_modification = $tab["date_modification"];
			$no_utilisateur = $tab["no_utilisateur"];
			if(empty($no_utilisateur))
				$nom_utilisateur = "importation depuis Drôme Provence Baronnie";
			else
				$nom_utilisateur = ((!empty($tab["pseudo"]))?$tab["pseudo"].' ('.$tab["email_utilisateur"].')':$tab["email_utilisateur"]);
			$validation = $tab["validation"];
			$actif = (bool)$tab["etat"];
			$nb_aime = $tab["nb_aime"];
			//Lieu
			$ville = $tab["ville"];
			$cp = $tab["cp"];
			$no_ville = $tab["no_ville"];
			//Description
			$description = $tab["description"];
			$chapo = $tab["chapo"];
			$notes = $tab["notes"];
			//Illustration
			$url_image = $tab["image"];
			
			//On appelle maintenant extraire_fiche
			$contenu = '<div class="bloc entete">';
				$contenu .= '<div>';
					$contenu .= '<div class="infos">';
						$contenu .= 'Numéro : '.$NO;
						$contenu .= '<br/>';
						$contenu .= 'Créé le '.$date_creation.' par '.$nom_utilisateur;
					$contenu .= '</div>';
					$contenu .= '<div class="etat_validation'.(($actif)?' valide':'').'">';
						$contenu .= '&Eacute;tat: '.(($actif)?'Actif <span class="lien">Désactiver</span>':'Non actif <span class="lien">Activer</span>');
						$contenu .= '<br/>';
						$contenu .= (($validation==1)?'Validé':((($validation==2)?'modifié, non validé':'Non validé').' <span class="lien">Valider</span>'));
						$contenu .= '<div>'.(($validation==2)?'<input type="button" value="afficher les versions" />':'').'</div>';
					$contenu .= '</div>';
					//$contenu .= 'Dernière modifications apportées le '.$date_modification;
				$contenu .= '</div>';
			$contenu .= '</div>';
		}
		else{
			$NO = -1;
			$contenu = '<div class="bloc entete">';
				$contenu .= '<div>';
					$contenu .= '<div class="attention">';
						$contenu .= 'La fiche demandé n\'existe pas<br />Vous pouvez <a href="?type=evenement&no=">Revenir à la liste des événements</a> ou en enregistrer un nouveau en complétant les formulaires ci-dessous.';
					$contenu .= '</div>';
				$contenu .= '</div>';
			$contenu .= '</div>';
		}
	}
	else{
		$contenu = '<div class="bloc entete">';
			$contenu .= '<div>';
				$contenu .= '<div class="infos">';
					$contenu .= 'Remplissez les formulaire ci-dessous, n\'oubliez pas :<ul><li>de renseigner les tags si vous souhaitez que votre article soit correctement référencé</li><li>De mettre un visuel si vous souhaitez qu\'il apparaisse en page d\'accueil.</li></ul><br />L\'étoile "*" signifie que le champ est obligatoire.';
				$contenu .= '</div>';
			$contenu .= '</div>';
		$contenu .= '</div>';
	}
	
	$contenu .= '<div class="bloc" id="generalites">';
		$contenu .= '<div>';
			$contenu .= '<h1>Généralités</h1>';
		
			$contenu .= '<table>';
				$contenu .= '<tr>';
					$contenu .= '<td class="entete">Titre : </td>';
					$contenu .= '<td><input type="hidden" name="BDDtype" id="BDDtype" value="editorial" /><input type="hidden" name="BDDno" id="BDDno" value="'.$NO.'" /><input type="text" name="BDDtitre" id="BDDtitre" title="titre" value="'.$titre.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td>Sous-titre : </td>';
					$contenu .= '<td><input type="text" name="BDDsous_titre" id="BDDsous_titre" title="sous-titre" value="'.$sous_titre.'" class="grand_input" /></td>';
				$contenu .= '</tr>';
				$contenu .= '<tr>';
					$contenu .= '<td>Nombre de like : </td>';
					$contenu .= '<td><input type="text" name="BDDnb_like" id="BDDnb_like" title="0" value="'.$nb_aime.'" size="3" /></td>';
				$contenu .= '</tr>';
			$contenu .= '</table>';
		
		$contenu .= '</div>';
	$contenu .= '</div>';
	
	
	$contenu .= '<div class="bloc" id="tags">';
		$contenu .= '<div>';
			$contenu .= '<h1>Tags</h1>';
			$contenu .= '<div><input type="button" value="Ajouter" /></div>';
		$contenu .= '</div>';
	$contenu .= '</div>';
	
	$contenu .= '<div class="bloc" id="lieu">';
		$contenu .= '<div>';
			$contenu .= '<h1>Lieu</h1>';
			
			$contenu .= '<table>';
				$contenu .= '<tr>';
					$contenu .= '<td>Ville : </td>';
					$contenu .= '<td><input type="text" name="ville" id="ville" title="ville" value="'.((!empty($ville))?($ville.' ('.$cp.')'):'').'" size="30" class="recherche_ville" /><input type="hidden" name="BDDno_ville" id="BDDno_ville" value="'.$no_ville.'" /><div id="recherche_ville_liste"><div></div></div></td>';
				$contenu .= '</tr>';
			$contenu .= '</table>';
			
		$contenu .= '</div>';
	$contenu .= '</div>';
	/*
	$contenu .= '<div class="bloc" id="contact">';
		$contenu .= '<div>';
			$contenu .= '<h1>Contact</h1>';
			
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
			
		$contenu .= '</div>';
	$contenu .= '</div>';*/

	$contenu .= '<div class="bloc" id="description">';
		$contenu .= '<div>';
			$contenu .= '<h1>Description</h1>';
			
				$contenu .= '<h2>Chapô</h2>';
				$contenu .= '<textarea id="BDDchapo" name="BDDchapo" class="editeur">'.$chapo.'</textarea>';
				
				$contenu .= '<h2>Description</h2>';
				$contenu .= '<textarea id="BDDdescription" name="BDDdescription" class="editeur">'.$description.'</textarea>';
				
				$contenu .= '<h2>Notes</h2>';
				$contenu .= '<textarea id="BDDnotes" name="BDDnotes" class="editeur">'.$notes.'</textarea>';
				
		$contenu .= '</div>';
	$contenu .= '</div>';
	
	
	$contenu .= '<div class="bloc" id="illustration">';
		$contenu .= '<div>';
			$contenu .= '<h1>Illustration</h1>';
			
				$contenu .= '<div style="text-align: center;">';
					$contenu .= '<input type="file" id="BDDurl_image" name="BDDurl_image" class="fichier poids[10|500] type[image] url['.(($url_image!=false)?str_replace("http://www.ensembleici.fr/","",$url_image):'').']" />';
					$contenu .= '<br /><input type="text" value="'.$copyright.'" title="copyright" />';
				$contenu .= '</div>';
			
		$contenu .= '</div>';
	$contenu .= '</div>';
	
	
	$contenu .= '<div class="bloc" id="liaisons">';
		$contenu .= '<div>';
			$contenu .= '<h1>Liaisons</h1>';
			$contenu .= '<div><input type="button" value="Ajouter" /></div>';
		$contenu .= '</div>';
	$contenu .= '</div>';
}
else{
	//RECHERCHE
	$menu = '<div class="recherche">';
		$menu .= '<input type="text" value="Rechercher" title="Rechercher" class="recherche vide" /><input type="button" class="recherche" />';
	$menu .= '</div>';
	//RESULTAT DE RECHERCHE
	$menu .= '<div id="zone_recherche" class="vide">';
	$menu .= '</div>';
	//FILTRES
	$menu .= '<div id="les_filtres">';
		/*if($EXPIRE>0){
			$menu .= '<div class="filtre_recherche actif">';
				$menu .= '<div class="libelle">';
					$menu .= '<a href="?page='.$PAGE.'&no_ville='.$VILLE.'"><img src="../img/img_colorize.php?uri=non_actif.png&c=255,255,255" /></a>';
					$menu .= '&Eacute;v&eacute;nements expir&eacute;s';
				$menu .= '</div>';
			$menu .= '</div>';
		}*/
		if(!empty($VILLE)||!empty($NOM_UTILISATEUR)||!empty($CP)){
			$menu .= '<div class="filtre_recherche actif" id="filtre_cpVilleUtilisateur">';
				$menu .= '<div class="libelle">';
					$menu .= '<a href="?no_ville=&user=&cp="><img src="../img/img_colorize.php?uri=non_actif.png&c=255,255,255" /></a>';
					$menu .= ((!empty($VILLE))?$LIBELLE_VILLE:((!empty($NOM_UTILISATEUR))?$NOM_UTILISATEUR:$CP));
				$menu .= '</div>';
			$menu .= '</div>';
		}/*
		if($EXPIRE==0)
			$menu .= '<div class="lien_filtre"><a href="?page='.$PAGE.'&no_ville='.$VILLE.'&expire=1">Afficher aussi les &eacute;v&eacute;nements expir&eacute;s</a></div>';
	*/
	$menu .= '</div>';
	//BOUTON DE SUPPRESSIONS ET CRÉATIONS
	$menu .= '<div class="nouveau_suppression">';
		$menu .= '<div class="nouveau"><a href="?no=-1"><input type="button" value="Nouvel article" class="nouveau" /></a></div>';
		$menu .= '<div class="suppression"><input type="button" value="Mode suppression" class="suppression" onclick="mode_suppression(true);" /></div>';
	$menu .= '</div>';
	$menu .= '<div id="legende">';
		$menu .= "<div>legende&nbsp;:</div>";
		$menu .= '<table>';
			$menu .= '<tr><td></td><td>Non validé</td></tr>';
			$menu .= '<tr><td></td><td>Modifié, non validé</td></tr>';
			$menu .= '<tr><td></td><td>Validé</td></tr>';
			$menu .= '<tr><td></td><td>Importé, non validé</td></tr>';
			$menu .= '<tr><td></td><td>Événement expriré</td></tr>';
			$menu .= '<tr class="non_actif"><td colspan="2">Événement non actif (expiré ou désactivé)</td></tr>';
		$menu .= '</table>';
	$menu .= '</div>';
	$menu .= '<div id="filtre_suppression"><div>Séléctionnez les articles à supprimer, puis cliquez sur le bouton ci-dessous :<br /><input type="button" value="Supprimer" class="suppression" /><br />ou<br /><input type="button" value="annuler" onclick="mode_suppression(false);" /></div></div>';
	
	
	$parametres = array("admin"=>true);
	if(!empty($UTILISATEUR))
		$parametres["utilisateur"] = $UTILISATEUR;
	else if(!empty($VILLE))
		$parametres["ville"] = $VILLE;
	else if(!empty($CP))
		$parametres["cp"] = $CP;
	
	if(empty($TRI))
		$TRI = "date_creation";
	$parametres["tri"] = $TRI;
		
	if(!empty($ORDRE))
		$parametres["ordre"] = $ORDRE;
	
	$tab_item = extraire_liste("editorial",30,1,$parametres);
		$nb_item_ville = $tab_item["count_ville"];
		$nb_item_proche = $tab_item["count_proche"];
		$nb_item_total = $tab_item["count_total"];
		$nb_page = $tab_item["count_page"];
		$tab_item = $tab_item["liste"];
	
	$contenu = '<div class="bloc"><div>';
		$contenu .= '<table>';
			$contenu .= '<tr class="titre">';
				$contenu .= '<td></td>';
				$contenu .= '<td></td>';
				$contenu .= '<td><a href="?tri=titre&ordre='.((empty($ORDRE)&&$TRI=="titre")?'DESC':'').'"'.(($TRI=="titre")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Titre de l\'article</a></td>';
				$contenu .= '<td><a href="?tri=ville&ordre='.((empty($ORDRE)&&$TRI=="ville")?'DESC':'').'"'.(($TRI=="ville")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Ville</a></td>';
				$contenu .= '<td><a href="?tri=pseudo&ordre='.((empty($ORDRE)&&$TRI=="pseudo")?'DESC':'').'"'.(($TRI=="pseudo")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Auteur</a></td>';
				$contenu .= '<td><a href="?tri=date_creation&ordre='.((empty($ORDRE)&&$TRI=="date_creation")?'DESC':'').'"'.(($TRI=="date_creation")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>Date de cr&eacute;ation</a></td>';
				$contenu .= '<td><a href="?tri=popularite&ordre='.((empty($ORDRE)&&$TRI=="popularite")?'DESC':'').'"'.(($TRI=="popularite")?(' class="tri_courant'.((!empty($ORDRE))?' desc"':'"')):'').'>"j\'aime"</a></td>';
				$contenu .= '<td class="action"></td>';
			$contenu .= '</tr>';
		for($i=0;$i<count($tab_item);$i++){
			/*if($i==4)
				$tab_item[$i]["actif"] = false;
			else
				$tab_item[$i]["actif"] = true;*/
			if(!(bool)$tab_item[$i]["etat"]||$tab_item[$i]["expire"])
				$ligne_active = false;
			else
				$ligne_active = true;
			$contenu .= '<tr class="'.(($i%2!=0)?"impaire":"paire").(($ligne_active)?"":" non_actif").'" onclick="selectionner(this);">';
				$contenu .= '<td><input type="button" class="voir" value="" onclick="clickSurBouton();" /></td>';
				$contenu .= '<td></td>';
				$contenu .= '<td><a href="?no='.$tab_item[$i]["no"].'">'.$tab_item[$i]["titre"].'</a></td>';
				$contenu .= '<td><a href="?page='.$PAGE.'&no_ville='.$tab_item[$i]["no_ville"].'">'.$tab_item[$i]["ville"].'</a></td>';
				$contenu .= '<td>'.$tab_item[$i]["pseudo"].'</td>';
				$contenu .= '<td>'.$tab_item[$i]["date_creation"].'</td>';
				$contenu .= '<td>'.$tab_item[$i]["nb_like"].'</td>';
				$contenu .= '<td class="action"><input type="button" class="activer'.(($tab_item[$i]["etat"])?" actif":"").'" value="" onclick="clickSurBouton();" /><input type="button" class="editer" value="" onclick="clickSurBouton();" /><input type="button" class="etiquette_suppression" value="" /></td>';
			$contenu .= '</tr>';
		}
		
		$contenu .= '</table>';
	$contenu .= '</div></div>';
}
$page = array("menu"=>$menu,"contenu"=>$contenu);
?>
