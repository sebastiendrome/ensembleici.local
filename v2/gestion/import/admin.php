<?php
/*****************************************************
Gestion des imports
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message si existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

$ajout_header = <<<AJHE
AJHE;
include "../inc-header.php";
?>
<h2>www.culture-provence-baronnies.fr</h2>
<ul class="liendspage">
	<li><a href="import_culture-provence-baronnies.fr.php" title="Forcer l'importation maintenant">Forcer l'importation des évènements maintenant</a> (<a href="log-import.txt" target="_blank" title="Log">Log</a>)</li>
	<li><a href="import_culture-provence-baronnies.fr_affichage.php" title="Afficher les évènement prêts pour l'importation" target="_blank">Afficher les évènement prêts pour l'importation</a></li>
</ul>

<?php include "../inc-footer.php"; ?>