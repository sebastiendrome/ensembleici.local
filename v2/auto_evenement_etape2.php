<?php
	
	session_name("EspacePerso");
	session_start();
	$no_evenement=$_SESSION['no_evenement'];
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "evenement";
	$tag_modif = "";
	$tag_description="";

	if (!$no_evenement)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	if($_REQUEST['provenance']=="etape1")
	{
		// Vérifications saisies étape précédente
		if (empty($_POST['nom'])) $erreur .= "<br/>Vous devez saisir un nom.";
		$annonce = intval($_POST['annonce']); // Type d'evt
		if($annonce!=-1)
			$genre = $annonce;
		else
			$genre = intval($_POST['genre']); // Type d'evt
		if (!$genre) $erreur .= "<br/>Vous devez sélectionner un type d'évènement.";
		if (empty($_POST['description'])) $erreur .= "<br/>Vous devez saisir une description.";
	}
	

	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_evt'])
	  $mode_modification = intval($_SESSION['mode_modification_evt']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";

	//mise à jour des infos de l'etape 1
	if($_POST['no_evenement_post']==$no_evenement)
	{
		$maj_evenement_etape1 = "UPDATE evenement SET titre=:titre, sous_titre=:sous_titre, no_genre=:no_genre, description=:description, description_complementaire=:description_complementaire WHERE no=:no";
		$maj_evenement = $connexion->prepare($maj_evenement_etape1);
		$maj_evenement->execute(array(':titre'=>$_POST['nom'], ':sous_titre'=>$_POST['sous_titre'], ':no_genre'=>$genre, ':description'=>$_POST['description'], ':description_complementaire'=>$_POST['description_comp'], ':no'=>$no_evenement)) or die ("requete ligne 18 : ".$insertion_evenement_etape1);
	}
	
	if($_GET['ajouter']=="ok")
	{
		//$no_tag_libelle="form_tag_".$_POST['indice_vie'];
		$tab_tag_reponse=$_POST['tag'];
		$no_vie_reponse=$_POST['form_vie'];
		
		if(count($tab_tag_reponse)>0)
		{
			for($indice_tag=0; $indice_tag<count($tab_tag_reponse); $indice_tag++)
			{
				$sql_test_clef="SELECT * FROM evenement_tag WHERE no_evenement=:no_evenement AND no_tag=:no_tag";
				$res_test_clef = $connexion->prepare($sql_test_clef);
				$res_test_clef->execute(array(':no_evenement'=>$no_evenement, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("requete ligne 97 : ".$sql_test_clef);
				$tab_test_clef=$res_test_clef->fetchAll();
				if(count($tab_test_clef)==0)
				{
					$sql_evenements = "INSERT INTO evenement_tag (no_evenement, no_tag) VALUES (:no_evenement, :no_tag)";
					$insert = $connexion->prepare($sql_evenements);
					$insert->execute(array(':no_evenement'=>$no_evenement, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("requete ligne 38 : ".$sql_evenements);
				}
			}
		}
		else
		{
			$sql_vie_tag="SELECT * FROM vie_tag WHERE no_vie=:no_vie";
			$res_vie_tag = $connexion->prepare($sql_vie_tag);
			$res_vie_tag->execute(array(':no_vie'=>$no_vie_reponse)) or die ("requete ligne 97 : ".$sql_vie_tag);
			$tab_vie_tag=$res_vie_tag->fetchAll();
			//echo "alert('$no_vie_reponse')";
			
			for($indice_tag=0; $indice_tag<count($tab_vie_tag); $indice_tag++)
			{
				$sql_test_clef="SELECT * FROM evenement_tag WHERE no_evenement=:no_evenement AND no_tag=:no_tag";
				$res_test_clef = $connexion->prepare($sql_test_clef);
				$res_test_clef->execute(array(':no_evenement'=>$no_evenement, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("requete ligne 97 : ".$sql_test_clef);
				$tab_test_clef=$res_test_clef->fetchAll();
				if(count($tab_test_clef)==0)
				{
					$sql_evenements = "INSERT INTO evenement_tag (no_evenement, no_tag) VALUES (:no_evenement, :no_tag)";
					$insert = $connexion->prepare($sql_evenements);
					$insert->execute(array(':no_evenement'=>$no_evenement, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("requete ligne 60 : ".$sql_evenements);
				}
			}
			
		}

	}
	
	$sql_evenement="SELECT * FROM evenement WHERE no=:no";
	$res_evenement = $connexion->prepare($sql_evenement);
	$res_evenement->execute(array(':no'=>$no_evenement)) or die ("requete ligne 14 : ".$sql_evenement);
	$tab_evenement_modif=$res_evenement->fetchAll();
	
	//on recupere les sous tag lié à cette evenement
	$sql_tag_evenement="SELECT * FROM evenement_tag WHERE no_evenement=:no_evenement ORDER BY no_tag";
	$res_tag_evenement = $connexion->prepare($sql_tag_evenement);
	$res_tag_evenement->execute(array(':no_evenement'=>$no_evenement)) or die ("requete ligne 41 : ".$sql_tag_evenement);
	$tab_tag_evenement=$res_tag_evenement->fetchAll();
	
	//recuperation des vies
	$sql_vie="SELECT * FROM vie ORDER BY libelle";
	$res_vie = $connexion->prepare($sql_vie);
	$res_vie->execute() or die ("requete ligne 97 : ".$sql_vie);
	$tab_vie=$res_vie->fetchAll();
	
	
	//on recupere l'int&eacute;gtralit&eacute; des tag
	$sql_tag="SELECT * FROM tag ORDER BY titre";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("requete ligne 97 : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();
	
	// include header
	
		$titre_page = $action_page." un évènement - Etape 2";
		$meta_description = $action_page." un évènement sur Ensemble ici : Tous acteurs de la vie locale";
		
	$titre_page_bleu = " ";
	$ajout_header .= <<<AJHE
	<script type="text/javascript">
	$(document).ready(function() {
		$('#load').hide();
	});
	
	$(function() {
		$(".delete").click(function() {
			$('#load').fadeIn();
			var commentContainer = $(this).parents('tr:first');
			var id = $(this).attr("id");
			var no_evenement = $no_evenement;
			var string = "no_tag=" + id + "&" + "no_evenement=" + no_evenement;
				
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
function verif_evenement_etape2()
{
	if(document.EIForm.form_vie.value==0)
	{
		alert("Vous devez choisir au moins une activité pour votre événement !!");
		document.EIForm.form_vie.focus();
		return false;
	}
	else
	{			
		return true;
	}
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
		xhr.open("GET", "vie_tag.php?keyword="+keyword, true);
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
		contents += "<td><input name='tag[]' value='"+no.nodeValue+"' type='checkbox' /> "+titre.nodeValue+"<br/></td>";
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
        <div id="formevent" class="blocB">
		<div id="load">
			<img src="img/image-loader.gif" alt="Suppression en cours" /><br/>
			Suppression en cours...
		</div>
			<h1><?php echo $titre_page; ?></h1>

			<?php require('01_include/structure_etapes_form.php'); 

				if (!empty($erreur))
					echo "<p id=\"erreur\">$erreur</p>";
				else
				{
				?>

				<div id="aide_icone">
					<img src="img/icone-info.png" title="Cliquez pour afficher les explications" class="infobulle-b" />
				</div>
					<h1 class="titreA">Activités</h1>
				<div id="aide_contenu">
					<p>Pour répertorier votre événement sur les pages du site « Ensemble ici », nous vous demandons de sélectionner ou de créer les thématiques liées.<br/> 
					Choisissez au minimum un intitulé et cliquez sur "Ajouter" pour enregistrer vos informations.<br/> 
					Pour ajouter de nouvelles thématiques, répéter cette action.<br/> 
					Un récapitulatif de vos thématiques est présenté en bas de page.
					</p>
				</div>
				
				<p>Quelles thématiques sont concernées par votre événement ?  <sup class="note">*</sup></p>
				
				<form name="EIForm" id="EIForm" action="auto_evenement_etape2.php?ajouter=ok" method="post" class="formA" accept-charset="UTF-8" onSubmit="return verif_evenement_etape2()">
					<fieldset>
						<select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)" class="validate[required]">
							<option value="" selected>Choisissez une thématique d'activité</option>
							<?php
								for($indice_vie=0; $indice_vie<count($tab_vie); $indice_vie++)
								{
									echo "<option value=\"".$tab_vie[$indice_vie]['no']."\">".$tab_vie[$indice_vie]['libelle']."</option>";
								}
							?>
						</select>
						<br/>
						<div id="resultats"></div>
						<br/><br/>
						<center><button type="submit" class="boutonbleu ico-fleche">Ajouter</button></center>
					</fieldset>
					</form>
					<br/><br/>
					<?php 
						if(count($tab_tag_evenement)>0)
						{
					?>
						
					<table class="tablo-activites">
						<thead>
							<tr><td colspan="2">
							<?php 
							if(count($tab_tag_evenement)>1)
								echo "Activités choisies";								else
								echo "Activité choisie";		
							?>
							</td></tr>
						</thead>
						<tbody>
						<?php
						for($indice_ss_tag=0; $indice_ss_tag<count($tab_tag_evenement); $indice_ss_tag++)
						{
							//recuperation des informations de base du tag
							$sql_tag2="SELECT * FROM tag WHERE no=:no ORDER BY titre";
							$res_tag2 = $connexion->prepare($sql_tag2);
							$res_tag2->execute(array(':no'=>$tab_tag_evenement[$indice_ss_tag]['no_tag'])) or die ("requete ligne 157 : ".$sql_tag2);
							$tab_tag2=$res_tag2->fetchAll();
							echo "<tr>
									<td>".$tab_tag2[0]['titre']."</td>
									<td><center>
									<a id=\"".$tab_tag2[0]['no']."\" href=\"#\" class=\"boutonbleu ico-supprimer delete\">Supprimer</a></center></td>
								</tr>";
					
						}
						?>
						</tbody>
					</table>
					<?php
						}
						if(count($tab_tag_evenement)>0)
						{
					?>
					<br/><br/>
						<div class="actions"><a href="auto_evenement_etape3.php?mode_modification=<?php if ($mode_modification) echo "1"; else echo "0"; ?>" class="boutonbleu ico-fleche">Suite (étape 3)</a></div>
					<?php
						}
						else
						{
							echo "<br/><br/><center>Pour continuer votre saisie vous devez séléctionner au moins une activité pour votre événement</center><br/><br/>";
						}
					?>
					
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