<?php
	
	session_name("EspacePerso");
	session_start();
	$no_structure=$_SESSION['no_structure'];
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "structure";
	$sous_tag_modif ="";
	$sous_tag_description="";

	if($_REQUEST['provenance']=="etape1")
	{
		// Vérifications saisies étape précédente
		if (empty($_POST['nom'])) $erreur .= "<br/>Vous devez saisir un nom.";
		$statut = intval($_POST['statut']);
		if (!$statut) $erreur .= "<br/>Vous devez sélectionner un statut.";
		if (empty($_POST['description'])) $erreur .= "<br/>Vous devez saisir une description.";
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

		if (!$no_structure)
		{
				// désactivé(e) ou inexistant(e)
				header("location:index.php");
				exit();
		}
	}
	else
	{
		if (!$no_structure)
		{
				// désactivé(e) ou inexistant(e)
				header("location:index.php");
				exit();
		}

		//mise à jour des infos de l'etape 1
		if($_POST['no_structure_post']==$no_structure)
		{
			$maj_structure_etape1 = "UPDATE structure SET nom=:nom, sous_titre=:sous_titre, no_statut=:no_statut, description=:description WHERE no=:no";
			$maj_structure = $connexion->prepare($maj_structure_etape1);
			$maj_structure->execute(array(':nom'=>$_POST['nom'], ':sous_titre'=>$_POST['sous_titre'], ':no_statut'=>$_POST['statut'], ':description'=>$_POST['description'], ':no'=>$no_structure)) or die ("requete ligne 18 : ".$insertion_structure_etape1);
		}

		if($_GET['ajouter']=="ok")
		{
		
			$tab_tag_reponse=$_POST['sous_tag'];
			$no_vie_reponse=$_POST['form_vie'];
			if(count($tab_tag_reponse)>0)
			{
				for($indice_tag=0; $indice_tag<count($tab_tag_reponse); $indice_tag++)
				{
					$sql_test_clef="SELECT * FROM structure_sous_tag WHERE no_structure=:no_structure AND no_sous_tag=:no_sous_tag";
					$res_test_clef = $connexion->prepare($sql_test_clef);
					$res_test_clef->execute(array(':no_structure'=>$no_structure, ':no_sous_tag'=>$tab_tag_reponse[$indice_tag])) or die ("requete ligne 97 : ".$sql_test_clef);
					$tab_test_clef=$res_test_clef->fetchAll();
					if(count($tab_test_clef)==0)
					{
						$sql_structures = "INSERT INTO structure_sous_tag (no_structure, no_sous_tag, description) VALUES (:no_structure, :no_sous_tag, :description)";
						$insert = $connexion->prepare($sql_structures);
						$insert->execute(array(':no_structure'=>$no_structure, ':no_sous_tag'=>$tab_tag_reponse[$indice_tag], ':description'=>$_POST['description'])) or die ("requete ligne 24 : ".$sql_structures);
					}
				}
			}
			else
			{
				$sql_vie_tag="SELECT * FROM tag_sous_tag WHERE no_tag=:no_tag";
				$res_vie_tag = $connexion->prepare($sql_vie_tag);
				$res_vie_tag->execute(array(':no_tag'=>$no_vie_reponse)) or die ("requete ligne 97 : ".$sql_vie_tag);
				$tab_vie_tag=$res_vie_tag->fetchAll();
				
				for($indice_tag=0; $indice_tag<count($tab_vie_tag); $indice_tag++)
				{
					$sql_test_clef="SELECT * FROM structure_sous_tag WHERE no_structure=:no_structure AND no_sous_tag=:no_sous_tag";
					$res_test_clef = $connexion->prepare($sql_test_clef);
					$res_test_clef->execute(array(':no_structure'=>$no_structure, ':no_sous_tag'=>$tab_vie_tag[$indice_tag]['no_sous_tag'])) or die ("requete ligne 97 : ".$sql_test_clef);
					$tab_test_clef=$res_test_clef->fetchAll();
					if(count($tab_test_clef)==0)
					{
						$sql_structures = "INSERT INTO structure_sous_tag (no_structure, no_sous_tag, description) VALUES (:no_structure, :no_sous_tag, :description)";
						$insert = $connexion->prepare($sql_structures);
						$insert->execute(array(':no_structure'=>$no_structure, ':no_sous_tag'=>$tab_vie_tag[$indice_tag]['no_sous_tag'], ':description'=>$_POST['description'])) or die ("requete ligne 24 : ".$sql_structures);
					}
				}
			}
		}
		
		if($_GET['supprimer']=="ok")
		{
			$sql_delete_sous_tag="DELETE FROM structure_sous_tag WHERE no_sous_tag=:no_sous_tag AND no_structure=:no_structure";
			$delete_sous_tag = $connexion->prepare($sql_delete_sous_tag);
			$delete_sous_tag->execute(array(':no_sous_tag'=>$_GET['no_sous_tag'], ':no_structure'=>$no_structure)) or die ("requete ligne 35 : ".$sql_delete_sous_tag);
		}
	}
	
	$sql_structure="SELECT * FROM structure WHERE no=:no";
	$res_structure = $connexion->prepare($sql_structure);
	$res_structure->execute(array(':no'=>$no_structure)) or die ("requete ligne 14 : ".$sql_structure);
	$tab_structure_modif=$res_structure->fetchAll();
	
	//on recupere les sous tag lié à cette structure
	$sql_sous_tag_structure="SELECT * FROM structure_sous_tag WHERE no_structure=:no_structure ORDER BY no_sous_tag";
	$res_sous_tag_structure = $connexion->prepare($sql_sous_tag_structure);
	$res_sous_tag_structure->execute(array(':no_structure'=>$no_structure)) or die ("requete ligne 41 : ".$sql_sous_tag_structure);
	$tab_sous_tag_structure=$res_sous_tag_structure->fetchAll();
	
	//on recupere l'int&eacute;gtralit&eacute; des sous_tag
	$sql_sous_tag="SELECT * FROM sous_tag ORDER BY titre";
	$res_sous_tag = $connexion->prepare($sql_sous_tag);
	$res_sous_tag->execute() or die ("requete ligne 97 : ".$sql_sous_tag);
	$tab_sous_tag=$res_sous_tag->fetchAll();
	
	//mise en place tag / ss-tag
	$sql_tag="SELECT * FROM tag ORDER BY titre";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("requete ligne 97 : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();
	
	// include header
	$titre_page = $action_page." une structure - Etape 2";
	$titre_page_bleu = " ";
	$meta_description = $action_page." une structure sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header .= <<<AJHE
	<script type='text/javascript' src="js/recherche_ajax.js"></script>
	<script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#load').hide();
	CKEDITOR.replace('description',{toolbar:'Auto',uiColor:'#F0EDEA',language:'fr',width:'560',height:'100',skin:'kama',enterMode : CKEDITOR.ENTER_BR});

});

$(function() {
	$(".delete").click(function() {
		$('#load').fadeIn();
		var commentContainer = $(this).parents('tr:first');
		var id = $(this).attr("id");
		var no_structure = $no_structure;
		var string = "no_sous_tag=" + id + "&" + "no_structure=" + no_structure;
			
		$.ajax({
		   type: "POST",
		   url: "ajax_supprimer_activite.php",
		   data: string,
		   cache: false,
		   success: function(){
			commentContainer.fadeOut("slow", function(){\$(this).remove();} );
			$('#load').fadeOut();
		  }
		});
		
		return false;
	});
});
</script>
AJHE;
	include ('01_include/structure_header.php');
?>
<script type="text/javascript" src="js/fonction_auto_presentation.js"></script>
<script type="text/javascript">
function verif_structure_etape2()
{
	var txt_description = CKEDITOR.instances.description.getData();
	var txt_description = jQuery.trim(txt_description);
	if(document.EIForm.form_vie.value==0)
	{
		alert("Vous devez choisir au moins une activité pour votre structure !!");
		document.EIForm.form_vie.focus();
		return false;
	}
	else
	{
		document.getElementById('indice_tag').value
		if(document.getElementById('form_tag_'+document.getElementById('indice_tag').value).value==0)
		{
			alert("Vous devez choisir au moins une sous activité pour votre structure !!");
			document.getElementById('form_tag_'+document.getElementById('indice_tag').value).focus();
			return false;
		}
		else
		{			
			return true;
		}
	}
}
function affiche_description()
{
	document.getElementById('txt_description').style.display = "block";
	document.getElementById('txt_cacher').style.display = "block";
	document.getElementById('txt_afficher').style.display = "none";	
}
function cacher_description()
{
	document.getElementById('txt_description').style.display = "none";
	document.getElementById('txt_cacher').style.display = "none";
	document.getElementById('txt_afficher').style.display = "block";	
}

function vie_tag(keyword)
{
	if (keyword == "0") alert('Vous devez choisir une activité valide');
	else
	{		
		var xhr = null;    
		if (window.XMLHttpRequest) 
		{ 
		    xhr = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) 
		{
		    xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
		//on définit l'appel de la fonction au retour serveur
		xhr.onreadystatechange = function() 
		{ 
		    if(xhr.readyState == 4 && xhr.status == 200)
		    {
		    	list_results(xhr, keyword);
		    }
		    
		    else 
		    {	
		    	document.getElementById("resultats").innerHTML = "<img  src='img/image-loader.gif' alt='load' />";
		    }
		};
		//on appelle le fichier .php en lui envoyant les variables en get
		xhr.open("GET", "tag_sous_tag.php?keyword="+keyword, true);
		xhr.send(null);
	}
	return false;
}

function list_results(xhr, keyword)
{
	var contents = ""; //contenu retour
	//récupération des noeuds XML
	var docXML= xhr.responseXML;
	var items = docXML.getElementsByTagName("item");
	// decompilation des resultats XML
	contents += "<table border='0' cellpadding='3' cellspacing='3'>";
	for (i=0;i<items.length;i++)
	{	
		if(i%3==0 || i==0)
		{
			if(i>0)
			{
				contents += "</tr>";
			}
			contents += "<tr>";
		}
		//récupération des données
		notemp = items[i].getElementsByTagName('no')[0];
		no = notemp.childNodes[0];
		titretemp = items[i].getElementsByTagName('titre')[0];
		titre = titretemp.childNodes[0];
		contents += "<td><input name='sous_tag[]' value='"+no.nodeValue+"' type='checkbox' /> "+titre.nodeValue.charAt(0).toUpperCase() + titre.nodeValue.substring(1).toLowerCase()+"<br/></td>";
	}
	
	contents += "</table>";
	document.getElementById("resultats").innerHTML = contents;
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
		<div id="load">
			<img src="img/image-loader.gif" alt="Suppression en cours" /><br/>
			Suppression en cours...
		</div>
			<h1><?php echo $action_page; ?> une structure - Etape 2</h1>
			<?php require('01_include/structure_etapes_form.php'); ?>

				<div id="aide_icone">
					<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
				</div>
				
				<?php /* <form name="EDretour" id="EDretour" action="auto_structure_etape1.php" method="post" accept-charset="UTF-8">
					<input type="hidden" value="<?php echo $_SESSION['no_structure'] ?>" name="no_orig">
					<button type="submit" class="boutonbleu ico-flecheretour">Retour (étape 1)</button>
				</form>*/ ?>

				<?php
				if (!empty($erreur))
				{
					echo "<p id=\"erreur\">$erreur</p>";
				}
				else
				{
				?>
					<h1 class="titreA">Activités</h1><br/>

				<div id="aide_contenu">
					<p>Pour répertorier votre structure / activité sur les pages du site « Ensemble ici », nous vous demandons de sélectionner les thématiques liées.<br/>
					Choisissez un titre et les mots-clés correspondants à votre structure / activité avec les cases à cocher. Cliquer sur <b>Ajouter</b> pour enregistrer.<br/> 
					Répéter cette action pour ajouter de nouveaux titres d’activité et de nouveaux mots-clés (un récapitulatif est présenté en bas de page).<br/><br/>
					</p>
				</div>
					<p>Quelles thématiques sont  concernée par votre structure / activité ?</p>
					<fieldset>
					<form name="EIForm" id="EIForm" action="auto_structure_etape2.php?ajouter=ok" method="post" class="formA" accept-charset="UTF-8" onSubmit="return verif_structure_etape2()">
					<fieldset>
<?php //						<label>1/ S&eacute;lectionner une thématique d'activit&eacute; <sup>*</sup> :</label><br/><br/><br/> ?>
						<strong>1.</strong> <select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)" class="validate[required]">
							<option value="" selected>Choisissez une thématique d'activité</option>
							<?php
								for($indice_tag=0; $indice_tag<count($tab_tag); $indice_tag++)
								{
									echo "<option value=\"".$tab_tag[$indice_tag]['no']."\">".$tab_tag[$indice_tag]['titre']."</option>";
								}
							?>
						</select> <sup>*</sup>
						<br/>
						<div id="resultats"></div>
						<br/><br/>
						<label class="labellarge">2. Proposer une description de cette activit&eacute; : <sup>(non obligatoire)</sup><br/></label><br/>
						<sub><div id="txt_afficher"><a href="#" onClick="affiche_description(); return false">-- afficher la zone de saisie --</a></div><div id="txt_cacher" style="display:none"><a href="#" onClick="cacher_description(); return false">-- cacher la zone de saisie --</a></div></sub><br/>
						<div id="txt_description" style="display:none">
						<textarea id="description" name="description">
							<?php echo $sous_tag_description ?>
						</textarea>
						</div>
						<br/><br/>
						<center><button type="submit" class="boutonbleu ico-fleche">Ajouter</button></center>
					</fieldset>
					</form>
					
					<?php 
						if(count($tab_sous_tag_structure)>0)
						{
					?>
					<br/>
					<h3>Activitées liées à votre structure : </h3><br/>
					<center>
					<table border="1" cellspacing="5" cellpadding ="5">
						<tr>
							<td><center><b>Nom activit&eacute;</b></center></td>
							<td><center><b>Description</b></center></td>
							<td></td>
						</tr>
						<?php
						for($indice_ss_tag=0; $indice_ss_tag<count($tab_sous_tag_structure); $indice_ss_tag++)
						{
							//recuperation des informations de base du sous_tag
							$sql_sous_tag2="SELECT * FROM sous_tag WHERE no=:no ORDER BY titre";
							$res_sous_tag2 = $connexion->prepare($sql_sous_tag2);
							$res_sous_tag2->execute(array(':no'=>$tab_sous_tag_structure[$indice_ss_tag]['no_sous_tag'])) or die ("requete ligne 157 : ".$sql_sous_tag2);
							$tab_sous_tag2=$res_sous_tag2->fetchAll();
							
							echo "<tr>
									<td>".$tab_sous_tag2[0]['titre']."</td>
									<td>".$tab_sous_tag_structure[$indice_ss_tag]['description']."</td>
									<td><center>
									<a id=\"".$tab_sous_tag2[0]['no']."\" href=\"#\" class=\"boutonbleu ico-supprimer delete\">Supprimer</a></center></td>
								</tr>";
								// ?supprimer=ok&no_sous_tag=".$tab_sous_tag2[0]['no']."\"
						
						}
						?>
					</table>
					</center>
					<br/><br/>
					<div class="actions"><a href="auto_structure_etape3.php<?php if ($mode_modification) echo "?mode_modification=1"; ?>" class="boutonbleu ico-fleche">Suite (étape 3)</a></div>
					<?php
						}
					?>
					
					</fieldset>
				<?php
				} // If !erreur
				?>				
				
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