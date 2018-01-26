<?php
/****
On met dans le contenu de droite le texte d'accueil de l'espace éditeur.
***/
//On vérifie la connexion
if(est_connecte()){
	//Si l'utilisateur est connecté
	//On récupère le titre et le contenu du bloc "espace personnel"
	/*$requete = "SELECT titre, contenu FROM contenu_blocs WHERE no=4 AND etat=1";
	$tab_espacePerso = execute_requete($requete);
	if(count($tab_espacePerso)>0){
		$contenu_droite = '<h3>'.$tab_espacePerso[0]["titre"].'</h3>';
		$contenu_droite .= $tab_espacePerso[0]["contenu"];
	}
	else{
		$contenu_droite = "";
	}*/
	$contenu_droite = contenu_colonne_droite("espace_perso");
	
	/***
	L'utilisateur demande son espace personnel, ou les fiches qu'il a saisi.
	***/
	$contenu = '<div>';
		$contenu .= '<link rel="stylesheet" href="dev.css" />'; //TODO
		if(empty($_POST["sous_page"])||$_POST["sous_page"]=="mes-fiches"){
			//On récupère la localisation préféree de l'utilisateur, son avatar, etc.
			$requete_infos_utilisateur = "SELECT villes.code_postal, villes.nom_ville_maj FROM utilisateur JOIN villes ON utilisateur.no_ville=villes.id WHERE utilisateur.no=:no";
			$tab_infos_utilisateur = execute_requete($requete_infos_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
			$nom_ville_utilisateur = $tab_infos_utilisateur[0]["code_postal"].' - '.$tab_infos_utilisateur[0]["nom_ville_maj"];
			//1. On affiche ses informations personnelles.
			$contenu .= '<div id="informations_personnelles">';
				$contenu .= '<div id="boutons_personnels">';
					$contenu .= '<input type="button" class="ico deconnexion" value="Déconnexion" onclick="deconnexion();" />';
					if($_SESSION["droit"]["no"]==1){
						$contenu .= '<br /><a href="'.$root_site.'gestion/" target="_blank"><input type="button" class="ico fleche" value="Administration" /></a>';
					}
				$contenu .= '</div>';
				$contenu .= '<div class="" style="display: none;"></div>'; // <- La future image du profil.
				$contenu .= '<div class="">';
					$contenu .= '<span id="pseudo"'.(!empty($_SESSION["utilisateur"]["pseudo"])?'':' class="vide"').'>'.(!empty($_SESSION["utilisateur"]["pseudo"])?$_SESSION["utilisateur"]["pseudo"]:'Nom d\'utilisateur').'</span><br />';
					$contenu .= '<span id="ville">'.$nom_ville_utilisateur.'</span><br />';
					$contenu .= '<span id="email">'.$_SESSION["utilisateur"]["email"].'</span><br />';
					$contenu .= '<span id="mdp">modifier mon mot de passe</span><br />';
				$contenu .= '</div>';
			$contenu .= '</div>';
	
			//2. On affiche les onglets "accueil", "archives", "mes fiches"
			$contenu .= '<div id="barre_onglets_personnels">';
				$contenu .= '<a href="espace-personnel.html"><div class="favoris '.(($_POST["sous_page"]!="mes-fiches")?"actif":"").'">Mes favoris</div></a>';
				$contenu .= '<a href="espace-personnel.mes-fiches.html"><div class="publications '.(($_POST["sous_page"]=="mes-fiches")?"actif":"").'">Mes publications</div></a>';
			$contenu .= '</div>';
			
			//3. On affiche la liste des fiches (favoris ou fiches créées)
			$contenu .= '<div id="espace_personnel_liste_fiche">';
			
				if($_POST["sous_page"]!="mes-fiches"){
					$contenu .= '<div class="libelle">Retrouvez ci-dessous toutes les fiches que vous avez ajouté à vos favoris.</div>';
					$parametres["favoris"] = $_SESSION["utilisateur"]["no"];
				}
				else{
					$contenu .= '<div class="libelle">Retrouvez ci-dessous toutes les fiches que vous avez vous-même saisi.</div>';
					$parametres["espace_personnel"] = $_SESSION["utilisateur"]["no"];
				}
				
				$les_fiches = array();
				if($_POST["sous_page"]!="mes-fiches"||($_POST["sous_page"]=="mes-fiches"&&$_SESSION["droit"]["no"]>0))
					$les_fiches[] = array("type"=>"editorial","libelle"=>"Éditorial","libelle_ajout"=>"ajouter un article");
				$les_fiches[] = array("type"=>"agenda","libelle"=>"Éditorial","libelle_ajout"=>"ajouter un événement");
				$les_fiches[] = array("type"=>"structure","libelle"=>"Répertoire","libelle_ajout"=>"ajouter une structure");
				$les_fiches[] = array("type"=>"petite-annonce","libelle"=>"Annonces","libelle_ajout"=>"ajouter une annonce");
				$les_fiches[] = array("type"=>"forum","libelle"=>"Forums","libelle_ajout"=>"ajouter un sujet");
				
				for($f=0;$f<count($les_fiches);$f++){
					$infos_fiches = extraire_liste($les_fiches[$f]["type"],30,1,$parametres);
						$tab_fiches = $infos_fiches["liste"];
					
					$contenu .= '<div id="'.$les_fiches[$f]["type"].'" class="espace_personnel_liste_fiche_bloc plier '.$les_fiches[$f]["type"].'">';
						$contenu .= '<h2>'.$les_fiches[$f]["libelle"].'</h2>';
						$contenu .= '<div class="espace_personnel_liste_fiche_liste">';
						
							if(empty($tab_fiches)){
								$contenu .= '<div style="margin:0.2em 2em;">';
								if(empty($_POST["sous_page"]))
									$contenu .= 'Aucun favori enregistré actuellement';
								else
									$contenu .= 'Aucune fiche saisie actuellement';
								$contenu .= '</div>';
							}
							else{
								for($e=0;$e<count($tab_fiches);$e++){
									$contenu .= '<div class="ligne_espace_personnel">';
											if(!empty($tab_fiches[$e]["image"])&&$tab_fiches[$e]["image"]!=$root_site){
												$contenu .= '<a href="'.$les_fiches[$f]["type"].'.'.$NOM_VILLE_URL.'.'.url_rewrite($tab_fiches[$e]["titre"]).'.'.$ID_VILLE.'.'.$tab_fiches[$e]["no"].'.html">';
													$contenu .= '<div class="image 1/1 visible" style="float: left;">';
														$contenu .= '<img src="'.$tab_fiches[$e]["image"].'" />';
													$contenu .= '</div>';
												$contenu .= '</a>';
											}
											if(!empty($tab_fiches[$e]["titre"])){
												$contenu .= '<h3>';
													$contenu .= '<a href="'.$les_fiches[$f]["type"].'.'.$NOM_VILLE_URL.'.'.url_rewrite($tab_fiches[$e]["titre"]).'.'.$ID_VILLE.'.'.$tab_fiches[$e]["no"].'.html">';
													$contenu .= $tab_fiches[$e]["titre"];
													$contenu .= '</a>';
												$contenu .= '</h3>';
											}
											else{
												$contenu .= '<div>Aucun titre</div>';
											}
											$contenu .= '<div>';
												$contenu .= '<a href="espace-personnel.'.$les_fiches[$f]["type"].'.'.$tab_fiches[$e]["no"].'.generalites.html"><input type="button" class="ico editer couleur" value="Modifier" /></a>';
												$contenu .= '<div class="informations_creation">';
													$contenu .= 'Créé le : '.$tab_fiches[$e]["date_creation"].'<br />';
													$contenu .= 'Modifié le : '.$tab_fiches[$e]["date_modification"];
												$contenu .= '</div>';
											$contenu .= '</div>';
									$contenu .= '</div>';
								}
							}
							
						$contenu .= '</div>';
						if(count($tab_fiches)>2){
							$contenu .= '<div class="plier_deplier" onclick="plier_deplier(this.parentNode)">';
								$contenu .= '<div class="plier">... voir plus ('.(count($tab_fiches)-2).')</div>';
								$contenu .= '<div class="deplier">Replier</div>';
							$contenu .= '</div>';
						}
						$contenu .= (($_POST["sous_page"]=="mes-fiches")?'<div class="nouvelle_fiche"><a href="espace-personnel.'.$les_fiches[$f]["type"].'.html"><input type="button" class="ico couleur plus" value="'.$les_fiches[$f]["libelle_ajout"].'" /></a></div>':'');
					$contenu .= '</div>';
				}
				
			$contenu .= '</div>'; // fin de id="espace_personnel_liste_fiche"
		}
		/***
		L'utilisateur demande un type (editorial,evenement,structre,petite-annonce,forum)
		**/
		else{
			$PAGE = $_POST["sous_page"]; //Variable PAGE utilisée dans l'admin et les fichiers formulaires
			if($PAGE=="agenda")
				$PAGE="evenement";
			if($PAGE=="repertoire")
				$PAGE="structure";
			if(empty($_POST["etape"])){
				//On affiche la mention expliquant le principe de la recherche avant l'ajout en fonction du type demandé
				if($PAGE=="editorial"){
					$phrase_presentation = 'Vous allez rédiger votre propre article. Celui-ci n\'apparaîtra sur le site qu\'après vérification par nos soins.';
					$cette_fiche = 'mon article';
				}
				else if($PAGE=="evenement"){
					$phrase_presentation = 'Avant d\'ajouter votre événement, assurez-vous qu\'il n\'est pas déjà présent sur le site&nbsp;:';
					$cette_fiche = 'cet événement';
				}
				else if($PAGE=="structure"){
					$phrase_presentation = 'Avant d\'ajouter votre structure, assurez-vous qu\'elle n\'est pas déjà présente sur le site&nbsp;:';
					$cette_fiche = 'cette structure';
				}
				else if($PAGE=="petite-annonce"){
					$phrase_presentation = '<p>Cet espace est réservé aux "<b>petites annonces</b>" (entraide, échanges, offres, recherches particulières, monétaires ou non) et aux initiatives (appel à bénévoles, présentation d\'un projet, inscriptions à des stages ou ateliers..).</p><p>S\'il s\'agit d\'un événement, merci de l\'ajouter dans l\'<b>espace Agenda</b> ("ajouter un événement"); pour une activité ou une structure, rendez vous dans le "Répertoire" d\'Ensemble Ici !</p>';
					$cette_fiche = 'cette petite annonce';
				}
				else if($PAGE=="forum"){
					$phrase_presentation = 'Avant d\'ajouter votre sujet, assurez-vous qu\'un sujet similaire n\'est pas déjà présent sur le site&nbsp;:';
					$cette_fiche = 'ce sujet';
				}
				if($PAGE!="editorial"||$_SESSION["droit"]["no"]>0){ //On autorise l'éditorial seulement aux adhérants
					$contenu .= $phrase_presentation.'<br />';
					//On affiche le formulaire de recherche en fonction du type
					$contenu .= '<form class="'.$_POST["sous_page"].'" action="espace-personnel.'.$_POST["sous_page"].'.generalites.html" method="post" onsubmit="return creer_nouvelle_fiche(this);">';
						if($PAGE!="editorial"){
							$contenu .= '<input type="text" name="q" id="input_titre_recherche" title="nom de la fiche" onkeyup="rechercher_temps_reel(this,\''.$PAGE.'\');" />';
							//Recherche ajax.
							$contenu .= '<div id="zone_recherche"></div>';
						}
						$contenu .= '<input type="submit" class="ico couleur plus" value="Créer '.$cette_fiche.'" />';
					$contenu .= '</form>';
				}
				else
					$contenu .= 'Vous n\'avez pas les autorisations nescessaires';
			}
			else{
				if(empty($NO)){ //INSERTION, CREATION
					//On est en mode création puisqu'il n'y a pas encore de NO
					//On créait donc la fiche avec l'éventuel post "q" qui correspondrait alors au titre saisie dans la recherche
					if(empty($_POST["q"])) $_POST["q"]="";
					$NO = execute_requete('INSERT INTO '.str_replace("-","",$PAGE).'('.(($PAGE!="structure")?'titre':'nom').',date_creation,no_utilisateur_creation) VALUES(:t,:d,:u)',array(':t'=>$_POST["q"],':d'=>date("Y-m-d"),":u"=>$_SESSION["utilisateur"]["no"]));
				}
				//On récupère les informations
				$tab = extraire_fiche($PAGE,$NO);
				if(!empty($tab)){
					//On affiche le fil d'arianne avec la liste des étapes.
					$contenu .= '<div id="etapes" class="'.$_POST["sous_page"].'">';
						$contenu .= '<a onclick="return espace_personnel_aller_a_etape(this.href)" href="espace-personnel.'.$_POST["sous_page"].'.'.$NO.'.generalites.html" class="une_etape'.(($_POST["etape"]=='generalites')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">1</div>Généralités';
						$contenu .= '</div></a>';
						$contenu .= '<a onclick="return espace_personnel_aller_a_etape(this.href)" href="espace-personnel.'.$_POST["sous_page"].'.'.$NO.'.thematique.html" class="une_etape'.(($_POST["etape"]=='thematique')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">2</div>Thématiques';
						$contenu .= '</div></a>';
						$contenu .= '<a onclick="return espace_personnel_aller_a_etape(this.href)" href="espace-personnel.'.$_POST["sous_page"].'.'.$NO.'.details.html" class="une_etape'.(($_POST["etape"]=='details')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">3</div>Détails';
						$contenu .= '</div></a>';
						$contenu .= '<a onclick="return espace_personnel_aller_a_etape(this.href)" href="espace-personnel.'.$_POST["sous_page"].'.'.$NO.'.illustration.html" class="une_etape'.(($_POST["etape"]=='illustration')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">4</div>Illustration';
						$contenu .= '</div></a>';
						$contenu .= '<a onclick="return espace_personnel_aller_a_etape(this.href)" href="espace-personnel.'.$_POST["sous_page"].'.'.$NO.'.validation.html" class="une_etape'.(($_POST["etape"]=='validation')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">5</div>Validation';
						$contenu .= '</div></a>';
					$contenu .= '</div>';
				
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
						$site = $tab["site"];
		
						//Lieu
						$ville = $tab["ville"];
						$cp = $tab["cp"];
						$no_ville = $tab["no_ville"];
		
						//Description
						$description = $tab["description"];
		
						//Illustration
						$url_image = $tab["image"];
							//Copyright
							$copyright = $tab["copyright"];
							//Légende
							$legende = $tab["legende"];
		
		
						if($PAGE=="evenement"||$PAGE=="structure"){
							//Lieu
							$nom_lieu = $tab["nom_adresse"];
							$adresse = $tab["adresse"];
							$telephone = $tab["telephone"];
							$telephone2 = $tab["telephone2"];
							$email = $tab["email"];
						}
		
						if($PAGE=="evenement"){
							//infos générales
							$libelle_genre = $tab["libelle_genre"];
							$no_genre = $tab["no_genre"];
							$date_deb = $tab["date_debut"];
								if($date_deb=="00/00/0000")
									$date_deb="";
							$date_fin = $tab["date_fin"];
								if($date_fin=="00/00/0000")
									$date_fin="";
							$heure_deb = $tab["heure_debut"];
							$heure_fin = $tab["heure_fin"];
							//Description
							$description_complementaire = $tab["description_complementaire"];
						}
						else if($PAGE=="editorial"){
							//Description
							$chapo = $tab["chapo"];
							$notes = $tab["notes"];
							$afficher_signature = $tab["afficher_signature"];
							$signature = $tab["signature"];
						}
						else if($PAGE=="structure"){
							$facebook = $tab["facebook"];
							$no_statut = $tab["no_statut"];
						}
						else if($PAGE=="petite-annonce"){
							$prix = $tab["prix"];
							$monetaire = $tab["monetaire"];
							$no_petiteannonce_type = $tab["no_petiteannonce_type"];
							$date_fin = $tab["date_fin"]; //Sinon aujourd'hui + 61jours (en moyenne deux mois)
							if($date_fin=="00/00/0000")
									$date_fin="";
							$rayonmax = $tab["rayonmax"];
						}
						else if($PAGE=="forum"){
							$afficher_signature = $tab["afficher_signature"];
							$signature = $tab["signature"];
							$no_forum_type = $tab["no_forum_type"];
						}
						/*
						$contenu .= '<a href="espace-personnel.'.$PAGE.'.generalites.html" class="une_etape'.(($_POST["etape"]=='generalites')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">1</div>Généralités';
						$contenu .= '</a></div>';
						$contenu .= '<a href="espace-personnel.'.$PAGE.'.generalites.html" class="une_etape'.(($_POST["etape"]=='thematique')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">2</div>Thématique';
						$contenu .= '</a></div>';
						$contenu .= '<a href="espace-personnel.'.$PAGE.'.generalites.html" class="une_etape'.(($_POST["etape"]=='details')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">3</div>Détails';
						$contenu .= '</a></div>';
						$contenu .= '<a href="espace-personnel.'.$PAGE.'.generalites.html" class="une_etape'.(($_POST["etape"]=='illustration')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">4</div>Illustration';
						$contenu .= '</a></div>';
						$contenu .= '<a href="espace-personnel.'.$PAGE.'.generalites.html" class="une_etape'.(($_POST["etape"]=='validation')?' courante':'').'"><div>';
							$contenu .= '<div class="numero">5</div>Validation';
						$contenu .= '</a></div>';*/
				
					if($_POST["etape"]=="generalites")
						$form_action = "espace-personnel.".$_POST["sous_page"].".".$NO.".thematique.html";
					else if($_POST["etape"]=="thematique")
						$form_action = "espace-personnel.".$_POST["sous_page"].".".$NO.".details.html";
					else if($_POST["etape"]=="details")
						$form_action = "espace-personnel.".$_POST["sous_page"].".".$NO.".illustration.html";
					else if($_POST["etape"]=="illustration")
						$form_action = "espace-personnel.".$_POST["sous_page"].".".$NO.".validation.html";
					else if($_POST["etape"]=="validation")
						//$form_action = "espace-personnel.".$_POST["sous_page"].".".$NO.".details.html"; //correction max suite redirection etape 3 vidage tag
						$form_action = $NOM_VILLE_URL.'.'.$ID_VILLE.'.'.$_POST["sous_page"].'.html'; //TODO -> 30km
				
					$contenu .= '<form id="formulaire_espace_personnel_etape" action="'.$form_action.'" onsubmit="return espace_personnel_etape_suivante(this'.(($_POST["etape"]=="validation")?',\'etat=1\'':'').');" method="post" class="'.$_POST["sous_page"].'">';
					//$contenu .= '<form action="'.$form_action.'" onsubmit="return false;" method="post" class="'.$_POST["sous_page"].'">';
				
					//On affiche l'étape qui correspond.
					if($_POST["etape"]=="generalites"){
						include "01_include/formulaires/_formulaire_generalites.php";
						include "01_include/formulaires/_formulaire_description.php";
					}
					else if($_POST["etape"]=="thematique"){
						include "01_include/formulaires/_formulaire_tags.php";
					}
					else if($_POST["etape"]=="details"){
						include "01_include/formulaires/_formulaire_lieu.php";
						if($PAGE=="evenement"||$PAGE=="petite-annonce"||$PAGE=="structure")
							include "01_include/formulaires/_formulaire_contact.php";
						else
							include "01_include/formulaires/_formulaire_auteur.php";
						//include "01_include/formulaires/_formulaire_liaisons.php"; caché par max pas necessaire de laisser des accès non finis
					}
					else if($_POST["etape"]=="illustration"){
						include "01_include/formulaires/_formulaire_illustration.php";
					}
					else if($_POST["etape"]=="validation"){
						$PREVISUALISATION = true;
						$PAGE_COURANTE = $PAGE; //TODO MODIFIER
						include "01_include/struct_fiche.php";
					}
					$contenu = str_replace("bloc","bloc_formulaire",$contenu);
					
						//On affiche le bouton permettant de passer à l'étape suivante, ou celui permettant de valider la fiche.
						$contenu .= '<div class="div_input_submit">';
							if($_POST["etape"]!="generalites")
								$contenu .= '<input type="hidden" id="BDDtype" name="BDDtype" value="'.$PAGE.'" /><input type="hidden" id="BDDno" name="BDDno" value="'.$NO.'" />';
							$contenu .= '<input type="submit" class="ico couleur fleche" value="'.(($_POST["etape"]!="validation")?'Étape suivante':'Publier sur ensembleici.fr').'" />';
						$contenu .= '</div>';
					
					$contenu .= '</form>';
				}
				else{ //Fiche demandée introuvable ...
					$contenu .= '<p>La fiche que vous demandez est introuvable ...</p>';
					$contenu .= '<p><a href="espace-personnel.mes-fiches.html"><input type="button" class="ico fleche_gauche" value="Retour à mon espace personnel" /></a></p>';
				}
				
			}
		}
	$contenu .= '</div>';
}
else{
	//Si l'utilisateur est déconnecté
	//On récupère le titre et le contenu du bloc "animation"
	/*$requete = "SELECT titre, contenu FROM contenu_blocs WHERE no=1 AND etat=1";
	$tab_espacePerso = execute_requete($requete);
	if(count($tab_espacePerso)>0){
		$contenu_droite = '<h3>'.$tab_espacePerso[0]["titre"].'</h3>';
		$contenu_droite .= $tab_espacePerso[0]["contenu"];
	}
	else{
		$contenu_droite = "";
	}*/
	$contenu_droite = contenu_colonne_droite("contenu_bloc[1]");
	
	$contenu = '<div>';
		$contenu .= '<p>';
			$contenu .= 'L\'espace personnel permet de faciliter votre navigation sur le site « Ensemble ici » en mémorisant votre situation géographique.';
		$contenu .= '</p>';
		$contenu .= '<p>';
			$contenu .= 'Notre système d\'ajout/modification de contenus nécessite la connexion à un espace personnel pour sécuriser et faciliter la gestion de vos informations.';
		$contenu .= '</p>';
		$contenu .= '<div id="espace-perso_connexion">';
			$contenu .= '<div>';
				$contenu .= '<p>Vous avez déjà un espace personnel sur "Ensemble Ici"</p>';
				$fonction_sortie_connexion = 'charger_page()';
				include "01_include/struct_form_connexion.php";
					$contenu = str_replace('id="input_email"','id="input_email_espacePerso"',$contenu);
					$contenu = str_replace('id="input_mdp"','id="input_mdp_espacePerso"',$contenu);
			$contenu .= '</div>';
		$contenu .= '</div>';
		$contenu .= '<div id="espace-perso_inscription">';
			$contenu .= '<div>';
				$contenu .= '<p>Vous n\'avez pas encore d\'espace personnel sur "Ensemble Ici" ?</p>';
				$contenu .= '<input type="button" value="Créer un compte" class="ico nouveau_compte" onclick="fenetre_nouveau_compte()" />';
				$contenu .= '<div class="important">(création en moins de 2 minutes)</div>';
			$contenu .= '</div>';
		$contenu .= '</div>';
	$contenu .= '</div>';
}

$ligne1 = array(array("class"=>"grand_moyen","id"=>"colonne_gauche","contenu"=>$contenu),array("class"=>"moyen_petit","id"=>"colonne_droite","contenu"=>$contenu_droite));
$lignes = array(array("lignes"=>$ligne1));
?>
