<?php
session_name("EspacePerso");
session_start();
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
if(est_connecte()){
	$requete = "UPDATE utilisateur SET pseudo=:p WHERE no=:no";
	$res = $connexion->prepare($requete);
	$res->execute(array(":p"=>urldecode($_POST['p']),":no"=>$_SESSION['UserConnecte_id']));
}
?>
