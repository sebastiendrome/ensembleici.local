<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	include('01_include/fonction_redim_image.php');
	$type = "petiteannonce";

	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_pa'])
	  $mode_modification = intval($_SESSION['mode_modification_pa']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";

	if($_REQUEST['provenance']=="etape3")
	{
		// Vérifications saisie ville étape précédente
		$rech_idville = intval($_POST['rech_idville']);
		if (!$rech_idville) $erreur .= "<br/>Vous devez sélectionner une ville.";
	}
	
	$no_pa=$_SESSION['no_pa'];

	if((isset($_POST['no_orig'])&&$_POST['no_orig']>0))
		$no_pa = $_SESSION['no_pa'] = $_POST['no_orig'];

	if (!$no_pa)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	if($_POST['ajout_contact'] == "ok")
	{

		// vérif de l'url
		if (($_POST['site_internet'])&&($_POST['site_internet']!="http://"))
		{
			$_POST['site_internet'] = htmlspecialchars($_POST['site_internet']);
			if (preg_match("#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i",$_POST['site_internet']))
				$site_internet_valid = $_POST['site_internet'];
			else
				$erreur .= "<br/>Site internet invalide.";
		}
		
		// Cases à cocher afficher les numéros de tel
		if (intval($_POST['afficher_mob']))
			$afficher_mob = 1;
		else
			$afficher_mob = 0;
		
		if (intval($_POST['afficher_tel']))
			$afficher_tel = 1;
		else
			$afficher_tel = 0;
		

		// insertion des données propre à la pa
		$maj_pa_etape1 = "UPDATE `petiteannonce` SET rayonmax=:rayonmax, afficher_mob=:afficher_mob, afficher_tel=:afficher_tel, no_ville=:no_ville, site=:site_internet WHERE no=:no";
		$maj_pa = $connexion->prepare($maj_pa_etape1);
		$maj_pa->execute(array(':rayonmax'=>$_POST['rayonmax'], ':afficher_mob'=>$afficher_mob, ':afficher_tel'=>$afficher_tel, ':no_ville'=>$rech_idville, ':site_internet'=>$site_internet_valid, ':no'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$insertion_pa_etape1);
		
		// saisie d'un contact
		if($_POST['mobile'] || $_POST['email'] || $_POST['telephone'])
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
				$sql_contact_transfert = "SELECT * FROM `petiteannonce_contact`
							WHERE no_petiteannonce=:no_pa
							AND no_contact=:no_contact";
				$res_contact_transfert = $connexion->prepare($sql_contact_transfert);
				$res_contact_transfert -> execute(array(':no_pa'=>$no_pa,':no_contact'=>$no_ancien_contact)) or die ("Erreur ".__LINE__." : ".$sql_contact_transfert);
				$tab_contact_transfert = $res_contact_transfert->fetchAll();

				// Test si différent de ce qui a été saisi
				if (($tab_contact[0]['nom'] != $_POST['nom_contact'])||($tab_contact[0]['email'] != $_POST['email'])||($tab_contact[0]['telephone'] != $_POST['telephone'])||($tab_contact[0]['mobile'] != $_POST['mobile']))
				{
					foreach ($tab_contact_transfert as $ct) {

						// MAJ de la table contact
						$sql_maj_contact="UPDATE `contact` SET
									nom=:nom,
									email=:email,
									mobile=:mobile,
									telephone=:telephone
									WHERE no=:no_contact";
						$maj_contact = $connexion->prepare($sql_maj_contact);
						$maj_contact->execute(array(':nom'=>$_POST['nom_contact'], ':email'=>$_POST['email'], ':mobile'=>$_POST['mobile'], ':telephone'=>$_POST['telephone'],':no_contact'=>$ct["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_maj_contact);
					}
				}
			}
			else
			{
				// Ajout d'un contact : Insère les nouvelles infos saisies
				$sql_contact = "INSERT INTO contact (nom, mobile, email, telephone) VALUES (:nom_contact, :mobile, :email, :telephone)";
				$insert = $connexion->prepare($sql_contact);
				$insert->execute(array(':nom_contact'=>$_POST['nom_contact'], ':mobile'=>$_POST['mobile'], ':email'=>$_POST['email'], ':telephone'=>$_POST['telephone'])) or die ("Erreur ".__LINE__." : ".$sql_contact);
				$no_contact = $connexion->lastInsertId();

				$sql_contact_pa = "INSERT INTO petiteannonce_contact (no_petiteannonce, no_contact) VALUES (:no_pa, :no_contact)";
				$insert = $connexion->prepare($sql_contact_pa);
				$insert->execute(array(':no_pa'=>$no_pa, ':no_contact'=>$no_contact)) or die ("Erreur ".__LINE__." : ".$sql_contact_pa);
			}
		}
	} // Fin ajout_contact OK
	
	$sql_pa="SELECT * FROM petiteannonce WHERE no=:no";
	$res_pa = $connexion->prepare($sql_pa);
	$res_pa->execute(array(':no'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$sql_pa);
	$tab_pa_modif=$res_pa->fetchAll();
	
	// include header
	$titre_page = $action_page." une petite annonce - Etape 4";
	$meta_description = $action_page." une petite annonce sur Ensemble ici : Tous acteurs de la vie locale";
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

			// Loader pour upload image
			$("#load").fadeOut();
			$("#contenu").fadeIn();
			$("#btonenvoyer").click(function() {
				$("#load").fadeToggle();
			});

		});


		</script>
AJHE;
	include ('01_include/structure_header.php');
?>	
	<div id="colonne2">
		<div id="load">
			<img src="img/image-loader.gif" alt="Chargement en cours" />
		</div>

		<div id="formpa" class="blocB">
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
					<h2 class="titreA">ILLUSTRATION / PHOTO</h2>
			<div id="aide_contenu">
					
					Vous pouvez illustrer votre petite annonce avec une photo. Elle ne doit pas d&eacute;passer 1Mo.<br/>
					Pour cela, il vous suffit de cliquer sur le bouton [parcourir] et de choisir une image sur votre ordinateur.<br/><br/>
					
					Pour valider votre enregistrement, cliquer sur le bouton [Envoyer l'image]. Vous serez directement dirigé à la prévisualisation de votre annonce.
			</div>
					<p><strong>A savoir :</strong> Une annonce avec photo est 7 fois plus consultée qu'une annonce sans photo.</p>

					<form name="EDconnexion" id="EDconnexion" action="auto_previsu.php?no_fiche=<?php echo $_SESSION['no_pa']; ?>&type=petiteannonce&etape_validation=1&ajout_image=1" method="post" class="formA" accept-charset="UTF-8" enctype="multipart/form-data">
					<fieldset>
					<?php
						$repertoire="/02_medias/09_petiteannonce/";
						if(strlen($tab_pa_modif[0]['url_image'])>0)
						{
							echo "<div class=\"illustr\"><a href=\"".$root_site.$tab_pa_modif[0]['url_image']."\" class=\"agrandir\" title=\"$titre\">";
							echo "<img src=\"".$root_site."miniature.php?uri=".$tab_pa_modif[0]['url_image']."&method=fit&w=150&h=150\">";
							echo "<a id=\"".$_SESSION['no_pa']."\" href=\"#\" class=\"delete_image\"><img src=\"img/cancelbutton.gif\" alt=\"Supprimer l'image\" title=\"Supprimer l'image\" class=\"icone\" /></a>";

							echo "</a></div>";
						}
					?>
					<label for="image_logo">Image : <sup>(max. 1 Mo)</sup></label>
					<input type="file" name="image_logo" /><br/>
					<br/>
					<center><button type="submit" id="btonenvoyer" class="boutonbleu ico-ajout">Envoyer l'image</button></center>
					</fieldset>
					</form>

					<br/><br/>
					<form name="EDretour" id="EDretour" action="auto_previsu.php?no_fiche=<?php echo $_SESSION['no_pa']; ?>&type=petiteannonce&etape_validation=1" method="post" class="formA" accept-charset="UTF-8">
					<input type="hidden" value="" name="imagesupprime" id="imagesupprime">
					<input type="hidden" value="<?php echo $_SESSION['no_pa'] ?>" name="no_orig">
					<?php
					if ($mode_modification)
						echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
					?>
					<div class="actions"><button type="submit" class="boutonbleu ico-fleche">Suite (étape 5<?php if($tab_pa_modif[0]['url_image']=="") echo ", sans image)"; else echo ")"; ?>
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