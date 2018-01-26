<?php
/*****************************************************
Gestion des contenus des blocs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Affichage du message si existe
$messager = $_SESSION['message'];
unset($_SESSION['message']);

$chemints=$root_site."js/jquery.tablesorter.min.js";
$ajout_header = <<<AJHE
<script type="text/javascript" src="$chemints"></script>
<script>
    $(document).ready(function() 
	{ 
	    $.tablesorter.defaults.widgets = ['zebra']; 
	    $(".tablesorter").tablesorter({
		headers: {1:{sorter: false}}
	    });
	}
    ); 
</script>
AJHE;
include "../inc-header.php";
?>
<ul class="liendspage">

</a></li> 
	<?php 
		include ('aff.php');
    ?>
</ul>

<?php include "../inc-footer.php"; ?>