<?php
session_name("EspacePerso2");
session_start();
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
//include "/home/ensemble/www/00_dev_sam/01_include/_fonctions.php";
require('/home/ensemble/01_include/_var_ensemble.php');
include "/home/ensemble/01_include/_fonctions.php";
echo json_encode(get_tags($_POST["vie"],$_POST["exception"],$_POST["m"],true));
?>
