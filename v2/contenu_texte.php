<?php
/* Affichage d'une page au contenu dynamique */
require_once ('01_include/_var_ensemble.php');
require_once ('01_include/_connect.php');

$id_bloc = intval($_GET["id_bloc"]);
if (!$id_bloc)
{
	header("Location:".$root_site."index.php");
	exit();
}

// Récupère les infos de la page
$sql_bloc="SELECT titre, contenu
            FROM `contenu_blocs`
            WHERE etat = :etat
            AND no = :idbloc";
$res_bloc = $connexion->prepare($sql_bloc);
$res_bloc->execute(array(':idbloc'=>$id_bloc, ':etat'=>1));
$tab_bloc = $res_bloc->fetch(PDO::FETCH_ASSOC);
if ((!empty($tab_bloc["titre"]))||(!empty($tab_bloc["contenu"])))
{
	$titre_page = $tab_bloc["titre"];
    if (!empty($tab_bloc["contenu"])) $contenu_page = nl2br($tab_bloc["contenu"]);
}

	include ('01_include/structure_header.php');
	echo '<div id="colonne2" class="page_inscription">';
	
	if ($titre_page) echo "<h1>".$titre_page."</h2>";
	if ($contenu_page)
	{
		echo $contenu_page;
	}
	else
	{
		echo '<p>Erreur.</p>';
	}

	echo '</div>';
	// Colonne 3
	// $affiche_articles = true;
	$affiche_publicites = true;
	include ('01_include/structure_colonne3.php');
	
	// Footer
	include ('01_include/structure_footer.php');
?>