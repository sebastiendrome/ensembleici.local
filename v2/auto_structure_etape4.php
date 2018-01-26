<?php
	session_name("EspacePerso");
	session_start();

	$no_structure=$_SESSION['no_structure'];
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	include('01_include/fonction_redim_image.php');
	$type = "structure";

	// Vérifications saisies étape précédente
	if($_REQUEST['provenance']=="etape1")
	{
		$rech_idville = intval($_POST['rech_idville']);
		if (!$rech_idville) $erreur .= "<br/>Vous devez sélectionner une ville.";
	}
	
	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_str'])
	  $mode_modification = intval($_SESSION['mode_modification_str']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";

	if((isset($_POST['no_orig'])&&$_POST['no_orig']>0))
	{
		$_SESSION['no_structure']=$_POST['no_orig'];
		$no_structure=$_SESSION['no_structure'];
	}

	if (!$no_structure)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	if($_POST['ajout_contact']=="ok")
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

		// insertion des données propre à la structure
		$maj_structure_etape1 = "UPDATE structure SET nomadresse=:nomadresse, adresse=:adresse, adresse_complementaire=:adresse_complementaire, no_ville=:no_ville, site_internet=:site_internet, facebook=:facebook, telephone=:telephone, telephone2=:telephone2, fax=:fax, email=:email WHERE no=:no";
		$maj_structure = $connexion->prepare($maj_structure_etape1);
		$maj_structure->execute(array(':nomadresse'=>$_POST['nomadresse'], ':adresse'=>$_POST['adresse'], ':adresse_complementaire'=>$_POST['adresse_complementaire'], ':no_ville'=>$_POST['rech_idville'], ':site_internet'=>$site_internet_valid, ':facebook'=>$_POST['facebook'], ':email'=>$_POST['email_structure'], ':telephone'=>$_POST['telephone_structure'], ':telephone2'=>$_POST['telephone2_structure'], ':fax'=>$_POST['fax_structure'], ':no'=>$no_structure)) or die ("requete ligne 18 : ".$insertion_structure_etape1);

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
				$sql_contact_transfert = "SELECT * FROM `structure_contact`
							WHERE no_structure=:no_structure
							AND no_contact=:no_contact";
				$res_contact_transfert = $connexion->prepare($sql_contact_transfert);
				$res_contact_transfert -> execute(array(':no_structure'=>$no_structure,':no_contact'=>$no_ancien_contact)) or die ("Erreur ".__LINE__." : ".$sql_contact_transfert);
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
					$sql_maj_role="UPDATE `structure_contact` SET
								no_role=:no_role
								WHERE no_structure=:no_structure
								AND no_contact=:no_contact";
					$maj_role = $connexion->prepare($sql_maj_role);
					$maj_role->execute(array(':no_structure'=>$no_structure,':no_role'=>$_POST['role'],':no_contact'=>$no_ancien_contact)) or die ("Erreur ".__LINE__." : ".$sql_maj_role);
				}
			}
			else
			{
				// Ajout d'un contact : Insère les nouvelles infos saisies
				$sql_contact = "INSERT INTO contact (nom, email, telephone) VALUES (:nom, :email, :telephone)";
				$insert = $connexion->prepare($sql_contact);
				$insert->execute(array(':nom'=>$_POST['nom_contact'], ':email'=>$_POST['email'], ':telephone'=>$_POST['tel'])) or die ("Erreur ".__LINE__." : ".$sql_contact);
				$no_contact = $connexion->lastInsertId();

				$sql_contact_structure = "INSERT INTO structure_contact (no_structure, no_contact, no_role) VALUES (:no_structure, :no_contact, :no_role)";
				$insert = $connexion->prepare($sql_contact_structure);
				$insert->execute(array(':no_structure'=>$no_structure, ':no_contact'=>$no_contact, ':no_role'=>$_POST['role'])) or die ("Erreur ".__LINE__." : ".$sql_contact_structure);
			}
		}
	}
	
	$sql_structure="SELECT * FROM structure WHERE no=:no";
	$res_structure = $connexion->prepare($sql_structure);
	$res_structure->execute(array(':no'=>$no_structure)) or die ("requete ligne 14 : ".$sql_structure);
	$tab_structure_modif=$res_structure->fetchAll();

	// include header
	$titre_page = $action_page." une nouvelle structure - Etape 4";
	$titre_page_bleu = " ";
	$meta_description = $action_page." une structure sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header .= 
	<<<AJHE
		<script type="text/javascript">
		  $(document).ready(function(){
			$(".agrandir").colorbox({
		    });
		  });
		$(document).ready(function() {
			// Aide
			$('#aide_icone').click(function() {
			  $('#aide_contenu').slideToggle('slow', function() {
			  });
			});
		});
		</script>
AJHE;
	include ('01_include/structure_header.php');
?>	
      <div id="colonne2" class="page_ville">                  
        <div id="formstructure" class="blocB">

					<h1><?php echo $action_page; ?> une structure - Etape 4</h1>

			<?php require('01_include/structure_etapes_form.php'); ?>
			<div id="aide_icone">
				<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
			</div>
				<?php
				if (!empty($erreur))
				{
					echo "<p id=\"erreur\">$erreur</p>";
				}
				else
				{
				?>
					
					<form name="EIForm" id="EIForm" action="auto_previsu.php?no_fiche=<?php echo $_SESSION['no_structure']; ?>&type=structure&etape_validation=1&ajout_image=1" method="post" class="formA" accept-charset="UTF-8" enctype="multipart/form-data">
					<h1 class="titreA">Illustration / Logo</h1>
			<div id="aide_contenu">

					Vous pouvez illustrer votre fiche structure avec une illustration ou un logo. Elle ne doit pas d&eacute;passer 1Mo. Pour cela, il vous suffit de cliquer sur le bouton [parcourir…] et de choisir une image sur votre ordinateur.<br/><br/>
					Pour valider votre enregistrement, cliquer sur le bouton [T&eacute;l&eacute;charger Image]. Vous serez directement dirigé vers la prévisualisation de votre structure. 
			</div>

					<fieldset>
					<?php
						$repertoire="/02_medias/04_structure/";
						if(strlen($tab_structure_modif[0]['url_logo'])>0)
						{
							echo "<div class=\"illustr\"><a href=\"".$root_site.$tab_structure_modif[0]['url_logo']."\" class=\"agrandir\" title=\"$titre\">";
							echo "<img src=\"".$root_site."miniature.php?uri=".$tab_structure_modif[0]['url_logo']."&method=fit&w=150&h=150\">";
							echo "</a></div>";
						}
					?>
					<label>Image : <sup>(max. 1 Mo)</sup></label>
					<input type="file" name="image_logo" /><br/>
					<br/>
					<label>Copyright &copy; :</label>
					<input type="text" name="copyright" value="<?php echo htmlspecialchars($tab_structure_modif[0]['copyright']) ?>" /><br/>

					<br/>
					<center><button type="submit" class="boutonbleu ico-ajout">T&eacute;l&eacute;charger l'image</button></center>
					</fieldset>
					</form>
					
					<br/><br/>
					<form name="EDretour" id="EDretour" action="auto_previsu.php?no_fiche=<?php echo $_SESSION['no_structure']; ?>&type=structure&etape_validation=1" method="post" class="formA" accept-charset="UTF-8">
					<input type="hidden" value="<?php echo $_SESSION['no_structure'] ?>" name="no_orig">
					<?php if ($mode_modification)
					echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
					 ?>
					<div class="actions"><button type="submit" class="boutonbleu ico-fleche">Etape finale
					<?php if($tab_structure_modif[0]['url_logo']=="") echo " (sans image)"; ?>
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