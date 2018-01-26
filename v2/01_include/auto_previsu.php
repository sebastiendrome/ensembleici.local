<?php
session_name("EspacePerso");
session_start();
include ('01_include/_connect.php');
include ('01_include/_var_ensemble.php');
include('01_include/fonction_redim_image.php');

$no = intval($_REQUEST['no_fiche']);

if ((preg_match("#^(structure|evenement|annonce|petiteannonce)$#", strtolower($_REQUEST['type'])))&&($no))
{
	$type = strtolower($_REQUEST['type']);

	if (preg_match("#^(espaceperso)$#", $_REQUEST['source']))
		$depuis_espaceperso = true;

	// Si on vient de la liste des evenements ou du repertoire
	if ((intval($_REQUEST['liste']))&&($_REQUEST['liste']==1))
		$depuis_liste =true;

	$mode_modification = intval($_REQUEST['mode_modification']);
	// Evenement
	if (!$mode_modification && $_SESSION['mode_modification_evt'])
	  $mode_modification = intval($_SESSION['mode_modification_evt']);
	// Structures
	if (!$mode_modification && $_SESSION['mode_modification_str'])
	  $mode_modification = intval($_SESSION['mode_modification_str']);

	// Aperçu d'une fiche (utilisé par exemple dans les form autopres pour afficher les liaisons)
	$mode_apercu_popup = intval($_REQUEST['apercu']);
	if ($mode_apercu_popup) $depuis_liste = false;

	// On est à la dernière étape de création/modif : validation de la fiche
	if ((intval($_REQUEST['etape_validation']))&&($_REQUEST['etape_validation']==1))
	{
		$etape_validation =true;

		if($type=="structure") {
			$libele_img = "url_logo";
			$dossier_up_img_num = "04"; // Numéro au début du nom du répertoire
			$gestion_versions= true; // versioning ?
		}
		elseif ($type=="evenement")
		{
			$libele_img = "url_image";
			$dossier_up_img_num = "05";
			$gestion_versions= true;
		}
		elseif ($type=="petiteannonce")
		{
			$libele_img = "url_image";
			$dossier_up_img_num = "09";
			$gestion_versions= false;
		}

		// on a cliqué sur le bouton supprimer l'image
		if ($_REQUEST['imagesupprime']=="oui")
		{
 			// Si l'image n'est pas enregistrée dans une version de l'historique, on la supprime

			// On récupère le nom de l'image supprimée
			$sql_url_image = "SELECT ".$libele_img." FROM ".$type." WHERE no=:no";
			$res_url_image = $connexion->prepare($sql_url_image);
			$res_url_image->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
			$tab_url_image=$res_url_image->fetchAll();
			$limg_supprimee = $tab_url_image[0][$libele_img];

			if ($gestion_versions) 
			{
				// Des versions utilisent cette image ?
				$sql_m = "SELECT * FROM `".$type."_modification` M, `".$type."_temp` T
							    WHERE M.no_".$type."_temp = T.no
							    AND no_".$type."=:no_".$type;
				$res_m = $connexion->prepare($sql_m);
				$res_m -> execute(array(":no_".$type=>$no)) or die ("Erreur ".__LINE__.".");
				$tab_m = $res_m->fetchAll();
				foreach ($tab_m as $m) {
					if ($m["url_image"] == $limg_supprimee) $img_utilise = true;
				}
			}
			if (!$img_utilise)
			{
				// Image non utilisée => On la supprime
				if (($limg_supprimee) && (file_exists($limg_supprimee)))
				{
					unlink($limg_supprimee);

					// Suppression de la BDD
					$maj_elt_supimg = "UPDATE $type SET $libele_img='' WHERE no=:no";
					$maj_elt_si = $connexion->prepare($maj_elt_supimg);
					$maj_elt_si->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
				}
			}
		}

		// Une image a été postée ?
		if ($_GET['ajout_image']==1)
		{
			$dossier_up_img = "02_medias/".$dossier_up_img_num."_".$type."/";

			$tab_upload_img=array();
			$tab_upload_img=redimensionne_img('image_logo', $dossier_up_img, 1, 800, 800);

			if($tab_upload_img[0]==0)//upload ok on insere les données
			{
				if ($type=="petiteannonce") {
					// Sans copyright
					$maj_elt_img = "UPDATE $type SET $libele_img=:url_logo WHERE no=:no";
					$maj_elt = $connexion->prepare($maj_elt_img);
					$maj_elt->execute(array(':url_logo'=>$tab_upload_img[2],':no'=>$no)) or die ("Erreur ".__LINE__.".");
				} else {
					$maj_elt_img = "UPDATE $type SET $libele_img=:url_logo, copyright=:copyright WHERE no=:no";
					$maj_elt = $connexion->prepare($maj_elt_img);
					$maj_elt->execute(array(':url_logo'=>$tab_upload_img[2], ':copyright'=>$_POST['copyright'], ':no'=>$no)) or die ("Erreur ".__LINE__.".");
				}
			}
			else
			{
				// Erreur
				echo "<script language=\"JavaScript\">alert('Erreur : ".$tab_upload_img[1]."')</script>";
			}
		}

		// Passe l'état de l'élément à 1
		$maj_elt_etat = "UPDATE $type SET etat=1 WHERE no=:no";
		$maj_elte = $connexion->prepare($maj_elt_etat);
		$maj_elte->execute(array(':no'=>$no));

	}
	else if (!$mode_apercu_popup)
	{
		// Supprimer aussi dans espace_personnel.php
		
		// On supprime le mode modification précédemment enregistré
		unset($_SESSION['mode_modification_evt']);
		unset($_SESSION['mode_modification_str']);
		unset($_SESSION['mode_modification_pa']);
		// Idem pour l'étape à laquelle on s'est arrêtés
		unset($_SESSION['etape_arret_form_evt']);
		unset($_SESSION['etape_arret_form_str']);
		unset($_SESSION['etape_arret_form_pa']);

		// Idem pour le mode de annonce ou évènement
		unset($_SESSION['type_annonce']);

		// Les id en modif
		unset($_SESSION['no_evenement']);
		unset($_SESSION['no_structure']);
		unset($_SESSION['no_pa']);

	}

	// Annonce (!= de petite annonce) => traitement comme un évènement, avec chgt de quelques textes
	if ($type=="annonce")
		$estAnnonce = true;
	if (!$estAnnonce)
	{
		if ($_SESSION['type_annonce'])
			$estAnnonce = intval($_SESSION['type_annonce']);
	}
	if ($estAnnonce) $type = "evenement";

	if ($depuis_liste)
	{
		// le nom de la ville selectionnée
		if($type=="structure")
		{
			$sql_e="SELECT nom, libelle, nom_ville_maj, code_postal
			FROM `structure` S,
			`villes` V,
			`statut` T
			WHERE S.no = :nostr
			AND S.etat = 1
			AND S.no_statut = T.no
			AND S.no_ville = V.id";
			// echo $sql_e;
			$res_e = $connexion->prepare($sql_e);
			$res_e->execute(array(':nostr'=>$no));
			$tab_e = $res_e->fetch(PDO::FETCH_ASSOC);
			$titre_struct = $tab_e["nom"];
			$titre_statut = $tab_e["libelle"];
			$titre_ville = $tab_e["nom_ville_maj"];
			$code_postal = $tab_e["code_postal"];

			// Affichage d'une structure
			if ($titre_struct)
			{
				$titre_page = coupe_chaine($titre_struct,130,false)." (".$titre_statut.") à ".$titre_ville." (".$code_postal.")";
				$titre_page_bleu = $titre_statut." à ".$titre_ville;
				if (strlen($titre_page_bleu)>28)
					$titre_page_bleu = $titre_ville;
				$page_ville = true; // Pour affichage du bouton modifier
				$meta_description = $titre_struct." (".$titre_statut.") à ".$titre_ville." sur Ensemble ici.";
			}
			else
			{
				$titre_page = "Structure introuvable";
				$titre_page_bleu = " ";
			}
		}
		elseif ($type=="petiteannonce")
		{ // Petite annonce 
			$sql_e="SELECT titre, nom_ville_maj, code_postal
			FROM `petiteannonce` E,
			`villes` V
			WHERE E.no = :noevt
			AND E.etat = 1
			AND E.no_ville = V.id";
			$res_e = $connexion->prepare($sql_e);
			$res_e->execute(array(':noevt'=>$no));
			$tab_e = $res_e->fetch(PDO::FETCH_ASSOC);
			$titre_pa = $tab_e["titre"];
			$titre_ville = $tab_e["nom_ville_maj"];
			$code_postal = $tab_e["code_postal"];

			// Affichage d'un evement
			if ($titre_pa)
			{
				$titre_page = coupe_chaine($titre_pa,150,false)." à ".$titre_ville." (".$code_postal.")";
				$titre_page_bleu = $titre_ville;
				$page_ville = true; // Pour affichage du bouton modifier
				$meta_description = $titre_pa." à ".$titre_ville." sur Ensemble ici.";
			}
			else
			{
				$titre_page = "Petite annonce introuvable";
				$titre_page_bleu = " ";
			}
		}
		else
		{ // Evenements 
			$sql_e="SELECT titre, libelle, nom_ville_maj, code_postal
			FROM `evenement` E,
			`villes` V,
			`genre` G
			WHERE E.no = :noevt
			AND E.etat = 1
			AND E.no_genre = G.no
			AND E.no_ville = V.id";
			$res_e = $connexion->prepare($sql_e);
			$res_e->execute(array(':noevt'=>$no));
			$tab_e = $res_e->fetch(PDO::FETCH_ASSOC);
			$titre_evt = $tab_e["titre"];
			$titre_genre = $tab_e["libelle"];
			$titre_ville = $tab_e["nom_ville_maj"];
			$code_postal = $tab_e["code_postal"];

			// Affichage d'un evement
			if ($titre_evt)
			{
				$titre_page = coupe_chaine($titre_evt,130,false)." (".$titre_genre.") à ".$titre_ville." (".$code_postal.")";
				$titre_page_bleu = $titre_genre." à ".$titre_ville;
				if (strlen($titre_page_bleu)>28)
					$titre_page_bleu = $titre_genre;
				$page_ville = true; // Pour affichage du bouton modifier
				$meta_description = $titre_evt." (".$titre_genre.") à ".$titre_ville." sur Ensemble ici.";
			}
			else
			{
				$titre_page = "Evènement introuvable";
			}
		}
	}
	else
	{
		// On est en dans la prévisualisation (étape 0 ou 4 des formulaires)
		$modeprevisu = true;

		// Mode modification ou ajout ?
		if ($mode_modification)
			$action_page = "Modifier";
		else
			$action_page = "Ajouter";
	}
	
	$sql_previsu="SELECT * FROM ".$type." WHERE no=:no";
	if ($depuis_liste) $sql_previsu .= " AND etat = 1"; // Actif uniquement
	$res_previsu = $connexion->prepare($sql_previsu);
	$res_previsu->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
	$pv=$res_previsu->fetchAll();
	$nb_result=$res_previsu->rowCount();

	if ($nb_result)
	{
		if ((empty($id_vie))&&(empty($id_tag)))
		{
			if($type=="structure")
			{
				//recuperation des infos structures
				$sql_ss_tag="SELECT * FROM ".$type."_sous_tag WHERE no_".$type."=:no";
				$res_ss_tag = $connexion->prepare($sql_ss_tag);
				$res_ss_tag->execute(array(':no'=>$pv[0]['no'])) or die ("Erreur ".__LINE__.".");
				$tab_ss_tag=$res_ss_tag->fetchAll();
			}
			elseif (($type=="evenement") || ($type=="petiteannonce"))
			{
				// Evenement
				$sql_ss_tag="SELECT * FROM ".$type."_tag WHERE no_".$type."=:no";
				$res_ss_tag = $connexion->prepare($sql_ss_tag);
				$res_ss_tag->execute(array(':no'=>$pv[0]['no'])) or die ("Erreur ".__LINE__.".");
				$tab_ss_tag=$res_ss_tag->fetchAll();
			}
		} else {
			// vie et tag passés en get pour provenance
			if (!empty($id_vie))
			{
				// libelle vie
				$sql_vie_libelle="SELECT libelle,libelle_url FROM vie WHERE no=:no";
				$res_vie_libelle = $connexion->prepare($sql_vie_libelle);
				$res_vie_libelle->execute(array(':no'=>$id_vie)) or die ("Erreur ".__LINE__.".");
				$tab_vie_libelle=$res_vie_libelle->fetchAll();
			}
			if (!empty($id_tag))
			{
				// libelle tag
				$sql_tag_libelle="SELECT titre FROM tag WHERE no=:no";
				$res_tag_libelle = $connexion->prepare($sql_tag_libelle);
				$res_tag_libelle->execute(array(':no'=>$id_tag)) or die ("Erreur ".__LINE__.".");
				$tab_tag_libelle=$res_tag_libelle->fetchAll();
			}
			elseif ($type=="structure")
			{
				//recuperation des infos structures
				$sql_ss_tag="SELECT * FROM ".$type."_sous_tag WHERE no_".$type."=:no";
				$res_ss_tag = $connexion->prepare($sql_ss_tag);
				$res_ss_tag->execute(array(':no'=>$pv[0]['no'])) or die ("Erreur ".__LINE__.".");
				$tab_ss_tag=$res_ss_tag->fetchAll();
			}
			elseif(($type=="evenement") || ($type=="petiteannonce"))
			{
				// Evenement
				$sql_ss_tag="SELECT * FROM ".$type."_tag WHERE no_".$type."=:no";
				$res_ss_tag = $connexion->prepare($sql_ss_tag);
				$res_ss_tag->execute(array(':no'=>$pv[0]['no'])) or die ("Erreur ".__LINE__.".");
				$tab_ss_tag=$res_ss_tag->fetchAll();
			}
		}
		
		$tag="";
		$pres_tag=0;
	
		//recuperation des contacts
		$sql_liaison_contact="SELECT * FROM ".$type."_contact WHERE no_".$type."=:no ORDER BY no_contact DESC";
		$res_liaison_contact = $connexion->prepare($sql_liaison_contact);
		$res_liaison_contact->execute(array(':no'=>$pv[0]['no'])) or die ("Erreur ".__LINE__.".");
		$tab_liaison_contact=$res_liaison_contact->fetchAll();
		
		//recuperation du premier contact
		$sql_contact="SELECT * FROM contact WHERE no=:no";
		$res_contact = $connexion->prepare($sql_contact);
		$res_contact->execute(array(':no'=>$tab_liaison_contact[0]['no_contact'])) or die ("Erreur ".__LINE__.".");
		$tab_contact=$res_contact->fetchAll();
		$nom_contact=$tab_contact[0]['nom'];
		
		$sql_ville="SELECT * FROM villes WHERE id=:no";
		$res_ville = $connexion->prepare($sql_ville);
		$res_ville->execute(array(':no'=>$pv[0]['no_ville'])) or die ("Erreur ".__LINE__.".");
		$tab_ville=$res_ville->fetchAll();
		
		if($type=="structure")
		{
			$titre=$pv[0]['nom'];
			$image=$pv[0]['url_logo'];
			$copyright_img=$pv[0]['copyright'];
			$description=$pv[0]['description'];
			$nom_ville=$tab_ville[0]['nom_ville_maj'];
			$cp_ville=$tab_ville[0]['code_postal'];
			$ss_titre=$pv[0]["sous_titre"];
			$nomadresse=$pv[0]["nomadresse"];
			$adresse=$pv[0]["adresse"];
			$adresse_comp=$pv[0]["adresse_complementaire"];
			$telephone=$pv[0]['telephone'];
			$telephone2=$pv[0]['telephone2'];
			$fax=$pv[0]['fax'];
			$email=$pv[0]['email'];
			$site=$pv[0]['site_internet'];
			$facebook=$pv[0]['facebook_internet'];
		}
		elseif($type=="petiteannonce")
		{
			$titre=$pv[0]['titre'];
			$image=$pv[0]['url_image'];
			$description=$pv[0]['description'];
			$nom_ville=$tab_ville[0]['nom_ville_maj'];
			$cp_ville=$tab_ville[0]['code_postal'];
			if ($pv[0]["afficher_tel"])
				$telephone=$tab_contact[0]['telephone'];
			else
				$telephone=0;
			if ($pv[0]["afficher_mob"]) {
				// tel principal ou ajout d'un secondaire
				if ($telephone)
					$telephone2=$tab_contact[0]['mobile'];
				else
					$telephone=$tab_contact[0]['mobile'];
			}
			else
				$telephone2=0;
			$email=$tab_contact[0]['email'];
			$site=$pv[0]['site_internet'];
			// Propre aux petites annonces
			$pa_date_fin=$pv[0]["date_fin"];
			$pa_monetaire=$pv[0]["monetaire"];
			$pa_prix=$pv[0]["prix"];
		}
		else
		{
			// Evenement
			$titre=$pv[0]['titre'];
			$image=$pv[0]['url_image'];
			$copyright_img=$pv[0]['copyright'];
			$description=$pv[0]['description'];
			$description_comp=$pv[0]['description_complementaire'];
			$nom_ville=$tab_ville[0]['nom_ville_maj'];
			$cp_ville=$tab_ville[0]['code_postal'];
			$ss_titre=$pv[0]["sous_titre"];
			$nomadresse=$pv[0]["nomadresse"];
			$adresse=$pv[0]["adresse"];
			$adresse_comp="";
			$telephone=$pv[0]['telephone'];
			$telephone2=$pv[0]['telephone2'];
			$email=$pv[0]['email'];
			$site=$pv[0]['site'];
			$facebook="";
			// Propre à un evt
			$evt_date_debut=$pv[0]['date_debut'];
			$evt_date_fin=$pv[0]['date_fin'];
			$evt_heure_debut=$pv[0]['heure_debut'];
			$evt_heure_fin=$pv[0]['heure_fin'];			
		}
		
		if ($modeprevisu)
		{
			if($type=="structure")
			{
				$titre_page = "Prévisualisation d'une structure";
				$meta_description = "Visualiser une structure sur Ensemble ici : Tous acteurs de la vie locale";
				$type_accentue = "structure";
				$un_accentue = "une structure";
			}
			elseif($type=="petiteannonce")
			{
				$titre_page = "Prévisualisation d'une petite annonce";
				$meta_description = "Visualiser une petite annonce sur Ensemble ici : Tous acteurs de la vie locale";
				$type_accentue = "petite annonce";
				$un_accentue = "une petite annonce";
			}
			else
			{ // Evenements 
				if ($estAnnonce)
				{
					// Annonce stage/atelier/cours
					$titre_page = "Prévisualisation d'une annonce";
					$meta_description = "Visualiser une annonce sur Ensemble ici : Tous acteurs de la vie locale";
					$type_accentue = "annonce";
					$un_accentue = "une annonce";
					$complement_titre = " <span class=\"soustitre\">Stages / atelier / cours</span>";
				}
				else
				{
					// Evnement normal
					$titre_page = "Prévisualisation d'un évènement";
					$meta_description = "Visualiser un évènement sur Ensemble ici : Tous acteurs de la vie locale";
					$type_accentue = "évènement";
					$un_accentue = "un évènement";
				}
			}
		}
		
		// Préparation du meta og:image => image pour facebook
		if(strlen($image)>0)
			$img_facebook = $root_site."miniature.php?uri=".$image."&method=fit&w=150&h=150";
		else
			$img_facebook = $root_site."img/logo-ensembleici_fb.jpg";
	
		// Infos de la ville selectionnée
		if (!empty($id_ville))
		{
			$sql_ville="SELECT * FROM villes WHERE id = :idville";
			$res_ville = $connexion->prepare($sql_ville);
			$res_ville->execute(array(':idville'=>$id_ville));
			$tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
			$titre_ville_bleu = $tab_ville["nom_ville_maj"];
		}
	
		$ajout_header .= <<<AJHE
			<meta property="og:image" content="$img_facebook"/> 
			<script type="text/javascript">
			  $(document).ready(function(){
				$(".agrandir").colorbox({
			    });
			  });
			</script>
AJHE;
		
		$titre_page_h1 = "$action_page $un_accentue - Etape 5 $complement_titre";
		$titre_page_bleu = $titre_ville_bleu;
		
		include ('01_include/structure_header.php');
		
	?>
	
		<script language="JavaScript">
		function fermer()
		{
			<?php
			if ($depuis_espaceperso) {
				// Retour page précédente
				echo "self.history.back();";
			}
			else
			{
				// Fermer la fenêtre
				echo "opener=self; self.close();";
			}
			?>
		}
		</script>
	      <div id="colonne2" class="page_ville">      
		<div id="vi" class="blocB">
	
		<?php
			if (!$depuis_liste && !$etape_validation && !$mode_apercu_popup)
			{
				// On est au début du form de modif / ajout
				echo "
			<p class='enlight'>Pour proposer une modification de la fiche ci-dessous cliquez sur 'modifier', sinon le bouton 'fermer' vous ramenera aux résultats de votre recherche.</p>
			<div>
			<form action='auto_".$type."_etape1.php' method='POST' name='modifier' class='formA'>
				<input type='hidden' name='no_orig' value='$no' />
				<input type='hidden' name='mode_modification' value='1' />";
				if ($estAnnonce)
					echo "<input type='hidden' name='annonce' value='1' />";
				echo "<input type='hidden' name='no_fiche' value='0' />
				<input type='hidden' name='no_fiche_temp' value='0' />
				<center><input type='submit' value='Modifier' />
				<input type='button' onclick='fermer()' value='Fermer' /></center>
			</form>
			</div>
			<br/><br/>
		";
			}
			else if ($mode_apercu_popup)
			{
				echo "<center><input type='button' onclick='fermer()' value='Fermer' /></center>";

			}
			else if(!$depuis_liste && $etape_validation)
			{
		// On est à la fin du form de modif / ajout : prévisualisation
		$lienretour = "auto_".$type."_etape4.php";
		echo "<h1";
		if($type=="structure") echo " class=\"repertoire\"";
		elseif($type=="evenement") echo " class=\"evt\"";
		elseif($type=="petiteannonce") echo " class=\"pa\"";
		echo ">".$titre_page_h1."</h1>";
	
		require('01_include/structure_etapes_form.php');
	
		echo <<<AJHV
			<p>
				Vous voici à la dernière étape, avec ci-dessous un aperçu de votre $type_accentue. Si l’ensemble des informations saisies vous satisfait, cliquez sur <strong>"Enregistrer"</strong>. Vos informations seront alors directement affichées sur "Ensemble ici". Vous pourrez ensuite à tout moment modifier votre fiche $type_accentue depuis votre espace personnel.</p>
			<div class="boutons"><a href="espace_personnel_retour.html" class="boutonbleu ico-fleche">Enregistrer</a></div>
AJHV;
			echo $description_validation;
			}
			?>
			
			<!-- affichage des informations de la fiche -->
			<?php
			// Liens retour si $depuis_liste (+ Modifier en admin)
			if($depuis_liste)
			{
				// Lien retour en js
				if(!empty($_SERVER['HTTP_REFERER']))
				{
				    if(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == $_SERVER['HTTP_HOST'])
					$lien_retour = 'Javascript:window.history.back();';
				}
				
				// Lien retour en dur (sans pagination, sans ancre)
				if (!$lien_retour)
				{
					// Préparation du lien
					$lien_retour .= $root_site;
					$lien_retour .= $titre_ville_url.".";
					if (($id_tag)&&($id_vie))
					{
						// Le tag sélectionné
						$lien_retour .= url_rewrite($tab_tag_libelle[0]['titre']).".tag.".$id_ville.".".$id_tag.".".$id_vie;
					}
					elseif ($id_vie)
					{
						// La vie sélectionnée
						$lien_retour .= url_rewrite($tab_vie_libelle[0]['libelle_url']).".".$id_ville.".".$id_vie;
					}
					else
					{
						// tout le répertoire / agenda / pa
						$lien_retour .= $id_ville;
						if($type=="structure")
							$lien_retour .= ".tout.repertoire";
						if($type=="petiteannonce")
							$lien_retour .= ".toutes.petites-annonces";
						else
							$lien_retour .= ".tout.agenda";				
					}
					$lien_retour .= ".html";
				}
	
				echo "<div class=\"actions-enhaut\">";
				echo "<a href=\"".$lien_retour."\"  class=\"boutonbleu ico-flecheretour\" title=\"Retour\">Retour</a>";
				// internaute est admin ?
				if(@$_SESSION['UserConnecte'])
				{
					$UserConnecte_id_fromSession = addslashes($_SESSION['UserConnecte']);
					$strQuery = "SELECT droits FROM `utilisateur` U
					WHERE id_connexion=:ucid
					AND `email`=:ucemail";
					$res_user = $connexion->prepare($strQuery);
					$res_user->bindParam(":ucid", $UserConnecte_id_fromSession, PDO::PARAM_STR);
					$res_user->bindParam(":ucemail", $UserConnecte_email, PDO::PARAM_STR);		
					$res_user->execute();
					$tab_us = $res_user->fetch(PDO::FETCH_ASSOC);
					if ($tab_us["droits"]=="A")
					{
						if($type=="structure")
							$rep_admin = "structures";
						elseif($type=="petiteannonce")
							$rep_admin = "petiteannonce";
						else
							$rep_admin = "events";
						echo " <a href=\"gestion/$rep_admin/modifajout.php?id=".$no."\"  class=\"boutonbleu ico-modifier\" target=\"_blank\" title=\"Modifier\">Modifier</a>";	
					}
				}
	
	
				echo "</div>";
			}
	
			// div pour message de retour colorbox (messagerie...)
			echo "<div id=\"msg\"></div>";

			if (!empty($titre)){

				echo "<h1";
				if($type=="structure") echo " class=\"repertoire\"";
				elseif($type=="petiteannonce") echo " class=\"pa\"";
				echo ">".$titre."<span class=\"soustitre\">".$ss_titre."</span>"
				?></h1>


			<?php
			// Date des évenements
			if ($evt_date_debut)
			{
			  /* if (($evt_date_fin)&&($evt_date_debut!=$evt_date_fin)){
			    echo "<div class=\"date\">Du ".datefr($evt_date_debut)." au ".datefr($evt_date_fin)."</div>";
			  }
			  else
			  {
			    echo "<div class=\"date\">Le ".datefr($evt_date_debut)."</div>";
			  }*/ 
			  
		    	echo "<div class=\"date\">".affiche_date_evt($evt_date_debut,$evt_date_fin)."</div>";
				echo "<div class=\"date\">".$sortieheure."</div>";
			}
			
			?>
		
						<div class="illustr">
						<?php
							if(strlen($image)>0)
							{
								if (strpos($image, "http://www.culture-provence-baronnies.fr") !== false)
								{
								    // image distante
								    echo "<a href=\"".$image."\" class=\"agrandir\">";
								    echo "<img src=\"".$image."\" width=\"150\" width=\"150\" />";
									if(!empty($copyright_img)) echo "<div class=\"legende\"> &copy; ".$copyright_img."</div>";
								    echo "</a>";
								}
								else
								{
									// Image locale
	
									// Image stockée sur la version de dev ou sur le site en prod ?
									$img_avec_lien = true;
									$fileUrl = $root_site.$image;
									$AgetHeaders = @get_headers($fileUrl);
									if (preg_match("|200|", $AgetHeaders[0])) {
										// fichier existant
										$root_site_d = $root_site;
									} else {
										// fichier non existant => on essaie l'autre chemin
										if ($root_site == $root_site_dev)
											$root_site_d = $root_site_prod;
										else
											$root_site_d = $root_site_dev;
										// Image toujours non existante
										$fileUrl = $root_site_d.$image;
										$AgetHeaders = @get_headers($fileUrl);
										if (!(preg_match("|200|", $AgetHeaders[0]))) {
											// fichier inexistant => img par défaut
											$image = "img/logo-ensembleici_fb.jpg"; 
											$img_avec_lien = false;
										}
									}
								
									if ($img_avec_lien)
									{
										echo "<a href=\"".$root_site_d.$image."\" class=\"agrandir\" title=\"".$titre;
										if(!empty($copyright_img)) echo " &copy; $copyright_img";
										echo "\">";
									}
									echo "<img src=\"".$root_site_d."miniature.php?uri=".$image."&method=fit&w=150&h=150\">";
									if(!empty($copyright_img)) echo "<div class=\"legende\"> &copy; ".$copyright_img."</div>";
									if ($img_avec_lien) echo "</a>";
								}
							}
							else
							{
								echo "<img src=\"img/logo-ensembleici_fb.jpg\" width=\"150\">";
							}
						?>
						</div>
						<?php
							// Petite annonce monétaire ?
							if($pa_monetaire)
							{
								echo "<div id=\"monetaire\" class=\"prix_pa\">";
								if(($pa_prix)&&($pa_prix!="0.00"))
								{
									// Formatage du prix
									echo FormatPrix($pa_prix);
									echo "<img src=\"img/monetaire.png\" alt=\"Annonce monétaire\" />";
								}
								echo "</div>";
							}
						?>

						<div class="coordonnees">
						<?php
							if(strlen($facebook)>0)
							{
								if(substr($facebook, 0, 4)!="http")
								{
									$facebook="http://".$facebook;
								}
								echo "<strong><a href=\"".$facebook."\" target=\"_blank\">Accès à la page Facebook <img src=\"img/lien_externe_gris.png\" alt=\"Ouvrir le lien dans une nouvelle fenêtre\" /></a></strong><br/>";
							}
							if(strlen($site)>0)
							{
								if(substr($site, 0, 4)!="http")
								{
									$site="http://".$site;
								}
								if(strlen($site)>50)
									$site_aff = $rest = substr($site,0,50)."...";
								else
									$site_aff = $site;

								echo "<p><strong><a href=\"".$site."\" target=\"_blank\" title=\"Accès au site internet\">$site_aff <img src=\"img/lien_externe_gris.png\" alt=\"Ouvrir le lien dans une nouvelle fenêtre\" /></a></strong></p>";
							}

							if($telephone)
								echo "<strong> Tél. : </strong>".FormatTel($telephone);
							// Tél 2
							if($telephone2)
								echo "<strong> ou </strong>".FormatTel($telephone2);
							echo "<br/>";
							if(strlen($fax)>0)
							{
								$fax = str_replace(array(";", " ", "-"), ".", $fax);
								echo "<strong>Fax. : </strong>".$fax."<br/>";
							}
							if($email)
							{
								echo "<script type=\"text/javascript\">
								  $(document).ready(function(){

								// colorbox contact
								$(\".ajax\").live('click', function() {
									$.fn.colorbox({
									  data:{type:'$type',no:$no},
									  href:\"messagerie.php\",
									  width:\"550px\",
									  onClosed:function(){
										$(\"#msg\").load(\"01_include/message.php\", function(){
											if ($(\"#msg\").html())
												$(\"#msg\").slideDown().delay(3500).slideUp();
										});
									  },
									  onComplete : function() {
										$(this).colorbox.resize();
									  }
									});
									return false; 
								});


								    /*
								    $('.ajax').colorbox({
									width:'550px',
									data:{type:'$type',no:$no},
									onComplete : function() {
									    $(this).colorbox.resize();
									}
								    });
								    */

								  });
								  </script>";
								echo "<strong>Email : </strong><a href=\"#\" title=\"Envoyer un courriel\" class=\"infobulle-b ajax\">Envoyer un courriel</a><br/>";
							}
							if(strlen($nom_contact)>0)
							{
								echo "<strong>Personne contact : </strong>".$nom_contact."<br/>";
							}
							if(strlen($nomadresse)>0)
							{
								echo "<strong>Lieu : </strong>".$nomadresse."<br/>";
							}
							if(($nom_ville)||($cp_ville)||(strlen($adresse)>0)||(strlen($adresse_comp)>0))
							{
								echo "<strong>Adresse :</strong> ";
								if(strlen($adresse)>0)
								{
									echo $adresse."<br/>";
								}
								if(strlen($adresse_comp)>0)
								{
									echo $adresse_comp."<br/>";
								}
								echo $cp_ville." ".$nom_ville."<br/>";
								
								if(strlen($evt_heure_debut)>0)
								{
									$sortieheure = substr($evt_heure_debut,0,-3);
									if (strlen($evt_heure_fin)>0) {$sortieheure .= " - ".substr($evt_heure_fin,0,-3);}									
									echo $sortieheure." <br/>";
								}
							}
							
						?>
						</div>
			   
						
						<div class="clear-r"></div>
			    <div class="description">
							<?php
							// supprimer les style ci dessous
							$description = preg_replace("/font-family\:.+?;/i", "", $description);
							$description = preg_replace("/font-family\:.+?\"/i", "", $description);
	
							$description = preg_replace("/background-color\:.+?;/i", "", $description);
							$description = preg_replace("/line-height\:.+?;/i", "", $description);
							// Supprimer les commentaires html (word)
							$description = preg_replace('/<!--.*?-->/s', '', $description);
							echo ucfirst($description) ?>
						</div>
						<?php
							if(strlen($description_comp)>0)
							{
						?>
						
						<div class="description">
							<?php
							// supprimer les style ci dessous
							$description_comp = preg_replace("/font-family\:.+?;/i", "", $description_comp);
							$description_comp = preg_replace("/font-family\:.+?\"/i", "", $description_comp);
							$description_comp = preg_replace("/background-color\:.+?;/i", "", $description_comp);
							$description_comp = preg_replace("/line-height\:.+?;/i", "", $description_comp);
							// Supprimer les commentaires html (word)
							$description_comp = preg_replace('/<!--.*?-->/s', '', $description_comp);
							echo ucfirst($description_comp) ?>
						</div>
						<?php
						}
						
						if(!$etape_validation)
						{
							?>
                            
						<div class="blocD bdactions">
							<h3>Actions <em>- Cliquez sur archiver pour activer / désactiver l’enregistrement sur votre espace personnel.</em></h3>
							<div class="contenu">
								<div class="clear"></div>
							    <div id="bloc_aime_previsu">
								<?php 
								
								$cette_page = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
						
								// Préparation favoris (archive)								
								$page="previsu";
								$no_occurence = $no;
								$type_objet = $type;
								$id_utilisateur = $_SESSION['UserConnecte_id'];
	
								if($_GET['action'] == "ajoutfav")
								{
									// le client est identifié ?
									if(($id_utilisateur) && ($no_occurence) && ($type_objet)){ 
										
										// le visiteur à déja archivé cet objet aujourd'hui ?
										$sql_testfav="SELECT id_fav FROM `favori` WHERE no_utilisateur='".$id_utilisateur."' AND no_occurence='".$no_occurence."' AND type_fav='".$type_objet."'";
										$res_testfav = $connexion->prepare($sql_testfav);
										$res_testfav->execute();
										$t_testfav = $res_testfav->rowCount();
										if ($t_testfav <= 0 && $action!="supprime")
										{
											// si autorisé à l'archiver, on enregistre dans la BDD 
											$sql_userfav = "INSERT INTO favori(no_utilisateur, no_occurence, type_fav) VALUES(:no_utilisateur, :no_occurence, :type_fav);";
											$ajout_userfav = $connexion->prepare($sql_userfav);
											$ajout_userfav->execute(array(':no_utilisateur'=>$id_utilisateur,':no_occurence'=>$no_occurence,':type_fav'=>$type_objet)) or die ("Erreur ".__LINE__.".");
										}
									}
								}
									
							    // Coup de coeur (like interne)
								if ($type != "petiteannonce") 
									require ('01_include/ajout_like.php');

							    // Archive (Favoris)
								require ('01_include/ajout_fav.php');
							
								if ($type != "petiteannonce")
								{
									// bouton modifier
									if ($estAnnonce) $annonce_en_get = "&annonce=1";
									echo "<form action='auto_".$type."_etape1.php?no_orig=".$no."&mode_modification=1".$annonce_en_get."' method='POST' class='form-enligne'>";
									// echo "<input type='hidden' name='no_orig' value='$no' />";
									// echo "<input type='hidden' name='mode_modification' value='1' />";
									echo "<button type='submit' class='boutonbleu ico-modifier bouton-avecdautres'>Modifier</button>";
									echo "</form>";
								}
								?>

								</div>
								<iframe src="//www.facebook.com/plugins/like.php?href=<?php echo $cette_page; ?>&amp;send=false&amp;layout=standard&amp;width=560&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:560px; height:24px;" allowTransparency="true"></iframe>
							</div>
	                           
						</div>
						
						<?php
						}
						
						// Tags / sous-tags
						if ((count($tab_ss_tag)) OR ($tab_vie_libelle[0]['titre']!=""))
						{
						?>
						
						<div class="blocD">
							<h3>Activités</h3>
							<div class="contenu">
								<?php 
			
								if ((!empty($id_vie))&&(!empty($id_tag)))
								{
									// vie et tag passés en get pour provenance
									echo "<div class=\"tag\">".$tab_vie_libelle[0]['libelle']." > ".$tab_tag_libelle[0]['titre']."</div>";
								}
								else
								{
									if($type=="structure")
									{
										for($indice_ss_tag=0; $indice_ss_tag<count($tab_ss_tag); $indice_ss_tag++)
										{
											$sql_ss_tag_libelle="SELECT S.titre AS titre_sstag, A.titre AS titre_tag
														FROM sous_tag S, tag_sous_tag T, tag A
														WHERE S.no=:no
														AND S.no = T.no_sous_tag
														AND T.no_tag = A.no";
											$res_ss_tag_libelle = $connexion->prepare($sql_ss_tag_libelle);
											$res_ss_tag_libelle->execute(array(':no'=>$tab_ss_tag[$indice_ss_tag]['no_sous_tag'])) or die ("Erreur ".__LINE__.".");
											$tab_ss_tag_libelle=$res_ss_tag_libelle->fetchAll(); 
											echo "<div class=\"tag\">".ucfirst($tab_ss_tag_libelle[0]['titre_tag']);
											echo " > ".ucfirst($tab_ss_tag_libelle[0]['titre_sstag'])."</div>";
											echo "<div class=\"descr-tag\">".ucfirst($tab_ss_tag[$indice_ss_tag]['description'])."</div>";
										}
									}
									else
									// Evenement + Petite annonce
									{
										for($indice_ss_tag=0; $indice_ss_tag<count($tab_ss_tag); $indice_ss_tag++)
										{
											//libelle tag
											$sql_tag_libelle="SELECT * FROM tag WHERE no=:no";
											$res_tag_libelle = $connexion->prepare($sql_tag_libelle);
											$res_tag_libelle->execute(array(':no'=>$tab_ss_tag[$indice_ss_tag]['no_tag'])) or die ("Erreur ".__LINE__.".");
											$tab_tag_libelle=$res_tag_libelle->fetchAll();
											
											//libelle vie
											$sql_vie_libelle="SELECT * FROM vie, vie_tag WHERE vie_tag.no_tag=:no AND vie.no=vie_tag.no_vie";
											$res_vie_libelle = $connexion->prepare($sql_vie_libelle);
											$res_vie_libelle->execute(array(':no'=>$tab_ss_tag[$indice_ss_tag]['no_tag'])) or die ("Erreur ".__LINE__.".");
											$tab_vie_libelle=$res_vie_libelle->fetchAll(); 
											
											echo "<div class=\"tag\">".$tab_vie_libelle[0]['libelle']." > ".$tab_tag_libelle[0]['titre']."</div>";
										}
									}
								}
								?>
							</div>
						</div>
						<?php
						}
						
						    // Affiche les liaisons
						    // Paramètre : $id_source et $type_source
						    $type_source = $type;
						    $id_source = $no;
						    require("ajax_liaisons_affiche.php");
		
				// Flux RSS CEDER
				if($type == "structure" && $tab_e["nom"] == "CEDER")
				{
					require_once("01_include/fonctions_rss.php");
					echo "<div id=\"bloc_rss\">";
					echo "<h2>Actualité du CEDER</h2>";
					echo RSS_Display("http://www.ceder-provence.fr/spip.php?page=backend", 8);
					echo "</div>";
				}
				?>
                        
			</div>
	<script type="text/javascript">
	$(function() {
		$(".ferme-colorbox").live("click", function(){
			$.colorbox.close();
		});

		$('.ico-like').click(function() {
			if (!$(this).hasClass("desactive"))
			{
				var obj = $(this).get();
				var id_recu = $(this).attr('rel');
				var type = $(this).attr("id");
				
				var urlpage = $(this).attr("href");
				
				var nb_like = $(this).attr("name");
				param = 'id_recu='+id_recu+'&type='+type+'&nb_like='+nb_like+'&urlpage='+urlpage;
				
				$(this).addClass('desactive');
				<?php // attention, ajouter aussi un case à ajax_like.php ?>

				type_aimez = "Coups de coeur";
				
				$.colorbox({
				  href:"ajax_like.php",
				  width:"550px",
				  data:param,
				  onComplete : function() {
					$(this).colorbox.resize();
					nb_like++;
					$(obj).text(type_aimez+" ("+nb_like+")");
					$(obj).attr("name",nb_like);
					$(obj)[0].title = type_aimez;
					$(obj).poshytip({
						content: type_aimez,
						showOn: 'none'
					});
				  }
				});						
			}
			return false;	
		});
		
		$('.ico-fav').live("click", function(){
			var obj = $(this).get();
			var id_recu = $(this).attr('rel');
			var type = $(this).attr("id");
			var urlpage = $(this).attr("href");
			param = 'id_recu='+id_recu+'&type='+type+'&urlpage='+urlpage+'&page=previsu';
			param2 = param.concat('&action=supprime') ;
			
			if ((!$(this).hasClass("desactive")) && (!$(this).hasClass("supprime")))
			{
					$(this).addClass('desactive');
					$(this).addClass('supprime');
				<?php // attention, ajouter aussi un case à ajax_like.php ?>
				switch (type) {
					case "structure":
					var type_fav = "Supprimer cette structure de vos archives";
					break;
					case "evenement":
					var type_fav = "Supprimer cet évènement de vos archives";
					break;
				}
				
				$.colorbox({
				  href:"ajax_fav.php",
				  width:"550px",
				  data:param,
				  onComplete : function() {
					$(this).colorbox.resize();
					$(obj)[0].title = type_fav;
					//$(obj)[0].text = type_fav;
					$(obj).poshytip({
						content: type_fav,
						showOn: 'none'
					});
				  }
				});						
			}else if($(this).hasClass("supprime")){
				<?php // attention, ajouter aussi un case à ajax_like.php ?>
				$(this).removeClass("desactive");
				$(this).removeClass("supprime");
				switch (type) {
					case "structure":
					var type_fav = "Ajouter cette structure aux favoris";
					break;
					case "evenement":
					var type_fav = "Ajouter cet évènement aux favoris";
					break;
					case "petiteannonce":
					var type_fav = "Ajouter cette petite annonce aux favoris";
					break;
				}
				$.colorbox({
				  href:"ajax_fav.php",
				  width:"550px",
				  data:param2,
				  onComplete : function() {
					$(this).colorbox.resize();
					$(obj)[0].title = type_fav;
					//$(obj)[0].text = type_fav;
					$(obj).poshytip({
						content: type_fav,
						showOn: 'yes'
					});
				  }
				});
			}

			return false;	
		});
		
	});
	</script>

	<?php
		}
	?>
	
	      </div>

<?php
	}
	else
	{
		// désactivé(e) ou inexistant(e)
		header("location:index.php");
		exit();
	}
$affiche_articles = true;
$affiche_publicites = true;
include ('01_include/structure_colonne3.php');

include ('01_include/structure_footer.php');

}
else
{
	// Erreur type ou no
	header("location:index.php");
	exit();
}
?>