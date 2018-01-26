<?php
header('Content-Type: text/plain; charset=UTF-8');
//require('/home/ensemble/www/00_dev_sam/01_include/_var_ensemble.php');
require('/home/ensemble/01_include/_var_ensemble.php');
echo $_SESSION["utilisateur"]["pseudo"];
?>