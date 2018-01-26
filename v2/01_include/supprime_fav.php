<?php
$id_recu = $_POST["id_recu"]; // id objet
$type = $_POST["type"]; // type objet
$id_utilisateur = $_POST["id_utilisateur"]; // url page dédiée

require_once ('_connect.php');
$flag_ok = $connexion -> exec("DELETE FROM favori WHERE no_utilisateur='".$id_utilisateur."' AND no_occurence='".$id_recu."' AND type_fav='".$type."'");

if($flag_ok==1)
	echo "ok";
	//print(json_encode("ok"));
?>