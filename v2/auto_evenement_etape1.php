<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "evenement";
	$evenement_nom="";
	$evenement_sous_titre="";
	$evenement_description="";
	$evenement_genre=0;
	

	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_evt'])
	  $mode_modification = intval($_SESSION['mode_modification_evt']);
	if ($mode_modification)
	{
		$_SESSION['mode_modification_evt'] = 1;
		$action_page = "Modifier";
	}
	else
		$action_page = "Ajouter";

	//retour sur une &eacute;tape pr&eacute;c&eacute;dente
	if($_SESSION['no_evenement']!=0)
	{
		$no_evenement=$_SESSION['no_evenement'];
		$sql_evenement="SELECT * FROM evenement WHERE no=:no";
		$res_evenement = $connexion->prepare($sql_evenement);
		$res_evenement->execute(array(':no'=>$no_evenement)) or die ("requete ligne 14 : ".$sql_evenement);
		$tab_evenement_modif=$res_evenement->fetchAll();
		
		$evenement_nom=$tab_evenement_modif[0]['titre'];
		$evenement_sous_titre=$tab_evenement_modif[0]['sous_titre'];
		$evenement_description=$tab_evenement_modif[0]['description'];
		$evenement_genre=$tab_evenement_modif[0]['no_genre'];
		$evenement_description_complementaire=$tab_evenement_modif[0]['description_complementaire'];
	}
	else
	{
		
		//on demande la modification d'une fiche existante
		if((isset($_REQUEST['no_orig'])&&$_REQUEST['no_orig']>0))
		{
			//modification d'une fiche existante donc copie des donnees actuelles dans la table evenement_temp
			$_SESSION['no_evenement'] = $_REQUEST['no_orig'];
			//recuperation des informations de la evenement dans la base de donnees
			$sql_evenement="SELECT * FROM evenement WHERE no=:no";
			$res_evenement = $connexion->prepare($sql_evenement);
			$res_evenement->execute(array(':no'=>$_REQUEST['no_orig'])) or die ("requete ligne 24 : ".$sql_evenement);
			$tab_evenement_modif=$res_evenement->fetchAll();
			
			$evenement_nom=$tab_evenement_modif[0]['titre'];
			$evenement_sous_titre=$tab_evenement_modif[0]['sous_titre'];
			$evenement_description=$tab_evenement_modif[0]['description'];
			$evenement_genre=$tab_evenement_modif[0]['no_genre'];
			
			//insertion des donn&eacute;es dans la table temp
			$sql_insere_evenement = "INSERT INTO evenement_temp (date_debut, date_fin, titre, sous_titre, no_genre, description, description_complementaire, url_image, site, email, telephone, no_utilisateur_creation, date_creation, adresse, no_ville, etat) VALUES 
			(:date_debut, :date_fin, :titre, :sous_titre, :no_genre, :description, :description_complementaire, :url_image, :site, :email, :telephone, :no_utilisateur_creation, :date_creation, :adresse, :no_ville, :etat)";
			$insert = $connexion->prepare($sql_insere_evenement);
			//print_r($tab_evenement_modif[0]);
			$insert->execute(array(':date_debut'=>$tab_evenement_modif[0]['date_debut'], ':date_fin'=>$tab_evenement_modif[0]['date_fin'], ':titre'=>$tab_evenement_modif[0]['titre'], ':sous_titre'=>$tab_evenement_modif[0]['sous_titre'], ':no_genre'=>$tab_evenement_modif[0]['no_genre'], ':description'=>$tab_evenement_modif[0]['description'], ':description_complementaire'=>$tab_evenement_modif[0]['description_complementaire'], ':url_image'=>$tab_evenement_modif[0]['url_image'], ':site'=>$tab_evenement_modif[0]['site'], ':email'=>$tab_evenement_modif[0]['email'], ':telephone'=>$tab_evenement_modif[0]['telephone'], ':no_utilisateur_creation'=>$tab_evenement_modif[0]['no_utilisateur_creation'], ':date_creation'=>$tab_evenement_modif[0]['date_creation'], ':adresse'=>$tab_evenement_modif[0]['adresse'], ':no_ville'=>$tab_evenement_modif[0]['no_ville'],':etat'=>$tab_evenement_modif[0]['etat'])) or die ("requete ligne 50 : ".$sql_insere_evenement);
			$no = $connexion->lastInsertId();
			
			//mise à jour de l'evenement validation = 2 en cours de modification
			$maj_event_etape1 = "UPDATE evenement SET validation=2 WHERE no=:no";
			$maj_event = $connexion->prepare($maj_event_etape1);
			$maj_event->execute(array(':no'=>$tab_evenement_modif[0]['no'])) or die ("Erreur ".__LINE__." : ".$maj_event_etape1);
						
			//recuperation des donn&eacute;es alternatives
			//contact
			$sql_evenement_contact="SELECT * FROM evenement_contact WHERE no_evenement=:no";
			$res_evenement_contact = $connexion->prepare($sql_evenement_contact);
			$res_evenement_contact->execute(array(':no'=>$_REQUEST['no_orig'])) or die ("Erreur ".__LINE__." : ".$sql_evenement_contact);
			$tab_evenement_contact=$res_evenement_contact->fetchAll();
			//activite
			$sql_evenement_activite="SELECT * FROM evenement_tag WHERE no_evenement=:no";
			$res_evenement_activite = $connexion->prepare($sql_evenement_activite);
			$res_evenement_activite->execute(array(':no'=>$_REQUEST['no_orig'])) or die ("Erreur ".__LINE__." : ".$sql_evenement_activite);
			$tab_evenement_activite=$res_evenement_activite->fetchAll();
			//liaisons
			$sql_liaison_A="SELECT * FROM `liaisons` E WHERE type_A=:type AND no_A=:no";
			$sql_liaison_B="SELECT * FROM `liaisons` E WHERE type_B=:type AND no_B=:no";
			$sql_liaison = "($sql_liaison_A) UNION ($sql_liaison_B)";
			$res_liaison = $connexion->prepare($sql_liaison);
			$res_liaison->execute(array(':type'=>$type,':no'=>$_REQUEST['no_orig'])) or die ("Erreur ".__LINE__." : ".$sql_liaison);
			$tab_evenement_liaison=$res_liaison->fetchAll();
			
			//insertion des informations dans la table evenement_modification
			$evenement_modification = "INSERT INTO evenement_modification (no_evenement, no_utilisateur, no_evenement_temp, date_modification) VALUES 
			(:no_evenement, :no_utilisateur, :no_evenement_temp, NOW())";
			$insert = $connexion->prepare($evenement_modification);
			$insert->execute(array(':no_evenement'=>$tab_evenement_modif[0]['no'], ':no_utilisateur'=>$_SESSION['UserConnecte_id'], ':no_evenement_temp'=>$no)) or die ("Erreur ".__LINE__." : ".$evenement_modification);

			// Sauvegardes des versions

			// Sauvegarde contact
			for($indice_contact=0; $indice_contact<count($tab_evenement_contact); $indice_contact++)
			{
				// Recup infos du contact principal
				$sql_contact_a = "SELECT * FROM `contact` WHERE no=:no_contact";
				$res_contact_a = $connexion->prepare($sql_contact_a);
				$res_contact_a->execute(array(':no_contact'=>$tab_evenement_contact[$indice_contact]["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_contact_a);
				$ct_a = $res_contact_a->fetchAll();
				$ancien_contact_existe = $res_contact_a->rowCount();
				
				// Insertion comme contact historique dans contact_temp
				if ($ancien_contact_existe)
				{
					// Insertion nouveau contact en sauvegarde
					$sql_contact_h = "INSERT INTO `contact_temp` (nom, email, telephone) VALUES (:nom, :email, :telephone)";
					$insert_h = $connexion->prepare($sql_contact_h);
					$insert_h->execute(array(':nom'=>$ct_a[0]["nom"], ':email'=>$ct_a[0]["email"], ':telephone'=>$ct_a[0]['telephone'])) or die ("Erreur ".__LINE__." : ".$sql_contact_h);
					$no_contact_temp = $connexion->lastInsertId();
					
					// Insertion d'une modification
					if ($no_contact_temp)
					{
						$evenement_modification = "INSERT INTO contact_modification (no_contact, no_utilisateur, no_contact_temp, type_referent, no_referent_temp, date_modification) VALUES 
						(:no_contact, :no_utilisateur, :no_contact_temp, :type_referent, :no_referent_temp, NOW())";
						$insert = $connexion->prepare($evenement_modification);
						$insert->execute(array(':no_contact'=>$tab_evenement_contact[$indice_contact]["no_contact"], ':no_utilisateur'=>$_SESSION['UserConnecte_id'], ':no_contact_temp'=>$no_contact_temp, ':type_referent'=>$type, ':no_referent_temp'=>$no)) or die ("Erreur ".__LINE__." : ".$evenement_modification);
					}
				}
			}

			// Sauvegarde activité ou tag
			for($indice_activite=0; $indice_activite<count($tab_evenement_activite); $indice_activite++)
			{
				$insertion_evenement_activite = "INSERT INTO evenement_tag_temp (no_evenement_temp, no_tag) VALUES 
				(:no_evenement, :no_activite)";
				$insert = $connexion->prepare($insertion_evenement_activite);
				$insert->execute(array(':no_evenement'=>$no, ':no_activite'=>$tab_evenement_activite[$indice_activite]['no_tag'])) or die ("Erreur ".__LINE__." : ".$insertion_evenement_activite);
			}
			
			// Sauvegarde liaisons avec l'id de la sauvegarde en référence
			for($indice_liaison=0; $indice_liaison<count($tab_evenement_liaison); $indice_liaison++)
			{
				$sql_ajt = "INSERT INTO `liaisons_temp`
					(`no`, `type_A` , `no_A` , `type_B` , `no_B`, `no_temp`, `date_creation`)
						VALUES
					(NULL, :type_A, :no_A, :type_B, :no_B, :no_temp, NOW())";
				$insert_ajt = $connexion->prepare($sql_ajt);
				$insert_ajt->execute(array(':type_A'=>$tab_evenement_liaison[$indice_liaison]['type_A'], ':no_A'=>$tab_evenement_liaison[$indice_liaison]['no_A'], ':type_B'=>$tab_evenement_liaison[$indice_liaison]['type_B'], ':no_B'=>$tab_evenement_liaison[$indice_liaison]['no_B'], ':no_temp'=>$no)) or die ("Erreur ".__LINE__." : ".$sql_ajt);
			}
		}
		else
		{
			// creation d'une nouvelle entree dans la table evenement

			$keyword_temp=$_REQUEST['keyword'];
			$sql_evenements = "INSERT INTO evenement (titre, date_creation, no_utilisateur_creation, etat) VALUES (:titre, NOW(), :num_utilisateur, 0)";
			$insert = $connexion->prepare($sql_evenements);
			$insert->execute(array(':titre'=>$keyword_temp, ':num_utilisateur'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$sql_evenements);
			$no = $connexion->lastInsertId();
			$no_temp=0;
			//recuperation des informations de l'evenement dans la BDD
			$sql_evenement="SELECT * FROM evenement WHERE no=:no";
			$res_evenement = $connexion->prepare($sql_evenement);
			$res_evenement->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__." : ".$sql_evenement);
			$tab_evenement_modif=$res_evenement->fetchAll();
			
			$evenement_nom=$tab_evenement_modif[0]['titre'];
			$evenement_sous_titre=$tab_evenement_modif[0]['sous_titre'];
			$evenement_description=$tab_evenement_modif[0]['description'];
			$evenement_genre=$tab_evenement_modif[0]['no_genre'];
			$evenement_description_complementaire=$tab_evenement_modif[0]['description_complementaire'];
			
			$_SESSION['no_evenement'] =$no;

			// On supprime le mode modification précédemment enregistré
			unset($_SESSION['mode_modification_evt']);
			// Idem pour l'étape à laquelle on s'est arrêtés
			unset($_SESSION['etape_arret_form_evt']);
		}
	}
	$evenement_description_complementaire=$tab_evenement_modif[0]['description_complementaire'];
	
	
	/***
		Nouvelles modifications, apportées par Sam
		**/
	$sql_genre_annonce = "SELECT * FROM genre WHERE type_genre='A' ORDER BY libelle";
	$sql_genre_evenemnt = "SELECT * FROM genre WHERE type_genre='E' ORDER BY libelle";
	
	$res_genre_annonce = $connexion->prepare($sql_genre_annonce);
	$res_genre_annonce->execute() or die ("Erreur 97 : ".$sql_genre_annonce);
	$tab_genre_annonce=$res_genre_annonce->fetchAll();
	
	$res_genre_evenement = $connexion->prepare($sql_genre_evenemnt);
	$res_genre_evenement->execute() or die ("Erreur 97 : ".$sql_genre_evenemnt);
	$tab_genre_evenement=$res_genre_evenement->fetchAll();
	
	
		// Evnement normal
		$type_accentue = "évènement";
		$un_accentue = "un évènement";
		

	// include header
	$titre_page = $action_page." ".$un_accentue." - Etape 1".$complement_titre_page;
	$titre_page_bleu = " ";
	$meta_description = $action_page." ".$un_accentue." sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header .= "<script type='text/javascript' src=\"js/recherche_ajax.js\"></script><script type=\"text/javascript\" src=\"js/ckeditor/ckeditor.js\"></script><script type=\"text/javascript\">
window.onload = function()
{	
	CKEDITOR.replace('description',{toolbar:'Auto',uiColor:'#F0EDEA',language:'fr',width:'560',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
	CKEDITOR.replace('description_comp',{toolbar:'Auto',uiColor:'#F0EDEA',language:'fr',width:'560',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
};</script>".
	<<<AJHE
AJHE;
	include ('01_include/structure_header.php');

?>
<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
function verif_evenement_etape1()
{
	var txt_description = CKEDITOR.instances.description.getData();
	var txt_description = jQuery.trim(txt_description);
	
	if(!test_champ_vide(document.EIForm.nom.value))
	{
		alert("Veuillez saisir le titre de votre <?php echo $type_accentue; ?> !!");
		document.EIForm.nom.focus();
		return false;
	}
	else
	{
		if(document.EIForm.annonce.value==-1&&document.EIForm.genre.value==0)
		{
			alert("Vous devez choisir un genre pour votre <?php echo $type_accentue; ?> !!");
			document.EIForm.genre.focus();
			return false;
		}
		else
		{
			if(txt_description.length<10)
			{
				alert ("Vous devez saisir une description (plus de 10 caractères) pour votre <?php echo $type_accentue; ?> !!");
				document.EIForm.description.focus();
				return false;
			}
			else
			{
				return true;
			}
		}
	}
}

$(document).ready(function() {
	// Aide
	$('#aide_icone').click(function() {
	  $('#aide_contenu').slideToggle('slow', function() {
	  });
	});
});
</script>		
      <div id="colonne2">                  
        <div id="formevent" class="blocB">
			<h1><?php echo $titre_page; ?></h1>

			<?php require('01_include/structure_etapes_form.php'); ?>
				
				<div id="aide_icone">
					<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
				</div>
			
				
				<form name="EDretour" id="EDretour" action="<?php
					echo "ajouter_un_evenement.html"; ?>" method="post" accept-charset="UTF-8">
					<input type="hidden" value="<?php echo $_SESSION['no_evenement'] ?>" name="no_orig">
					<button type="submit" class="boutonbleu ico-flecheretour">Retour</button>
				</form>

				<div id="aide_contenu">
					<p>Afin de profiter au mieux de ce service, nous vous conseillons, dans un premier temps, de rassembler toutes les informations permettant de r&eacute;aliser votre fiche événement.<br/><br/>
					Voici les &eacute;l&eacute;ments qui vous seront demand&eacute;s : (seuls les &eacute;l&eacute;ments suivi d'une <sup class="note">*</sup> sont obligatoires)<br/><br/>
					<ul>
						<li>Qu’est-ce que l’événement ?<br/>
							<ul>
								<li>Titre de l’événement / manifestation, atelier / stage / cours <sup class="note">*</sup></li> 
								<li>Un texte de présentation <sup class="note">*</sup></li>
								<li>Visuel (affiche, logo, photo équipe,…)</li>
								<li>Adresse <sup class="note">*</sup></li>
								<li>Numéro de téléphone </li>
								<li>Email <sup class="note">*</sup></li>
							</ul>
						</li>
						<li>Où se passe-t-il ?<br/>
							<ul>
								<li>Le nom du lieu</li>
								<li>Une petite description du lieu</li>
								<li>Les coordonnées du lieu</li>
							</ul>
						</li>
						<li>Qui l’organise ?<br/>
							<ul>
								<li>Le nom de la structure</li>
								<li>Une petite description de la structure</li>
								<li>L'adresse de la structure et ses contacts</li>
						</li>
					</ul>
					</p>
				</div>

					<form name="EIForm" id="EIForm" action="auto_evenement_etape2.php" method="post" class="formA" accept-charset="UTF-8" onSubmit="return verif_evenement_etape1()">
					<fieldset>
					<label>Titre de l’événement <sup>*</sup> :</label><input type="text" name="nom" value="<?php echo htmlspecialchars($evenement_nom) ?>" class="validate[required]" id="nom"/><br/><br/>
					<label>Sous titre :</label><input type="text" name="sous_titre" value="<?php echo htmlspecialchars($evenement_sous_titre) ?>"/><br/><br/>
					<label>Quel type d'événement? <sup>*</sup> :</label>
					<select name="annonce" class="validate[required]" onchange="if(this.value==-1)document.getElementById('select_genres').style.display='inline-block';else document.getElementById('select_genres').style.display='none';">
						<option value="0" selected></option>
						<option value="-1">&Eacute;v&eacute;nements</option>
						<?php
							for($indice_statut=0;$indice_statut<count($tab_genre_annonce); $indice_statut++)
							{
								if($tab_genre_annonce[$indice_statut]['no']==$evenement_genre)
								{
									echo "<option value=\"".$tab_genre_annonce[$indice_statut]['no']."\" selected>".$tab_genre_annonce[$indice_statut]['libelle']."</option>";
								}
								else
								{
									echo "<option value=\"".$tab_genre_annonce[$indice_statut]['no']."\" >".$tab_genre_annonce[$indice_statut]['libelle']."</option>";
								}
							
							}						
						?>
					</select>
					<select name="genre" class="validate[required]" id="select_genres" style="display:none;">
						<option value="0" selected></option>
						<?php
							for($indice_statut=0;$indice_statut<count($tab_genre_evenement); $indice_statut++)
							{
								if($tab_genre_evenement[$indice_statut]['no']==$evenement_genre)
								{
									echo "<option value=\"".$tab_genre_evenement[$indice_statut]['no']."\" selected>".$tab_genre_evenement[$indice_statut]['libelle']."</option>";
								}
								else
								{
									echo "<option value=\"".$tab_genre_evenement[$indice_statut]['no']."\" >".$tab_genre_evenement[$indice_statut]['libelle']."</option>";
								}
							
							}						
						?>
					</select>
					</fieldset>
					<label class="labellarge">Proposez-nous un texte descriptif - présentant votre événement <sup>*</sup> :</label><br/><br/>
					<textarea id="description" name="description" class="validate[required]">
					<?php
						echo $evenement_description;
					?>
					</textarea>
					<br/><br/>
					<label class="labellarge">Les renseignements complémentaires importants  :</label>
					<div class="note">Indiquez le tarif, les horaires ou toute information utile à la réservation ou à la périodicité.<br/>(Nous vous demanderons de préciser le lieu et l’adresse ultérieurement)</div>	
					<textarea id="description_comp" name="description_comp">
					<?php
						echo $evenement_description_complementaire;
					?>
					</textarea>
					<input type=hidden name="no_evenement_post" value="<?php echo $_SESSION['no_evenement']?>">
					<input type=hidden name="provenance" value="etape1">
					<?php if ($mode_modification)
					echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
					 ?>
					<br/><br/>
					<div class="actions"><button type="submit" class="boutonbleu ico-fleche">Suite (étape 2)</button></div>
					
					<br/><br/>
					</form>
				<div id="resultats"></div>
					
				
		</div>
		<div class="clear"></div>
      </div>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#nom").focus();
			    // Validation form
			    $("#EIForm").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			});
		</script>
<?php

$affiche_articles = true;
$affiche_publicites = true;
include ('01_include/structure_colonne3.php');

include ('01_include/structure_footer.php');
?>