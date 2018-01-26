<?php
	// include header
	$titre_page = "Guide d'utilisation du site";
	$meta_description = "Guide d'utilisation du site internet Ensemble Ici. Ensemble ici : Tous acteurs de la vie locale.";

	/* $ajout_header = <<<AJHE
AJHE;*/
	include ('01_include/structure_header.php');

?>
      <div id="colonne2" class="page_inscription">                  
		<div class="un-event">
			<h1 class="titreA">Voir le guide en ligne</h1><br/>
			<center>
				<a href="http://www.calameo.com/read/001045068da8ecd549187" target="_blanck"><img src="02_medias/01_interface/guide_jpg.jpg" border="0"></a>
			</center>
			<br/><br/><br/>
			<h1 class="titreA">Voir le guide au format PDF</h1><br/>
			<center>
				<a href="02_medias/08_pdf/guide_utilisation-08-06-2012.pdf" target="_blanck"><img src="02_medias/01_interface/icone_pdf.jpg" border="0"></a>
			</center>
		</div>
      </div>
    
<?php
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>