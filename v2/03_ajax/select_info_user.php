<?php
// Affichage des pages villes
session_name("EspacePerso");
session_start();
//if(!isset($_SESSION['date_pa']) || ($_SESSION['date_pa']=="")) $_SESSION['date_pa']=1;
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
if(est_connecte()){
	$reponse = array("id"=>$_SESSION["UserConnecte_id"],"admin"=>est_admin());
}
else{
	$reponse = false;
}
echo json_encode($reponse);
?>
