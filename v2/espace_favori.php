<?php 
session_name("EspacePerso");
session_start();
require_once ('01_include/_var_ensemble.php');
require_once ('01_include/_connect.php'); 
?>
<h3>Mes archives</h3>
<div id="gbl_fav">
		<?php require ('01_include/espace_affiche_fav.php'); ?>
</div>
