<?php
session_name("EspacePerso");
session_start();
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
echo json_encode(est_connecte());
?>
