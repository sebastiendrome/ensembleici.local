<?php
if($PAGE_COURANTE=="editorial")
	$table = "editorial";
else if($PAGE_COURANTE=="agenda"||$PAGE_COURANTE=="evenement")
	$table = "evenement";
else if($PAGE_COURANTE=="structure")
	$table = "structure";
else if($PAGE_COURANTE=="petiteannonce"||$PAGE_COURANTE=="petite-annonce")
	$table = "petiteannonce";
else
	$table = "forum";
//On regarde si l'utilisateur aime le contenu.
if(est_connecte())
	$NO_UTILISATEUR = $_SESSION["utilisateur"]["no"];
else
	$NO_UTILISATEUR = 0;
	
function extraire_message_fiche($type,$no){
	if($type=="editorial")
		$table = "editorial";
	else if($type=="agenda"||$type=="evenement")
		$table = "evenement";
	else if($type=="structure")
		$table = "structure";
	else if($type=="petiteannonce"||$type=="petite-annonce")
		$table = "petiteannonce";
	else
		$table = "forum";
	$requete_message = "SELECT message.*, utilisateur.pseudo AS utilisateur, utilisateur.no AS no_utilisateur, IFNULL(utilisateur.no_contact,0) AS no_contact FROM message JOIN utilisateur ON message.no_utilisateur_creation=utilisateur.no WHERE message.no_".$table."=:no AND message.afficher=1 ORDER BY message.date_creation DESC";
	$param_message = array(":no"=>$no);
	return execute_requete($requete_message,$param_message);
}
function extraire_commentaire_message($no_message){
	$requete_message = "SELECT message.*, utilisateur.pseudo AS utilisateur, utilisateur.no AS no_utilisateur, IFNULL(utilisateur.no_contact,0) AS no_contact FROM message JOIN utilisateur ON message.no_utilisateur_creation=utilisateur.no WHERE message.no_message=:no AND message.afficher=1 ORDER BY message.date_creation ASC";
	$param_message = array(":no"=>$no_message);
	return execute_requete($requete_message,$param_message);
}
/****
1. FICHE
***/
if(!empty($NO)){
	//On regarde si l'utilisateur a eu un coup de coeur pour la fiche
	$requete_coupdecoeur = "SELECT ".$table."_coupdecoeur.no FROM ".$table."_coupdecoeur WHERE ".$table."_coupdecoeur.no_".$table."=:no AND ".$table."_coupdecoeur.no_utilisateur=:no_utilisateur AND ".$table."_coupdecoeur.IP=:ip";
	$tab_coupdecoeur = execute_requete($requete_coupdecoeur,array(":no"=>$NO,":no_utilisateur"=>$NO_UTILISATEUR,":ip"=>$_SERVER["REMOTE_ADDR"]));
	$EST_COUPDECOEUR = (!empty($tab_coupdecoeur)&&!empty($tab_coupdecoeur[0]["no"]));
	//On regarde si l'utilisateur a mis le contenu en favoris
	if(est_connecte()){
		$requete_favoris = "SELECT * FROM ".$table."_favoris WHERE ".$table."_favoris.no_".$table."=:no AND ".$table."_favoris.no_utilisateur=:no_utilisateur";
		$tab_favoris = execute_requete($requete_favoris,array(":no"=>$NO,":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
		$EST_FAVORI = !empty($tab_favoris);
	}
	else
		$EST_FAVORI = false;
	//On regarde si l'utilisateur est abonné à la fiche
	$requete_notification = "SELECT ".$table."_notification.date_modification FROM ".$table."_notification WHERE ".$table."_notification.no_".$table."=:no AND ".$table."_notification.no_utilisateur=:no_utilisateur AND etat=1";
	$tab_notification = execute_requete($requete_notification,array(":no"=>$NO,":no_utilisateur"=>$NO_UTILISATEUR));
	$NOTIFICATION_ACTIVE = (!empty($tab_notification)&&!empty($tab_notification[0]["date_modification"]));

	//On inclue la fiche
	include "01_include/struct_fiche.php";
	//On récupère les messages
	$tab_message = extraire_message_fiche($PAGE_COURANTE,$NO);
	//On affiche les messages concernant la fiche
        $contenu .= '<div style="text-align:center; margin-top: 5px;">';
        $contenu .= '<a target="_blank" style="cursor:pointer;" id="article_partage_FB" data-title="'.$tab_item["titre"].'" title="Partagez sur Facebook" onclick="displayFB();return false;"><img src="http://www.ensembleici.fr/img/facebook_icon.png" alt="Facebook" /></a>&nbsp;&nbsp;';
        $contenu .= '<a target="_blank" style="cursor:pointer;" id="article_partage_TW" data-title="'.$tab_item["titre"].'" title="Partagez sur Twitter" onclick="displayTW();return false;"><img src="http://www.ensembleici.fr/img/twitter_icon.png" alt="Twitter" /></a>&nbsp;&nbsp;';
        $contenu .= '<a target="_blank" style="cursor:pointer;" id="article_partage_GP" data-title="'.$tab_item["titre"].'" title="Partagez sur Goole +" onclick="displayGP();return false;"><img src="http://www.ensembleici.fr/img/gplus_icon.png" alt="Google+" /></a>';
        $contenu .= '</div>';
	$contenu .= '<div id="exprimez_vous">';
        $contenu .= '<input type="button" data-page="'.$PAGE_COURANTE.'" data-id="'.$NO.'" id="signaler_article" value="Signaler comme inapproprié" class="ico couleur forum" onclick="signaler_article()" />';
        $contenu .= '<input type="button" value="'.(($PAGE_COURANTE!="forum")?'Commenter':'Ajouter un message').'" class="ico couleur forum" onclick="affiche_div_message(this)" />';
	$contenu .= '</div>';
	$contenu .= '<div class="zone_messages" id="zone_messages">';
		for($i=0;$i<count($tab_message);$i++){
			$contenu .= '<div class="un_message" id="message-'.$tab_message[$i]["no"].'">';
				$contenu .= '<div>';
			
					$contenu .= '<div class="un_message_header">';
				
						$contenu .= '<div class="un_message_date">';
							$contenu .= datefr($tab_message[$i]["date_creation"],true,false);
						$contenu .= '</div>';
					
						$contenu .= '<div class="un_message_utilisateur"'.((!empty($tab_message[$i]["no_contact"]))?' onclick="fenetre_contact('.$tab_message[$i]["no_contact"].')"':'').'>';
							$contenu .= $tab_message[$i]["utilisateur"];
						$contenu .= '</div>';
					
					$contenu .= '</div>';
					
					//CONTENU DU MESSAGE
					$contenu .= '<div';
					if($tab_message[$i]["no_utilisateur"]==$_SESSION["utilisateur"]["no"]||$_SESSION["droit"]["no"]==1)
						$contenu .= ' onclick="affiche_div_modification_message_commentaire(this);"';
					$contenu .= ' class="un_message_contenu';
					if($tab_message[$i]["no_utilisateur"]==$_SESSION["utilisateur"]["no"]||$_SESSION["droit"]["no"]==1)
						$contenu .= ' editable infobulle[Modifier ce message|haut]';
					$contenu .= '">';
						$contenu .= $tab_message[$i]["contenu"];
					$contenu .= '</div>';
				
					$contenu .= '<div class="un_message_commentaires">';
						
						$tab_sous_message = extraire_commentaire_message($tab_message[$i]["no"]);
						for($j=0;$j<count($tab_sous_message);$j++){
							$contenu .= '<div class="un_commentaire" id="commentaire-'.$tab_sous_message[$j]["no"].'">';
								$contenu .= '<div>';
								
									//CONTENU DU COMMENTAIRE
									$contenu .= '<div';
									if($tab_sous_message[$j]["no_utilisateur"]==$_SESSION["utilisateur"]["no"]||$_SESSION["droit"]["no"]==1)
										$contenu .= ' onclick="affiche_div_modification_message_commentaire(this);"';
									$contenu .= ' class="un_commentaire_contenu';
									if($tab_sous_message[$j]["no_utilisateur"]==$_SESSION["utilisateur"]["no"]||$_SESSION["droit"]["no"]==1)
										$contenu .= ' editable infobulle[Modifier ce commentaire|haut]';
									$contenu .= '">';
										$contenu .= $tab_sous_message[$j]["contenu"];
									$contenu .= '</div>';
								
									$contenu .= '<div class="un_commentaire_footer">';
										$contenu .= '<span class="un_commentaire_utilisateur"'.((!empty($tab_message[$i]["no_contact"]))?' onclick="fenetre_contact('.$tab_sous_message[$j]["no_contact"].')"':'').'>';
											$contenu .= $tab_sous_message[$j]["utilisateur"];
										$contenu .= '</span>';
										$contenu .= '<span class="un_commentaire_date">';
											$contenu .= datefr($tab_sous_message[$j]["date_creation"],true,false);
										$contenu .= '</span>';
									$contenu .= '</div>';
									
									if($tab_sous_message[$j]["no_utilisateur"]==$_SESSION["utilisateur"]["no"]||$_SESSION["droit"]["no"]==1)
										$contenu .= '<div class="supprimer infobulle[Supprimer|bas]" onclick="supprimer_message_commentaire(this.parentNode.parentNode,event);"></div>';
								
								$contenu .= '</div>';
							$contenu .= '</div>';
						}
						
						$contenu .= '<div class="lien_commentaire infobulle[Répondre à ce message|bas]" onclick="affiche_div_commentaire(this);">'.(($PAGE_COURANTE!="forum")?'répondre':'commenter').'</div>';
					
					$contenu .= '</div>';
					
					if($tab_message[$i]["no_utilisateur"]==$_SESSION["utilisateur"]["no"]||$_SESSION["droit"]["no"]==1)
						$contenu .= '<div class="supprimer infobulle[Supprimer|bas]" onclick="supprimer_message_commentaire(this.parentNode.parentNode,event);"></div>';
					
					$contenu .= '<div class="ancre_message" id="m-'.$tab_message[$i]["no"].'"></div>';
				
				$contenu .= '</div>';
			$contenu .= '</div>';
		}
	$contenu .= '</div>';
			/*$contenu .= '<div class="ancre_message" id="message'.$tab_message[$i]["no"].'"></div>';
			$contenu .= '<div class="un_message" id="message_'.$tab_message[$i]["no"].'_'.$tab_message[$i]["no_utilisateur"].'">';
				$contenu .= '<div class="information_message">';
					$contenu .= '<span class="pseudo">'.$tab_message[$i]["utilisateur"].'</span> le '.datefr($tab_message[$i]["date_modification"],true,false);
					if(est_connecte()){
						if($tab_message[$i]["no_utilisateur"]==$_SESSION["UserConnecte_id"]||est_admin()){ // || est_admin()
							$contenu .= '<div class="bouton_admin_proprietaire">';
								$contenu .= '<img id="edit_message_'.$tab_message[$i]["no"].'" onclick="ouvre_edit_message('.$tab_message[$i]["no"].',\'message\','.$tab_message[$i]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_edit.png&c=FFD500" onmouseover="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=e19f00\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=FFD500\';" />';
								$contenu .= '<img onclick="supprimer_message('.$tab_message[$i]["no"].',\'message\','.$tab_message[$i]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_delete.png&c=FEBDBD" onmouseover="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=241,63,65\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=FEBDBD\';" />';
							$contenu .= '</div>';
						}
					}
				$contenu .= '</div>';
				$contenu .= '<div class="contenu_message">'.$tab_message[$i]["contenu"].'</div>';
			$contenu .= '</div>';
			$contenu .= '<div class="commentaires" id="commentaire_'.$tab_message[$i]["no"].'">';
				$contenu .= '<span class="lien_commentaire" onclick="affiche_div_commentaire(this);">ajouter un commentaire</span>';
				$res_message->execute(array(":no"=>$NO,":nom"=>$tab_message[$i]["no"]));
				$tab_sous_message = $res_message->fetchAll();
				for($j=0;$j<count($tab_sous_message);$j++){
					$contenu .= '<div class="un_commentaire" id="unCommentaire_'.$tab_sous_message[$j]["no"].'_'.$tab_sous_message[$j]["no_utilisateur"].'">';
						$contenu .= '<div class="contenu_unCommentaire">';
							$contenu .= $tab_sous_message[$j]["contenu"];
						$contenu .= '</div>';
						$contenu .= '<div class="signature_commentaire"><a class="pseudo" href="profil.php?no='.$tab_sous_message[$j]["no_utilisateur"].'">'.$tab_sous_message[$j]["utilisateur"].'</a> le '.datefr($tab_message[$i]["date_modification"],true,false).'</div>';
						if(est_connecte()){
							if($tab_sous_message[$j]["no_utilisateur"]==$_SESSION["UserConnecte_id"]||est_admin()){ // || est_admin()
							$contenu .= '<div class="bouton_admin_proprietaire">';
								$contenu .= '<img id="edit_message_'.$tab_sous_message[$j]["no"].'" onclick="ouvre_edit_message('.$tab_sous_message[$j]["no"].',\'unCommentaire\','.$tab_sous_message[$j]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_edit.png&c=FFD500" onmouseover="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=e19f00\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_edit.png&c=FFD500\';" />';
								$contenu .= '<img onclick="supprimer_message('.$tab_sous_message[$j]["no"].',\'unCommentaire\','.$tab_sous_message[$j]["no_utilisateur"].')" src="img/img_colorize.php?uri=ico_delete.png&c=FEBDBD" onmouseover="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=241,63,65\';" onmouseout="this.src=\'img/img_colorize.php?uri=ico_delete.png&c=FEBDBD\';" />';
							$contenu .= '</div>';
							}
						}
					$contenu .= '</div>';
				}
			$contenu .=	'</div>';
			
		}
		$contenu .= '</div>';
	}
	*/
	
	
	/*$requete = "SELECT titre, contenu FROM contenu_blocs WHERE no=1 AND etat=1";
	$tab_editoEi = execute_requete($requete);
	if(count($tab_editoEi)>0){
		$CONTENU_EDITORIAL_ENSEMBLEICI = '<h3>'.$tab_editoEi[0]["titre"].'</h3>'.$tab_editoEi[0]["contenu"];
	}
	else{
		$CONTENU_EDITORIAL_ENSEMBLEICI = "";
	}
	$contenu_droite = $CONTENU_EDITORIAL_ENSEMBLEICI;*/
	$contenu_droite = contenu_colonne_droite("fiche");
}
/****
2. LISTE
***/
else{
	//On récupère les paramètres qui pourraient encore nous être utiles
	/*$NUMERO_PAGE = (!empty($_POST["np"]))?$_POST["np"]:((!empty($_GET["np"]))?$_GET["np"]:1);
	$DU = (!empty($_POST["du"]))?$_POST["du"]:((!empty($_GET["du"]))?$_GET["du"]:"");
	$DISTANCE = (!empty($_POST["dist"]))?$_POST["dist"]:((!empty($_GET["dist"]))?$_GET["dist"]:"");
		$DISTANCE = ($DISTANCE!="")?(($DISTANCE!="tous")?str_replace("km","",$DISTANCE):-1):0;
	$TRI = $_POST["tri"];*/
		
	$PARAMETRES = array();
	if($DISTANCE!=0){
		$PARAMETRES["distance"] = $DISTANCE;
	}
	if(!empty($TRI)){
		$PARAMETRES["tri"] = $TRI;
	}
	if(!empty($DU)){
		$PARAMETRES["du"] = $DU;
	}
	if(!empty($LISTE_TAGS)){
		$PARAMETRES["tags"] = $LISTE_TAGS;
	}
		
	$NB_LISTE = 10;
	
	//On prépare certains paramètres en fonction de $PAGE_COURANTE
	if($PAGE_COURANTE=="editorial"){
		include_once("01_include/_var_editorial.php");
//		$TITRE_PAGE = "&Eacuteditorial";
                $TITRE_PAGE = "Médias";
		$SOUS_TITRE_PAGE = "Retrouvez sur cette page le contenu Média d'ensemble ici.";
		$FORMAT_IMAGE = "4/3";
		$LIBELLE_ITEM = "article";
		$LIBELLE_ITEM_PLURIEL = "articles";
		$LIBELLE_AJOUTER = 'Rédiger un article';
	}
	else if($PAGE_COURANTE=="agenda"){
		include_once("01_include/_var_agenda.php");
		$TITRE_PAGE = "Agenda";
		$SOUS_TITRE_PAGE = "Retrouvez sur cette page tout l'agenda d'ensemble ici.";
		$FORMAT_IMAGE = "carre";
		$LIBELLE_ITEM = "&eacute;v&eacutenement";
		$LIBELLE_ITEM_PLURIEL = "&eacute;v&eacutenements";
		$LIBELLE_AJOUTER = 'Ajouter un événement';
	}
	else if($PAGE_COURANTE=="petite-annonce"){
		include_once("01_include/_var_petiteannonce.php");
		$TITRE_PAGE = "Petites annonces";
		$SOUS_TITRE_PAGE = "Retrouvez sur cette page toutes les annonces postées sur ensemble ici.";
		$FORMAT_IMAGE = "carre";
		$LIBELLE_ITEM = "petite annonce";
		$LIBELLE_ITEM_PLURIEL = "petites annonces";
		$LIBELLE_AJOUTER = 'Créer une petite annonce';
	}
	else if($PAGE_COURANTE=="structure"){
		include_once("01_include/_var_repertoire.php");
		$TITRE_PAGE = "R&eacute;pertoire";
		$SOUS_TITRE_PAGE = "Retrouvez sur cette page le r&eacute;pertoire d'ensemble ici.";
		$FORMAT_IMAGE = "carre";
		$LIBELLE_ITEM = "structure";
		$LIBELLE_ITEM_PLURIEL = "structures";
		$LIBELLE_AJOUTER = 'Ajouter une structure';
	}
	else if($PAGE_COURANTE=="forum"){
		include_once("01_include/_var_forum.php");
		$TITRE_PAGE = "Forum";
		$SOUS_TITRE_PAGE = "Retrouvez sur cette page les forums d'ensemble ici.";
		$FORMAT_IMAGE = "carre";
		$LIBELLE_ITEM = "sujet";
		$LIBELLE_ITEM_PLURIEL = "sujets";
		$LIBELLE_AJOUTER = 'Créer un sujet';
	}
	
	/****
	On récupère la liste demandée
	****/
	$tab_item = extraire_liste($PAGE_COURANTE,$NB_LISTE,$NUMERO_PAGE,$PARAMETRES);
        $nb_item_ville = $tab_item["count_ville"];
        $nb_item_proche = $tab_item["count_proche"];
        $nb_item_total = $tab_item["count_total"];
        $nb_page = $tab_item["count_page"];
        $tab_item = $tab_item["liste"];
	
	
	/****
	On affiche la liste récupérée
	****/
		/**
		Les titres
		**/
	$contenu = '<h1>'.$TITRE_PAGE.'</h1>';
	$contenu .= '<h2>'.$SOUS_TITRE_PAGE.'</h2>';
	
		/**
		Les boutons
		**/
	$contenu .= '<div>';
		//$contenu .= '<input type="button" value="Retour" class="ico fleche_gauche couleur" />';
		if($PAGE_COURANTE=="agenda"||$PAGE_COURANTE=="petite-annonce"||$PAGE_COURANTE=="structure"||$PAGE_COURANTE=="forum"){
			$contenu .= '<a href="'.$root_site.'espace-personnel.'.$PAGE_COURANTE.'.html"><input type="button" value="'.$LIBELLE_AJOUTER.'" class="ico plus couleur" style="margin-left:0em;" /></a>';
			if($PAGE_COURANTE=="agenda"&&est_connecte()&&$_SESSION["droit"]["no"]>0)
				$contenu .= '<a href="'.$root_site.'calendrier'.((empty($DU_URL))?'':'-'.$DU_URL).'.pdf"><input type="button" value="voir la version PDF" class="ico fleche couleur" style="float:right;margin-right:0em;" /></a>';
			//else if($PAGE_COURANTE=="petite-annonce")
			//	$contenu .= '<input type="button" value="Bricothèque Solidar\'Nyons" class="ico fleche couleur" style="float:right;margin-right:0em;" />';
		}
		else if($PAGE_COURANTE=="editorial"){
			if(est_connecte()&&$_SESSION["droit"]["no"]>0)
				$contenu .= '<a href="'.$root_site.'gestion/" target="_blank"><input type="button" value="'.$LIBELLE_AJOUTER.'" class="ico plus couleur" style="margin-left:0em;" /></a>';
			//else
			//	$contenu .= '<input type="button" value="Devenir éditeur" class="ico fleche couleur" style="margin-left:0em;" />';
		}
	$contenu .= '</div>';
	
		/**
		La zone de filtres
		**/
	$contenu .= '<div class="formulaire_filtre">';
			//1. Distance
			$contenu .= '<div class="afficher_pour">';
				$contenu .= '<h2>Afficher pour</h2>';
				$contenu .= '<div id="zone_filtre_distance">';
					$contenu .= '<input type="range" step="10" value="'.(($DISTANCE!=-1)?$DISTANCE:60).'" min="0" max="60" onchange="change_distance(this)" oninput="change_distance(this,true)" id="input_distance" />';
					$contenu .= '<div><div id="libelle_distance"><span>'.$NOM_VILLE.'</span><span>+</span><span>Partout</span></div></div>';
				$contenu .= '</div>';
			$contenu .= '</div>';
			if($PAGE_COURANTE=="agenda"){
				$contenu .= '<div class="a_partir_du">';
					$contenu .= '<h2>À partir du</h2>';
					$contenu .= '<input type="text" class="calendrier non_anterieur '.$PAGE_COURANTE.' charger_page" value="'.((!empty($DU))?datefr($DU):date("d/m/Y")).'" onchange="change_date(this)" />';
				$contenu .= '</div>';
			}
			$contenu .= '<h2>';
				$contenu .= 'Trier par&nbsp;:&nbsp;';
				
				$contenu .= '<div id="div_tri">';
					$url_page = $NOM_VILLE_URL.'.'.$ID_VILLE.'.'.$PAGE_COURANTE;
					if(!empty($LISTE_TAGS))
						$url_page .= ".tag".str_replace(",","-",$LISTE_TAGS);
					if($DISTANCE!=0)
						$url_page .= ".".(($DISTANCE>0)?$DISTANCE."km":"tous");
					if(!empty($DU))
						$url_page .= ".du-".$DU_URL;
					$url_page_date = $url_page.'.html';
					$url_page_distance = $url_page.'.distance.html';
					$url_page_reputation = $url_page.'.reputation.html';
					if($PAGE_COURANTE!="structure")	
						$contenu .= '<a id="tri_date"'.((empty($TRI)||$TRI=="date")?' class="actif"':'').' href="'.$url_page_date.'">Date</a>';
					$contenu .= '<a id="tri_distance"'.(($TRI=="distance")?' class="actif"':'').' href="'.$url_page_distance.'">Distance</a>';
//					if($PAGE_COURANTE!="petite-annonce")
//						$contenu .= '<a id="tri_reputation"'.(($TRI=="reputation")?' class="actif"':'').' href="'.$url_page_reputation.'">Réputation</a>';
				$contenu .= '</div>';
				/*$contenu .= '<div id="div_tri">';
					if($PAGE_COURANTE!="structure")	
						$contenu .= '<div id="tri_date" onclick="change_tri(this);"'.((empty($TRI)||$TRI=="date")?' class="actif"':'').'>Date</div>';
					$contenu .= '<div id="tri_distance" onclick="change_tri(this);"'.(($TRI=="distance")?' class="actif"':'').'>Distance</div>';
					if($PAGE_COURANTE!="petite-annonce")
						$contenu .= '<div id="tri_reputation" onclick="change_tri(this);"'.(($TRI=="reputation")?' class="actif"':'').'>Réputation</div>';
				$contenu .= '</div>';*/
			$contenu .= '</h2>';
	$contenu .= '</div>';
		/**
		La zone des pages
		**/
		$pagination = "haut";
		include "01_include/ecrire_pagination.php";
		/**
			La zone de compteurs
			**/
	$contenu .= '<div class="zone_compteurs">';
	$contenu .= '<div>'.$nb_item_ville.'</div><span>&nbsp;'.(($nb_item_ville>1)?$LIBELLE_ITEM_PLURIEL:$LIBELLE_ITEM).' à '.$NOM_VILLE.'</span>';
		if($nb_item_proche>0){
			$contenu .= '<br/><span><div>&nbsp;+&nbsp;'.$nb_item_proche.'</div>&nbsp;'.(($nb_item_proche>1)?$LIBELLE_ITEM_PLURIEL:$LIBELLE_ITEM).' proche'.(($nb_item_proche>1)?'s':'').'</span>';
		}
		if($DISTANCE==-1){
			$contenu .= '<br/><span><div>&nbsp;+&nbsp;'.$nb_item_total.'</div>&nbsp;'.(($nb_item_total>1)?$LIBELLE_ITEM_PLURIEL:$LIBELLE_ITEM).' au total</span>';
		}
		
	$contenu .= '</div>';
	
	for($i_item=0;$i_item<count($tab_item);$i_item++){
		$contenu .= '<div class="liste_ligne'.(($PAGE_COURANTE!="forum"||!$tab_item[$i_item]["est_citoyen"])?'':' citoyen').'">';
			$contenu .= '<div class="genre_ville noSmartphone">';
				$contenu .= '<span class="genre">'.$tab_item[$i_item]["genre"].'</span>';
				$contenu .= '<span class="ville">'.$tab_item[$i_item]["ville"].'</span>';
			$contenu .= '</div>';
			$contenu .= '<h3>';
				$contenu .= '<a href="'.$root_site.$PAGE_COURANTE.'.'.url_rewrite($tab_item[$i_item]["ville"]).'.'.url_rewrite($tab_item[$i_item]["titre"]).'.'.$tab_item[$i_item]["no_ville"].'.'.$tab_item[$i_item]["no"].'.html">';
					$contenu .= $tab_item[$i_item]["titre"];
					if($PAGE_COURANTE=="petite-annonce"&&$tab_item[$i_item]["monetaire"])
						$contenu .= '&nbsp;<img class="ico-monetaire infobulle" title="'.$tab_item[$i_item]["prix"].' €" src="img/monetaire.png" />';
				$contenu .= '</a>';
			$contenu .= '</h3>';
			if(!empty($tab_item[$i_item]["sous_titre"])){
				$contenu .= '<h4>';
					$contenu .= $tab_item[$i_item]["sous_titre"];
				$contenu .= '</h4>';
			}
			
			$contenu .= '<div class="genre_ville smartphone">';
				$contenu .= '<span class="genre">'.$tab_item[$i_item]["genre"].'</span>';
				$contenu .= '<span class="separateur_genre_ville"> - </span>';
				$contenu .= '<span class="ville">'.$tab_item[$i_item]["ville"].'</span>';
			$contenu .= '</div>';
			if(!empty($tab_item[$i_item]["image"])){
                            $tabphoto     = getimagesize(str_replace(' ', '%20', $tab_item[$i_item]["image"]));
                            $hauteur      = $tabphoto[1];
                            $largeur      = $tabphoto[0];
                            if ($FORMAT_IMAGE == 'carre') {
                                    if ($largeur > $hauteur) {
                                        $hauteur_res = ($hauteur * 80) / $largeur;
                                        $largeur_res = '80';
                                        $margin_left = "0";
                                        $margin_top = number_format((80 - $hauteur_res)/2,2);

                                    } else {
                                        $largeur_res = ($largeur * 80) / $hauteur;
                                        $hauteur_res = '80';
                                        $margin_top = "0";
                                        $margin_left = number_format((80 - $largeur_res)/2,2);
                                    }
                            }
                            else {
                                if (($largeur/160) > ($hauteur/119)) {
                                    $hauteur_res = ($hauteur * 160) / $largeur;
                                    $largeur_res = '160';
                                    $margin_left = "0";
                                    $margin_top = number_format((119 - $hauteur_res)/2,2);
                                    
                                } else {
                                    $largeur_res = ($largeur * 119) / $hauteur;
                                    $hauteur_res = '119';
                                    $margin_top = "0";
                                    $margin_left = number_format((160 - $largeur_res)/2,2);
                                }
                            }
                            $style = "style='height: ".number_format($hauteur_res,2)."px; width: ".number_format($largeur_res,2)."px; margin-top: ".$margin_top."px; margin-left: ".$margin_left."px'";
				$contenu .= '<a href="'.$root_site.$PAGE_COURANTE.'.'.url_rewrite($tab_item[$i_item]["ville"]).'.'.url_rewrite($tab_item[$i_item]["titre"]).'.'.$tab_item[$i_item]["no_ville"].'.'.$tab_item[$i_item]["no"].'.html">';
//					$contenu .= '<div class="image '.$FORMAT_IMAGE.' invisible">';
                                        if ($FORMAT_IMAGE == 'carre') {
                                            $contenu .= '<div class="'.$FORMAT_IMAGE.'" style="float:left; margin-right: 1em; margin-top: 0; width:80px;">';
                                                
                                        }
                                        else {
                                            $contenu .= '<div class="'.$FORMAT_IMAGE.'" style="float:left; margin-right: 1em; margin-top: 0; width:160px;">';
                                        }
						$contenu .= '<img src="'.$tab_item[$i_item]["image"].'" '.$style.' />';
						$contenu .= $tab_item[$i_item]["div_fichier"];
					$contenu .= '</div>';
				$contenu .= '</a>';
			}
				//$contenu .= '<div class="archiver noSmartphone infobulle" title="Favori">&nbsp;</div>'.(($PAGE_COURANTE!="petite-annonce")?'<div class="coupdecoeur noSmartphone infobulle" title="Coup de coeur">&nbsp;<div class="nb_aime'.(empty($tab_item[$i_item]["nb_aime"])?' zero':'').'">'.$tab_item[$i_item]["nb_aime"].'</div></div>':'');
			//Editorial
			if($PAGE_COURANTE=="editorial"){
				$contenu .= '<div class="source">';
					$contenu .= '<div><b>'.$tab_item[$i_item]["pseudo"].'</b>, le '.$tab_item[$i_item]["date_mise_en_ligne"].'</div>';
				$contenu .= '</div>';
			}
			//Agenda
			else if($PAGE_COURANTE=="agenda"){
				if($tab_item[$i_item]["date_debut"]!=$tab_item[$i_item]["date_fin"])
					$date = "du <b>".$tab_item[$i_item]["date_debut"]."</b> au <b>".$tab_item[$i_item]["date_fin"]."</b>";
				else
					$date = "le <b>".$tab_item[$i_item]["date_debut"]."</b>";
				$contenu .= '<div class="source">';
					$contenu .= '<div>'.$date.'</div>';
				$contenu .= '</div>';
			}
			//Petite annonce
			else if($PAGE_COURANTE=="petite-annonce"){
				$date = "valable jusqu'au <b>".$tab_item[$i_item]["date_fin"]."</b>";
				$contenu .= '<div class="source">';
					$contenu .= '<div>'.$date.'</div>';
				$contenu .= '</div>';
			}
			$contenu .= '<div class="description">';
				$requete_coupdecoeur = "SELECT ".$table."_coupdecoeur.no FROM ".$table."_coupdecoeur WHERE ".$table."_coupdecoeur.no_".$table."=:no AND ".$table."_coupdecoeur.no_utilisateur=:no_utilisateur AND ".$table."_coupdecoeur.IP=:ip";
				$tab_coupdecoeur = execute_requete($requete_coupdecoeur,array(":no"=>$tab_item[$i_item]["no"],":no_utilisateur"=>$NO_UTILISATEUR,":ip"=>$_SERVER["REMOTE_ADDR"]));
				$EST_COUPDECOEUR = (!empty($tab_coupdecoeur)&&!empty($tab_coupdecoeur[0]["no"]));
				//On regarde si l'utilisateur a mis le contenu en favoris
				if(est_connecte()){
					$requete_favoris = "SELECT * FROM ".$table."_favoris WHERE ".$table."_favoris.no_".$table."=:no AND ".$table."_favoris.no_utilisateur=:no_utilisateur";
					$tab_favoris = execute_requete($requete_favoris,array(":no"=>$tab_item[$i_item]["no"],":no_utilisateur"=>$_SESSION["utilisateur"]["no"]));
					$EST_FAVORI = !empty($tab_favoris);
				}
				else
					$EST_FAVORI = false;
				$contenu .= '<div class="ajout_coupdecoeur_et_favoris">';
					$contenu .= '<div class="ajout_coupdecoeur'.(($EST_COUPDECOEUR)?' actif':'').' infobulle[coup de coeur|bas]" onclick="favori_coupdecoeur(this,'.$tab_item[$i_item]["no"].',\''.$PAGE_COURANTE.'\')">'.((!empty($tab_item[$i_item]["nb_aime"]))?'<div>'.$tab_item[$i_item]["nb_aime"].'</div>':'').'</div>';
					if(est_connecte())
						$contenu .= '<div class="ajout_favoris infobulle[favori|bas]'.(($EST_FAVORI)?' actif':'').'" onclick="favori_coupdecoeur(this,'.$tab_item[$i_item]["no"].',\''.$PAGE_COURANTE.'\')"></div>';
				
				$contenu .= '</div>';
				$contenu .= $tab_item[$i_item]["descriptionsub"];
				
			$contenu .= '</div>';
			//$contenu .= '<div class="coupdecoeur smartphone">'.(($PAGE_COURANTE!="petite-annonce")?'<div>Coup de coeur<div class="nb_aime'.(empty($tab_item[$i_item]["nb_aime"])?' zero':'').'">'.$tab_item[$i_item]["nb_aime"].'</div></div>':'').'</div><div class="archiver smartphone"><div>Favoris</div></div>';
			
			
			
			$contenu .= '<div style="clear:both;"></div>';
		$contenu .= '</div>';
	}
	/**
		La zone des pages
		**/
	$pagination = "bas";
	include "01_include/ecrire_pagination.php";
	
	/**
		Colonne droite : les tags
		**/
	$contenu_droite = contenu_colonne_droite("liste");
	/*$les_vies = get_vies();
	//$LISTE_TAGS = "31,1355304057,17";
	$les_tags = get_tags($VIE,$LISTE_TAGS,"",true);
	if(!empty($LISTE_TAGS))
		$les_tags_select = get_tags_depuis_liste($LISTE_TAGS,true);
	else
		$les_tags_select = array();
	$contenu_droite = '<div id="boite_tag" class="vie-toute">';
		$contenu_droite .= '<div class="libelle">Thématiques</div>';
		$contenu_droite .= '<select onchange="set_vie(this);">';
			$contenu_droite .= '<option value="vie-toute_0">Toutes les vies</option>';
		for($i_vie=0;$i_vie<count($les_vies);$i_vie++){
			$contenu_droite .= '<option value="'.url_rewrite($les_vies[$i_vie]["libelle"]).'_'.$les_vies[$i_vie]["no"].'">'.$les_vies[$i_vie]["libelle"].'</option>';
		}
		$contenu_droite .= '</select>';
		$contenu_droite .= '<div>';
			$contenu_droite .= '<input type="text" class="recherche_tag" title="rechercher un tag" />';
			$contenu_droite .= '<input type="button" class="recherche_tag" />';
		$contenu_droite .= '</div>';
		//On affiche les tags sélectionnés par l'utilisateur
		$contenu_droite .= '<div id="liste_tag_select">';
			for($i_tag=0;$i_tag<count($les_tags_select);$i_tag++){
				$contenu_droite .= '<div onclick="tag_click(this);" class="un_tag '.$les_tags_select[$i_tag]["class"].'" id="tag_'.$les_tags_select[$i_tag]["no"].'">'.$les_tags_select[$i_tag]["titre"].'</div>';
			}
		$contenu_droite .= '</div>';
		$contenu_droite .= '<div id="liste_tag">';
			//On affiche les tags disponibles selon la recherche, la vie, et non sélectionnés par l'utilisateur
		for($i_tag=0;$i_tag<count($les_tags);$i_tag++){
			$contenu_droite .= '<div onclick="tag_click(this);" class="un_tag '.$les_tags[$i_tag]["class"].'" id="tag_'.$les_tags[$i_tag]["no"].'">'.$les_tags[$i_tag]["titre"].'</div>';
		}
		$contenu_droite .= '</div>';
	$contenu_droite .= "</div>";*/
}
$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
