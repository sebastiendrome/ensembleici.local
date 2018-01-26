<?php
	
	session_name("EspacePerso");
	session_start();
	$no_pa=$_SESSION['no_pa'];
	require ('01_include/connexion_verif.php');
	require('01_include/_connect.php');
	$type = "petiteannonce";


	if (!$no_pa)
	{
			// désactivé(e) ou inexistant(e)
			header("location:index.php");
			exit();
	}

	if($_REQUEST['provenance']=="etape1")
	{
		// Vérifications saisies étape précédente
		if (empty($_POST['titre'])) $erreur .= "<br/>Vous devez saisir un titre.";
		if (empty($_POST['description'])) $erreur .= "<br/>Vous devez saisir une description.";
	}

	$mode_modification = intval($_REQUEST['mode_modification']);
	if (!$mode_modification && $_SESSION['mode_modification_pa'])
	  $mode_modification = intval($_SESSION['mode_modification_pa']);
	if ($mode_modification)
		$action_page = "Modifier";
	else
		$action_page = "Ajouter";

	//mise à jour des infos de l'etape 1
    $prix = str_replace("€","",$_POST['prix']); // Supprime le symbole euro
    $prix = str_replace("&euro;","",$_POST['prix']); // Supprime le symbole euro
    $prix = floatval(str_replace(",",".",$prix)); // Remplace les , par des .

	if($_POST['no_pa_post']==$no_pa)
	{
		$maj_pa_etape1 = "UPDATE petiteannonce SET titre=:titre, monetaire=:monetaire, prix=:prix, description=:description WHERE no=:no";
		$maj_pa = $connexion->prepare($maj_pa_etape1);
		$maj_pa->execute(array(':titre'=>$_POST['titre'], ':monetaire'=>$_POST['monetaire'], ':prix'=>$prix, ':description'=>$_POST['description'], ':no'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$insertion_pa_etape1);
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
				$sql_test_clef="SELECT * FROM petiteannonce_tag WHERE no_petiteannonce=:no_pa AND no_tag=:no_tag";
				$res_test_clef = $connexion->prepare($sql_test_clef);
				$res_test_clef->execute(array(':no_pa'=>$no_pa, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur ".__LINE__." : ".$sql_test_clef);
				$tab_test_clef=$res_test_clef->fetchAll();
				if(count($tab_test_clef)==0)
				{
					$sql_pas = "INSERT INTO petiteannonce_tag (no_petiteannonce, no_tag) VALUES (:no_pa, :no_tag)";
					$insert = $connexion->prepare($sql_pas);
					$insert->execute(array(':no_pa'=>$no_pa, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur ".__LINE__." : ".$sql_pas);
				}
			}
		}
		else
		{
			$sql_vie_tag="SELECT * FROM vie_tag WHERE no_vie=:no_vie";
			$res_vie_tag = $connexion->prepare($sql_vie_tag);
			$res_vie_tag->execute(array(':no_vie'=>$no_vie_reponse)) or die ("Erreur ".__LINE__." : ".$sql_vie_tag);
			$tab_vie_tag=$res_vie_tag->fetchAll();
			//echo "alert('$no_vie_reponse')";
			
			for($indice_tag=0; $indice_tag<count($tab_vie_tag); $indice_tag++)
			{
				$sql_test_clef="SELECT * FROM petiteannonce_tag WHERE no_petiteannonce=:no_pa AND no_tag=:no_tag";
				$res_test_clef = $connexion->prepare($sql_test_clef);
				$res_test_clef->execute(array(':no_pa'=>$no_pa, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur ".__LINE__." : ".$sql_test_clef);
				$tab_test_clef=$res_test_clef->fetchAll();
				if(count($tab_test_clef)==0)
				{
					$sql_pas = "INSERT INTO petiteannonce_tag (no_petiteannonce, no_tag) VALUES (:no_pa, :no_tag)";
					$insert = $connexion->prepare($sql_pas);
					$insert->execute(array(':no_pa'=>$no_pa, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur ".__LINE__." : ".$sql_pas);
				}
			}
		}
	}
	
	$sql_pa="SELECT * FROM petiteannonce WHERE no=:no AND no_utilisateur_creation=:no_utilisateur_creation";
	$res_pa = $connexion->prepare($sql_pa);
	$res_pa->execute(array(':no'=>$no_pa,':no_utilisateur_creation'=>$_SESSION['UserConnecte_id'])) or die ("Erreur ".__LINE__." : ".$sql_pa);
	$tab_pa_modif=$res_pa->fetchAll();
	
	//on recupere les sous tag lié à cette petiteannonce
	$sql_tag_pa="SELECT * FROM petiteannonce_tag WHERE no_petiteannonce=:no_pa ORDER BY no_tag";
	$res_tag_pa = $connexion->prepare($sql_tag_pa);
	$res_tag_pa->execute(array(':no_pa'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$sql_tag_pa);
	$tab_tag_pa=$res_tag_pa->fetchAll();
	
	//recuperation des vies
	$sql_vie="SELECT * FROM vie ORDER BY libelle";
	$res_vie = $connexion->prepare($sql_vie);
	$res_vie->execute() or die ("Erreur ".__LINE__." : ".$sql_vie);
	$tab_vie=$res_vie->fetchAll();
	
	
	//on recupere l'int&eacute;gtralit&eacute; des tag
	$sql_tag="SELECT * FROM tag ORDER BY titre";
	$res_tag = $connexion->prepare($sql_tag);
	$res_tag->execute() or die ("Erreur ".__LINE__." : ".$sql_tag);
	$tab_tag=$res_tag->fetchAll();
	
	// include header
	$titre_page = $action_page." une petite annonce - Etape 2";
	$meta_description = $action_page." une petite annonce sur Ensemble ici : Tous acteurs de la vie locale";
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
			var no_pa = $no_pa;
			var string = "no_tag=" + id + "&" + "no_pa=" + no_pa;
				
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
function verif_pa_etape2()
{
	if(document.EIForm.form_vie.value==0)
	{
		alert("Vous devez choisir au moins une thématique pour votre petite annonce !");
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
	if (keyword == "0") alert('Vous devez choisir une thématique valide');
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
        <div id="formpa" class="blocB">
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
					<h1 class="titreA">Thématiques</h1>
				<div id="aide_contenu">
					<p>Pour répertorier votre petite annonce sur les pages du site « Ensemble ici », nous vous demandons de sélectionner les thématiques liées.<br/> 
					Choisissez au minimum un intitulé et cliquez sur "Ajouter" pour enregistrer vos informations.<br/> 
					Pour ajouter de nouvelles thématiques, répéter cette action.<br/> 
					Un récapitulatif de vos thématiques est présenté en bas de page.
					</p>
				</div>
				
				<p>Quelles thématiques sont concernées par votre petite annonce ?  <sup class="note">*</sup></p>
				
				<form name="EIForm" id="EIForm" action="auto_petiteannonce_etape2.php?ajouter=ok" method="post" class="formA" accept-charset="UTF-8" onSubmit="return verif_pa_etape2()">
					<fieldset>
						<select name="form_vie" id="form_vie" onChange="return vie_tag(this.value)" class="validate[required]">
							<option value="" selected>Choisissez une thématique</option>
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
						if(count($tab_tag_pa)>0)
						{
					?>
						
					<table class="tablo-activites">
						<thead>
							<tr><td colspan="2">
							<?php 
							if(count($tab_tag_pa)>1)
								echo "Thématiques choisies";
							else
								echo "Thématique choisie";
							?>
							</td></tr>
						</thead>
						<tbody>
						<?php
						for($indice_ss_tag=0; $indice_ss_tag<count($tab_tag_pa); $indice_ss_tag++)
						{
							//recuperation des informations de base du tag
							$sql_tag2="SELECT * FROM tag WHERE no=:no ORDER BY titre";
							$res_tag2 = $connexion->prepare($sql_tag2);
							$res_tag2->execute(array(':no'=>$tab_tag_pa[$indice_ss_tag]['no_tag'])) or die ("Erreur ".__LINE__." : ".$sql_tag2);
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
						if(count($tab_tag_pa)>0)
						{
					?>
					<br/><br/>
						<div class="actions"><a href="auto_petiteannonce_etape3.php<?php if ($mode_modification) echo "?mode_modification=1"; ?>" class="boutonbleu ico-fleche">Suite (étape 3)</a></div>
					<?php
						}
						else
						{
							echo "<br/><br/><center>Pour continuer votre saisie vous devez séléctionner au moins une thématique pour votre petite annonce</center><br/><br/>";
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