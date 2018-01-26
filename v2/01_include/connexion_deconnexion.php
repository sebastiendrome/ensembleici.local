<?php
if(isset($_GET["page"]))
	$page = $_GET["page"];
// ----------------------------------------
// Fermer la session utilisateur
// ----------------------------------------

require ('_var_ensemble.php');
session_name("EspacePerso");
session_start();
session_unset ();
session_destroy ();
 
//$page = 'inscription.php';
if(isset($page) && $page == "espace")
{
	echo "<script type=\"text/javascript\">";
	echo "$('#colonne2').html('Vous venez de vous d&eacute;connecter, <a href=\"$root_site\">cliquez ici</a> pour revenir &agrave; l\'accueil.');";
	echo "$(location).attr('href',\"$root_site\");";
	echo "</script>";
}
else
{
	header("Location: $root_site");
}
exit;
?>