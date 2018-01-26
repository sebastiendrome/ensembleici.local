<?php
// Affichage des archives des newsletter
require_once ('01_include/_var_ensemble.php');
require_once ('01_include/_connect.php');

// Récupère les newsletter
$sql_bloc="SELECT objet, repertoire, date_debut
			FROM lettreinfo 
			WHERE no_envoi <> 0 
			ORDER BY date_creation DESC";
$res_bloc = $connexion -> prepare($sql_bloc);
$res_bloc -> execute();
$lettreinfos = $res_bloc->fetchAll();
if (!count($lettreinfos))
{
	// redirection vers l'accueil
	header("Location:".$root_site."index.php");
	exit();
}

	$titre_page = "Archive des lettres d'information";
	$meta_description = $titre_page.". Ensemble ici : Tous acteurs de la vie locale";

	include ('01_include/structure_header.php');

	echo "<div id='colonne2' class='page_inscription'>
	<h1>Lettres d'information</h1>\n";

    echo "<ul class='liste_aeree'>";
    foreach ($lettreinfos as $lettreinfo) 
    {
		echo "<li><a href=\"".$lettreinfo["repertoire"]."index.php\" target=\"_blank\">".datefr($lettreinfo["date_debut"])." : ".ucfirst($lettreinfo["objet"])."</a></li>";
	}
    echo "</ul>";

	echo '</div>';
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	include ('01_include/structure_footer.php');
?>