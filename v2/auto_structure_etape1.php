<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "structure";
	$structure_nom="";
	$structure_sous_titre="";
	$structure_description="";
	$structure_statut=0;
	
	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_str'])
	  $mode_modification = intval($_SESSION['mode_modification_str']);
	if ($mode_modification)
	{
		$action_page = "Modifier";
		$_SESSION['mode_modification_str'] = "1";
	}
	else
	{
		$action_page = "Ajouter";
	}

	//retour sur une etape precedente
	
	if($_SESSION['no_structure']!=0)
	{
		$no_structure=$_SESSION['no_structure'];
		$sql_structure="SELECT * FROM structure WHERE no=:no";
		$res_structure = $connexion->prepare($sql_structure);
		$res_structure->execute(array(':no'=>$no_structure)) or die ("requete ligne 14 : ".$sql_structure);
		$tab_structure_modif=$res_structure->fetchAll();
		
		$structure_nom=$tab_structure_modif[0]['nom'];
		$structure_sous_titre=$tab_structure_modif[0]['sous_titre'];
		$structure_description=$tab_structure_modif[0]['description'];
		$structure_statut=$tab_structure_modif[0]['no_statut'];
	}
	else
	{
		//on demande la modification d'une fiche existante
		if((isset($_REQUEST['no_orig'])&&$_REQUEST['no_orig']>0))
		{
			//modification d'une fiche existante donc copie des donnees actuelles dans la table structure_temp
			$_SESSION['no_structure'] =$_REQUEST['no_orig'];
			//recuperation des informations de la structure dans la base de donn&eacute;es
			$sql_structure="SELECT * FROM structure WHERE no=:no";
			$res_structure = $connexion->prepare($sql_structure);
			$res_structure->execute(array(':no'=>$_REQUEST['no_orig'])) or die ("requete ligne 24 : ".$sql_structure);
			$tab_structure_modif=$res_structure->fetchAll();
			
			$structure_nom=$tab_structure_modif[0]['nom'];
			$structure_sous_titre=$tab_structure_modif[0]['sous_titre'];
			$structure_description=$tab_structure_modif[0]['description'];
			$structure_statut=$tab_structure_modif[0]['no_statut'];
			
			//insertion des donn&eacute;es dans la table temp
			$sql_insere_structure = "INSERT INTO structure_temp (nom, sous_titre, no_statut, description, url_logo, copyright, site_internet, facebook, no_utilisateur_creation, date_creation, nomadresse, adresse, adresse_complementaire, telephone, telephone2, fax, email, no_ville, etat) VALUES 
			(:nom, :sous_titre, :no_statut, :description, :url_logo, :copyright, :site_internet, :facebook, :no_utilisateur_creation, :date_creation, :nomadresse, :adresse, :adresse_complementaire, :telephone, :telephone2, :fax, :email, :no_ville, :etat)";
			$insert = $connexion->prepare($sql_insere_structure);
			//print_r($tab_structure_modif[0]);
			$insert->execute(array(':nom'=>$tab_structure_modif[0]['nom'], ':sous_titre'=>$tab_structure_modif[0]['sous_titre'], ':no_statut'=>$tab_structure_modif[0]['no_statut'], ':description'=>$tab_structure_modif[0]['description'], ':url_logo'=>$tab_structure_modif[0]['url_logo'], ':copyright'=>$tab_structure_modif[0]['copyright'], ':site_internet'=>$tab_structure_modif[0]['site_internet'], ':facebook'=>$tab_structure_modif[0]['facebook'], ':no_utilisateur_creation'=>$tab_structure_modif[0]['no_utilisateur_creation'], ':date_creation'=>$tab_structure_modif[0]['date_creation'], ':nomadresse'=>$tab_structure_modif[0]['nomadresse'], ':adresse'=>$tab_structure_modif[0]['adresse'], ':adresse_complementaire'=>$tab_structure_modif[0]['adresse_complementaire'], ':telephone'=>$tab_structure_modif[0]['telephone'], ':telephone2'=>$tab_structure_modif[0]['telephone2'], ':fax'=>$tab_structure_modif[0]['fax'], ':email'=>$tab_structure_modif[0]['email'], ':no_ville'=>$tab_structure_modif[0]['no_ville'], ':etat'=>$tab_structure_modif[0]['etat'])) or die ("requete ligne 50 : ".$sql_insere_structure);
			$no = $connexion->lastInsertId();
			
			//mise à jour de la structure validation = 2 en cours de modification
			$maj_structure_etape1 = "UPDATE structure SET validation=2 WHERE no=:no";
			$maj_structure = $connexion->prepare($maj_structure_etape1);
			$maj_structure->execute(array(':no'=>$_SESSION['no_structure'])) or die ("Erreur ".__LINE__." : ".$maj_structure_etape1);
			
			//recuperation des donn&eacute;es alternatives
			//contact
			$sql_structure_contact="SELECT * FROM structure_contact WHERE no_structure=:no";
			$res_structure_contact = $connexion->prepare($sql_structure_contact);
			$res_structure_contact->execute(array(':no'=>$_REQUEST['no_orig'])) or die ("Erreur ".__LINE__." : ".$sql_structure_contact);
			$tab_structure_contact=$res_structure_contact->fetchAll();
			//activite
			$sql_structure_activite="SELECT * FROM structure_sous_tag WHERE no_structure=:no";
			$res_structure_activite = $connexion->prepare($sql_structure_activite);
			$res_structure_activite->execute(array(':no'=>$_REQUEST['no_orig'])) or die ("Erreur ".__LINE__." : ".$sql_structure_activite);
			$tab_structure_activite=$res_structure_activite->fetchAll();
			//liaisons
			$sql_liaison_A="SELECT * FROM `liaisons` E WHERE type_A=:type AND no_A=:no";
			$sql_liaison_B="SELECT * FROM `liaisons` E WHERE type_B=:type AND no_B=:no";
			$sql_liaison = "($sql_liaison_A) UNION ($sql_liaison_B)";
			$res_liaison = $connexion->prepare($sql_liaison);
			$res_liaison->execute(array(':type'=>$type,':no'=>$_REQUEST['no_orig'])) or die ("Erreur ".__LINE__." : ".$sql_liaison);
			$tab_liaison=$res_liaison->fetchAll();
						
			//insertion des informations dans la table structure_modification
			$structure_modification = "INSERT INTO structure_modification (no_structure, no_utilisateur, no_structure_temp, date_modification) VALUES 
			(:no_structure, :no_utilisateur, :no_structure_temp, NOW())";
			$insert = $connexion->prepare($structure_modification);
			$insert->execute(array(':no_structure'=>$tab_structure_modif[0]['no'], ':no_utilisateur'=>$_SESSION['UserConnecte_id'], ':no_structure_temp'=>$no)) or die ("Erreur ".__LINE__." : ".$structure_modification);

			// Sauvegardes des versions
			
			// Sauvegarde contact
			for($indice_contact=0; $indice_contact<count($tab_structure_contact); $indice_contact++)
			{
				// Recup infos du contact principal
				$sql_contact_a = "SELECT * FROM `contact` WHERE no=:no_contact";
				$res_contact_a = $connexion->prepare($sql_contact_a);
				$res_contact_a->execute(array(':no_contact'=>$tab_structure_contact[$indice_contact]["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_contact_a);
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
						$structure_modification = "INSERT INTO contact_modification (no_contact, no_utilisateur, no_contact_temp, type_referent, no_referent_temp, date_modification) VALUES 
						(:no_contact, :no_utilisateur, :no_contact_temp, :type_referent, :no_referent_temp, NOW())";
						$insert = $connexion->prepare($structure_modification);
						$insert->execute(array(':no_contact'=>$tab_structure_contact[$indice_contact]["no_contact"], ':no_utilisateur'=>$_SESSION['UserConnecte_id'], ':no_contact_temp'=>$no_contact_temp, ':type_referent'=>$type, ':no_referent_temp'=>$no)) or die ("Erreur ".__LINE__." : ".$structure_modification);
					}
				}
			}

			// Sauvegarde activite (ou soustag)
			for($indice_activite=0; $indice_activite<count($tab_structure_activite); $indice_activite++)
			{
				$insertion_structure_activite = "INSERT INTO structure_sous_tag_temp (no_structure_temp, no_sous_tag, description) VALUES 
				(:no_structure, :no_activite, :description)";
				$insert = $connexion->prepare($insertion_structure_activite);
				$insert->execute(array(':no_structure'=>$no, ':no_activite'=>$tab_structure_activite[$indice_activite]['no_sous_tag'], ':description'=>$tab_structure_activite[$indice_activite]['description'])) or die ("requete ligne 66 : ".$insertion_structure_activite);
			}

			// Sauvegarde liaisons avec l'id de la sauvegarde en référence
			for($indice_liaison=0; $indice_liaison<count($tab_liaison); $indice_liaison++)
			{
				$sql_ajt = "INSERT INTO `liaisons_temp`
					(`no`, `type_A` , `no_A` , `type_B` , `no_B`, `no_temp`, `date_creation`)
						VALUES
					(NULL, :type_A, :no_A, :type_B, :no_B, :no_temp, NOW())";
				$insert_ajt = $connexion->prepare($sql_ajt);
				$insert_ajt->execute(array(':type_A'=>$tab_liaison[$indice_liaison]['type_A'], ':no_A'=>$tab_liaison[$indice_liaison]['no_A'], ':type_B'=>$tab_liaison[$indice_liaison]['type_B'], ':no_B'=>$tab_liaison[$indice_liaison]['no_B'], ':no_temp'=>$no)) or die ("Erreur ".__LINE__." : ".$sql_ajt);
			}
			
		}
		else
		{
			// creation d'une nouvelle entree dans la table structure

			$keyword_temp=$_REQUEST['keyword'];
			//$keyword_temp="test";
			//cr&eacute;ation d'une nouvelle entr&eacute;e dans la table structure
			$sql_structures = "INSERT INTO structure (nom, date_creation, no_utilisateur_creation, etat) VALUES (:nom, NOW(), :num_utilisateur, 0)";
			$insert = $connexion->prepare($sql_structures);
			$insert->execute(array(':nom'=>$keyword_temp, ':num_utilisateur'=>$_SESSION['UserConnecte_id'])) or die ("requete ligne 98 : ".$sql_structures);
			$no = $connexion->lastInsertId();
			$no_temp=0;
			//recuperation des informations de la structure dans la base de donn&eacute;es
			$sql_structure="SELECT * FROM structure WHERE no=:no";
			$res_structure = $connexion->prepare($sql_structure);
			$res_structure->execute(array(':no'=>$no)) or die ("requete ligne 104 : ".$sql_structure);
			$tab_structure_modif=$res_structure->fetchAll();
			
			$structure_nom=$tab_structure_modif[0]['nom'];
			$structure_sous_titre=$tab_structure_modif[0]['sous_titre'];
			$structure_description=$tab_structure_modif[0]['description'];
			$structure_statut=$tab_structure_modif[0]['no_statut'];
			
			$_SESSION['no_structure'] =$no;

			// On supprime le mode modification précédemment enregistré
			unset($_SESSION['mode_modification_str']);
			// Idem pour l'étape à laquelle on s'est arrêtés
			unset($_SESSION['etape_arret_form_str']);

		}
	}
	
	//on recupere l'intégtralité des statuts
	$sql_statut="SELECT * FROM statut ORDER BY libelle";
	$res_statut = $connexion->prepare($sql_statut);
	$res_statut->execute() or die ("requete ligne 97 : ".$sql_statut);
	$tab_statut=$res_statut->fetchAll();
	
	// include header
	$titre_page .= $action_page." une structure - Etape 1";
	$titre_page_bleu = " ";
	$meta_description = $action_page." une structure sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header .= "<script type='text/javascript' src=\"js/recherche_ajax.js\"></script>
	<script type=\"text/javascript\" src=\"js/ckeditor/ckeditor.js\"></script>
	<script type=\"text/javascript\">
		window.onload = function()
		{
			CKEDITOR.replace('description',{toolbar:'Auto',uiColor:'#F0EDEA',language:'fr',width:'560',height:'200',skin:'kama',enterMode : CKEDITOR.ENTER_BR});
		};
	</script>";
	include ('01_include/structure_header.php');
?>
<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
function verif_structure_etape1()
{
	var txt_description = CKEDITOR.instances.description.getData();
	var txt_description = jQuery.trim(txt_description);
	if(!test_champ_vide(document.EIForm.nom.value))
	{
		alert("Veuillez saisir le nom de votre structure !!");
		document.EIForm.nom.focus();
		return false;
	}
	else
	{
		if(document.EIForm.statut.value==0)
		{
			alert("Vous devez choisir un statut pour votre structure !!");
			document.EIForm.statut.focus();
			return false;
		}
		else
		{
			if(txt_description.length<10)
			{
				alert ("Vous devez saisir une description (plus de 10 caractères) pour votre structure !!");
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
        <div id="formstructure" class="blocB">

			<h1><?php echo $action_page; ?> une structure - Etape 1</h1>

			<?php require('01_include/structure_etapes_form.php'); ?>

			<div id="aide_icone">
				<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
			</div>
				<form name="EDretour" id="EDretour" action="ajout_structure.php" method="post" accept-charset="UTF-8">
					<input type="hidden" value="<?php echo $_SESSION['no_structure'] ?>" name="no_orig">
					<button type="submit" class="boutonbleu ico-flecheretour">Retour</button>
				</form>

				<div id="aide_contenu">
					<p>
					Afin de profiter au mieux de ce service, nous vous conseillons, dans un premier temps, de rassembler toutes les informations permettant de r&eacute;aliser votre fiche structure / activit&eacute;.<br/><br/>
					Voici les &eacute;l&eacute;ments qui vous seront demand&eacute;s :<br/>
					Seuls les &eacute;l&eacute;ments suivi d'une * sont &agrave; fournir obligatoirement<br/>
					<ul>
						<li>nom <span class="note">*</span></li>
						<li>un texte de pr&eacute;sentation <span class="note">*</span></li>
						<li>petite image (logo de la structure, photo &eacute;quipe,…)</li>
						<li>adresse</li>
						<li>num&eacute;ro de t&eacute;l&eacute;phone et de fax</li>
						<li>votre email <span class="note">*</span></li>
					</ul>
					</p>
				</div>
					<form name="EIForm" id="EIForm" action="auto_structure_etape2.php" method="post" class="formA" accept-charset="UTF-8" onSubmit="return verif_structure_etape1()">
					<fieldset>

					<label>Nom <sup>*</sup> :</label><input type="text" name="nom" value="<?php echo htmlspecialchars($structure_nom) ?>" class="validate[required]"/><br/><br/>
					<label>Sous titre :</label><input type="text" name="sous_titre" value="<?php echo htmlspecialchars($structure_sous_titre) ?>"/><br/><br/>
					<label>Statut <sup>*</sup> :</label>
					<select name="statut" class="validate[required]">
						<option value="0" selected></option>
						<?php
							for($indice_statut=0;$indice_statut<count($tab_statut); $indice_statut++)
							{
								if($tab_statut[$indice_statut]['no']==$structure_statut)
								{
									echo "<option value=\"".$tab_statut[$indice_statut]['no']."\" selected>".$tab_statut[$indice_statut]['libelle']."</option>";
								}
								else
								{
									echo "<option value=\"".$tab_statut[$indice_statut]['no']."\" >".$tab_statut[$indice_statut]['libelle']."</option>";
								}
							
							}						
						?>
					</select><br/><br/>
					</fieldset>

					<label class="large">Description <sup>*</sup> :</label><br/><br/>
					
					<textarea id="description" name="description" class="validate[required]">
					<?php
						echo $structure_description;
					?>
					</textarea>
					<input type=hidden name="no_structure_post" value="<?php echo $_SESSION['no_structure']?>">
					<input type=hidden name="provenance" value="etape1">
					
					<?php if ($mode_modification)
					echo "\n<input type='hidden' name='mode_modification' value='1' />\n";
					 ?>
					
					<br/><br/>
					<div class="actions"><button type="submit" class="boutonbleu ico-fleche">Suite (étape 2)</button></div>
					
					<br/><br/>
					</form>
				<div id="resultats"></div>
					
					</fieldset>
				
				
		</div>
		<div class="clear"></div>
      </div>
		<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
			$(document).ready(function() {
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