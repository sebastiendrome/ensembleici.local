<?php
	session_name("EspacePerso");
	session_start();
	require_once ('01_include/_var_ensemble.php');

	// Ajouter une redirection à l'espace perso si déjà connecté

	include("gestion_page_inscription.php");

	// include header
	$titre_page = $titre_inscription;
	$meta_description = "Ensemble ici : Tous acteurs de la vie locale";
 	/* $chem_css_ui = $root_site."css/jquery-ui-1.8.21.custom.css";
	<link rel="stylesheet" href="$chem_css_ui" type="text/css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> */

	include ('01_include/structure_header.php');
?>

      <div id="colonne2" class="page_inscription">

	<?php
		include($mainPage);
	?>

      </div>
<?php
	// Colonne 3
	$affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');

	// Footer
	include ('01_include/structure_footer.php');
?>