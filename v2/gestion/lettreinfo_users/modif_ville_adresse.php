<?php
session_name("EspacePerso");
session_start();
require_once "config.php";
if(isset($_POST["no_adresse"])&&$_POST["no_adresse"]!=null&isset($_POST["no_ville"])&&$_POST["no_ville"]!=null){
	$query = "UPDATE `newsletter` SET no_ville=:n_v WHERE no=:no";
	$res_query = $connexion->prepare($query);
	$res_query->execute(array(":n_v"=>$_POST["no_ville"],":no"=>$_POST["no_adresse"]));
	$reponse = true;
}
else{
	$reponse = false;
}
echo json_encode($reponse);
?>