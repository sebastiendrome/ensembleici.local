<?php
session_name("EspacePerso");
session_start();
require_once "config.php";
if($_POST["n"]!=null&&($_POST["v"]==0||$_POST["v"]==1)){
	$query = "UPDATE utilisateur SET newsletter=:v WHERE no=:no";
	$res = $connexion->prepare($query);
	$res->execute(array(":v"=>$_POST["v"],":no"=>$_POST["n"])) or die ("Erreur ".__LINE__." : ".$query);
	echo "true";
}
else{
	echo "false";
}
?>