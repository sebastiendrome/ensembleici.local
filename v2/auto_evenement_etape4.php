<?php
	session_name("EspacePerso");
	session_start();

	$no_evenement=$_SESSION['no_evenement'];
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	include('01_include/fonction_redim_image.php');
	$type = "evenement";
	
	if($_REQUEST['provenance']=="etape3")
	{
		// Vérifications saisies étape précédente
		$rech_idville = intval($_POST['rech_idville']);
		if (!$rech_idville) $erreur .= "<br/>Vous devez sélectionner une ville.";
		if (!$_POST['date_debut']) $erreur .= "<br/>Vous devez sélectionner une date d'évenement.";
	}
	
	
	
	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_evt'])
	  $mode_modification = intval($_SESSION['mode_modification_evt']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";
	
	if((isset($_POST['no_orig'])&&$_POST['no_orig']>0))
	{
		$_SESSION['no_evenement']=$_POST['no_orig'];
		$no_evenement=$_SESSION['no_evenement'];
	}

	if (!$no_evenement)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	if($_POST['ajout_contact']=="ok")
	{
		$tab_date_debut=explode("/", $_POST['date_debut']);
		$date_debut_mysql=$tab_date_debut[2]."-".$tab_date_debut[1]."-".$tab_date_debut[0];
		
		$tab_date_fin=explode("/", $_POST['date_fin']);
		$date_fin_mysql=$tab_date_fin[2]."-".$tab_date_fin[1]."-".$tab_date_fin[0];
		
		// Ajout des heures
		$heure_debut_event=$_POST['heure_debut'];
		$heure_fin_event=$_POST['heure_fin'];	
		
		if ($heure_debut_event==""){$heure_debut_event=null;}
		if ($heure_fin_event==""){$heure_fin_event=null;}

		// regex de l'heure
		if($heure_debut_event!=null){
			if (preg_match("#^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$#", $heure_debut_event)){
			  $heure_debut = $heure_debut_event; 
			}
			else{
				$erreur .= "Erreur : L'heure de début n'est pas au format (HH:MM)<br/>";	
				$heure_debut = null;
				$_POST['heure_debut']=null;
				}
		}
		else{$heure_debut=null;}
		
		if($heure_fin_event!=null){
			if (preg_match("#^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$#", $heure_fin_event)){
			  $heure_fin = $heure_fin_event; 
			}
			else{
				$erreur .= "Erreur : L'heure de fin n'est pas au format (HH:MM)<br/>";		
				$heure_fin = null;
				$_POST['heure_fin']=null;
			}	
		}
		else{$heure_fin=null;}
		
		if( ($heure_fin!=null) && ($heure_debut!=null) && (($date_debut_mysql) == ($date_fin_mysql)) && (strtotime($heure_debut_event) >= strtotime($heure_fin_event)) ){
			$erreur .= "</br> L'heure de fin doit être superieur à l'heure de début<br/>";
			$heure_fin = null;
			$_POST['heure_fin'] = null;
		}
		
		if(($heure_fin!=null) && ($heure_debut==null)){
			$erreur .= "</br> Veuillez aussi indiquer une heure de début<br/>";
			$heure_fin = null;
			$_POST['heure_fin'] = null;
		}
		
		$heure_debut_mysql = $heure_debut;
		$heure_fin_mysql = $heure_fin;
		
		// vérif de l'url
		if (($_POST['site_internet'])&&($_POST['site_internet']!="http://"))
		{
			$_POST['site_internet'] = htmlspecialchars($_POST['site_internet']);
			if (preg_match("#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i",$_POST['site_internet']))
				$site_internet_valid = $_POST['site_internet'];
			else
				$erreur .= "<br/>Site internet invalide.";
		}

		//insertion des données propre à la evenement
		$maj_evenement_etape1 = "UPDATE `evenement` SET nomadresse=:nomadresse, adresse=:adresse, no_ville=:no_ville, site=:site_internet, telephone=:telephone, telephone2=:telephone2, email=:email, date_debut=:date_debut, date_fin=:date_fin, heure_debut=:heure_debut, heure_fin=:heure_fin WHERE no=:no";
		$maj_evenement = $connexion->prepare($maj_evenement_etape1);
		$maj_evenement->execute(array(':nomadresse'=>$_POST['nomadresse'], ':adresse'=>$_POST['adresse'], ':no_ville'=>$rech_idville, ':site_internet'=>$site_internet_valid, ':email'=>$_POST['email_evenement'], ':telephone'=>$_POST['telephone_evenement'], ':telephone2'=>$_POST['telephone2_evenement'],':date_debut'=>$date_debut_mysql, ':date_fin'=>$date_fin_mysql, ':heure_debut'=>$heure_debut_mysql, ':heure_fin'=>$heure_fin_mysql, ':no'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$insertion_evenement_etape1);
		
		// saisie d'un contact
		if($_POST['nom_contact'] || $_POST['email'] || $_POST['tel'])
		{
			// Une création ou une modif de contact ?
			$no_ancien_contact = intval($_POST['no_contact']);
			if($no_ancien_contact)
			{
				// Modification du contact
				
				// Infos de l'ancien contact
				$sql_contact="SELECT * FROM `contact` WHERE no=:no";
				$res_contact = $connexion->prepare($sql_contact);
				$res_contact->execute(array(':no'=>$no_ancien_contact)) or die ("Erreur ".__LINE__." : ".$sql_contact);
				$tab_contact=$res_contact->fetchAll();

				// Infos de l'association ancien contact
				$sql_contact_transfert = "SELECT * FROM `evenement_contact`
							WHERE no_evenement=:no_evenement
							AND no_contact=:no_contact";
				$res_contact_transfert = $connexion->prepare($sql_contact_transfert);
				$res_contact_transfert -> execute(array(':no_evenement'=>$no_evenement,':no_contact'=>$no_ancien_contact)) or die ("Erreur ".__LINE__." : ".$sql_contact_transfert);
				$tab_contact_transfert = $res_contact_transfert->fetchAll();

				// Test si différent de ce qui a été saisi
				if (($tab_contact[0]['nom'] != $_POST['nom_contact'])||($tab_contact[0]['email'] != $_POST['email'])||($tab_contact[0]['telephone'] != $_POST['tel']))
				{
					foreach ($tab_contact_transfert as $ct) {

						// MAJ de la table contact
						$sql_maj_contact="UPDATE `contact` SET
									nom=:nom,
									email=:email,
									telephone=:telephone
									WHERE no=:no_contact";
						$maj_contact = $connexion->prepare($sql_maj_contact);
						$maj_contact->execute(array(':nom'=>$_POST['nom_contact'], ':email'=>$_POST['email'], ':telephone'=>$_POST['tel'],':no_contact'=>$ct["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_maj_contact);
					}
				}

				// Le role a changé ?
				if ($tab_contact_transfert[0]['role'] != $_POST['role'])
				{
					// MAJ du rôle
					$sql_maj_role="UPDATE `evenement_contact` SET
								no_role=:no_role
								WHERE no_evenement=:no_evenement
								AND no_contact=:no_contact";
					$maj_role = $connexion->prepare($sql_maj_role);
					$maj_role->execute(array(':no_evenement'=>$no_evenement,':no_role'=>$_POST['role'],':no_contact'=>$no_ancien_contact)) or die ("Erreur ".__LINE__." : ".$sql_maj_role);
				}
			}
			else
			{
				// Ajout d'un contact : Insère les nouvelles infos saisies
				$sql_contact = "INSERT INTO contact (nom, email, telephone) VALUES (:nom, :email, :telephone)";
				$insert = $connexion->prepare($sql_contact);
				$insert->execute(array(':nom'=>$_POST['nom_contact'], ':email'=>$_POST['email'], ':telephone'=>$_POST['tel'])) or die ("Erreur ".__LINE__." : ".$sql_contact);
				$no_contact = $connexion->lastInsertId();

				$sql_contact_evenement = "INSERT INTO evenement_contact (no_evenement, no_contact, no_role) VALUES (:no_evenement, :no_contact, :no_role)";
				$insert = $connexion->prepare($sql_contact_evenement);
				$insert->execute(array(':no_evenement'=>$no_evenement, ':no_contact'=>$no_contact, ':no_role'=>$_POST['role'])) or die ("Erreur ".__LINE__." : ".$sql_contact_evenement);
			}			
		}
	}
	
	$sql_evenement="SELECT * FROM evenement WHERE no=:no";
	$res_evenement = $connexion->prepare($sql_evenement);
	$res_evenement->execute(array(':no'=>$no_evenement)) or die ("requete ligne 14 : ".$sql_evenement);
	$tab_evenement_modif=$res_evenement->fetchAll();
	
	// include header
	
		$titre_page = $action_page." un évènement - Etape 4";
		$meta_description = $action_page." un évènement sur Ensemble ici : Tous acteurs de la vie locale";
		
	$titre_page_bleu = " ";
	$ajout_header .= 
	<<<AJHE
		<script type="text/javascript">
		$(function() {
			// Agrandir l'image
			$(".agrandir").colorbox();

			// Aide
			$('#aide_icone').click(function() {
			  $('#aide_contenu').slideToggle('slow', function() {
			  });
			});
			
			// Suppression de l'image
			$(".delete_image").live('click', function() {
				$('#imagesupprime').val("oui"); 
				alert('Pour valider la suppression, cliquez sur "Suite".');
				$('.illustr').fadeOut("slow");
				return false;
			});
		});
		</script>		
AJHE;
	include ('01_include/structure_header.php');
?>	
	<div id="colonne2" class="page_ville">                  
		<div id="formevent" class="blocB">
			<h1><?php echo $titre_page; ?></h1>
			<?php require('01_include/structure_etapes_form.php');
				
				if (!empty($erreur))
				{
					echo "<p id=\"erreur\">$erreur</p>";
				}
				else
				{
				?>
			<div id="aide_icone">
				<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
			</div>
					<h1 class="titreA">Illustration / Logo</h1>
			<div id="aide_contenu">
					
					Vous pouvez illustrer votre événement avec une image. Elle ne doit pas d&eacute;passer 1Mo.<br/>
					Pour cela, il vous suffit de cliquer sur le bouton [parcourir] et de choisir une image sur votre ordinateur.<br/><br/>
					
					Pour valider votre enregistrement, cliquer sur le bouton [T&eacute;l&eacute;charger l'image]. Vous serez directement dirigé à la prévisualisation de votre événement.
			</div>

					<form name="EDconnexion" id="EDconnexion" action="auto_previsu.php?no_fiche=<?php echo $_SESSION['no_evenement']; ?>&type=evenement&etape_validation=1&ajout_image=1" method="post" class="formA" accept-charset="UTF-8" enctype="multipart/form-data">
					<fieldset>
						<p>Seules les annonces / événements ayant une illustration<br/> sont affichés sur la page d'accueil du site.</p>
					<?php
						$repertoire="/02_medias/04_evenement/";
						if(strlen($tab_evenement_modif[0]['url_image'])>0)
						{
							echo "<div class=\"illustr\"><a href=\"".$root_site.$tab_evenement_modif[0]['url_image']."\" class=\"agrandir\" title=\"$titre\">";
							echo "<img src=\"".$root_site."miniature.php?uri=".$tab_evenement_modif[0]['url_image']."&method=fit&w=150&h=150\">";
							echo "<a id=\"".$_SESSION['no_evenement']."\" href=\"#\" class=\"delete_image\"><img src=\"img/cancelbutton.gif\" alt=\"Supprimer l'image\" title=\"Supprimer l'image\" class=\"icone\" /></a>";

							echo "</a></div>";
						}
					?>
					<label for="image_logo">Image : <sup>(max. 1 Mo)</sup></label>
					<input type="file" name="image_logo" /><br/>
					<br/>
					<label for="copyright">Copyright &copy; :</label>
					<input type="text" name="copyright" value="<?php echo htmlspecialchars($tab_evenement_modif[0]['copyright']) ?>" size="35" /><br/>

					<br/>
					<center><button type="submit" class="boutonbleu ico-ajout">T&eacute;l&eacute;charger l'image</button></center>
					</fieldset>
					</form>

					<br/><br/>
					<form name="EDretour" id="EDretour" action="auto_previsu.php?no_fiche=<?php echo $_SESSION['no_evenement']; ?>&type=evenement&etape_validation=1" method="post" class="formA" accept-charset="UTF-8">
					<input type="hidden" value="" name="imagesupprime" id="imagesupprime">
					<input type="hidden" value="<?php echo $_SESSION['no_evenement'] ?>" name="no_orig">
					<?php
					if ($mode_modification)
						echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
					?>
					<div class="actions"><button type="submit" class="boutonbleu ico-fleche">Suite (étape 5<?php if($tab_evenement_modif[0]['url_image']=="") echo ", sans image)"; else echo ")"; ?>
					</button></div>
					</form><br/>
				<?php
				} // If !erreur
				?>

		</div>
		<div class="clear"></div>
      </div>
<?php

$affiche_articles = true;
$affiche_publicites = true;
include ('01_include/structure_colonne3.php');

include ('01_include/structure_footer.php');
?>