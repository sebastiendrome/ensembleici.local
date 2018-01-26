<?php
	session_name("EspacePerso");
	session_start();
	require ('01_include/connexion_verif.php');
	$_SESSION['no_evenement']=0;
	
	// include header
	$titre_page = "Ajouter un évènement";
	$titre_page_bleu = " ";
	$meta_description = "Ajouter un évènement sur Ensemble ici : Tous acteurs de la vie locale";
	$ajout_header .= "<script type='text/javascript' src=\"js/recherche_ajax.js\"></script>";
	
	include ('01_include/structure_header.php');
?>

      <div id="colonne2" class="page_ville">                  
        <div id="agendalocal" class="blocB">
			<h1>Ajouter un évènement</h1>

			<div>
				<form name="recherche_form" id="recherche_form" action="" method="post" class="formA" >
					
					<p>Entrez le nom de votre événement, afin de savoir si il est déjà présent dans notre base de données. 
Le résultat de recherche vous permettra de créer votre propre fiche "événement" ou de modifier une fiche déjà existante.</p><br/>

					<label>Nom :</label><input type="text" name="nom_rech" class="validate[required] nom_rech"/><br/><br/>
					<label>Code Postal :</label><input type="text" value="" name="cp_rech"/>&nbsp;
					<br/><br/>
					<center>
						<button type="submit" class="boutonbleu ico-fleche" onClick="return auto_recherche(document.recherche_form.nom_rech.value, document.recherche_form.cp_rech.value, 'evenement')">Rechercher</button></center>
				
					<br/><br/></form>
					<div id="resultats"></div>
			</div>  
		</div>
		<div class="clear"></div>
      </div>
	<script src="js/jquery.validationEngine-fr.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
		<script type="text/javascript">
			$(document).ready(function() {
				$(".nom_rech").focus();
			    // Validation form
			    $("#recherche_form").validationEngine("attach",{promptPosition : "topRight", scroll: false});
			});
		</script>
<?php

$affiche_articles = true;
$affiche_publicites = true;
include ('01_include/structure_colonne3.php');

include ('01_include/structure_footer.php');
?>