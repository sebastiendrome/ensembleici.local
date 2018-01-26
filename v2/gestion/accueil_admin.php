<?php
/*****************************************************
Page d'accueil de l'interface d'administration
******************************************************/
session_name("EspacePerso");
session_start();
$pageGestion = true;
require_once('../01_include/connexion_verif.php');
require_once('../01_include/_connect.php');
require_once('../01_include/_var_ensemble.php');

$TitrePage = "Bienvenue sur l'administration Ensemble ici";
$pagemenuadm = "acc";

// Affichage du message s'il existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

include "inc-header.php";
?>
<h2>Bienvenue !</h2>
<p>Choisissez une rubrique dans le menu ci-contre.</p>

<?php
include "inc-footer.php";
?>