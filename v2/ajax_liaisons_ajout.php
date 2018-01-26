<?php
/*****************************************************
Ajout d'une liaison
******************************************************/
session_name("EspacePerso");
session_start();
require_once ('01_include/connexion_verif.php');
require_once ('01_include/_connect.php');

// Vérifications
$no_A = intval($_POST['no_A']);
$no_B = intval($_POST['no_B']);
// Types => Uniquement des lettres
if (preg_match("/^[A-Za-z\\-\\., \']+$/",$_POST['type_A']))
    $type_A = strtolower($_POST['type_A']);
if (preg_match("/^[A-Za-z\\-\\., \']+$/",$_POST['type_B']))
    $type_B = strtolower($_POST['type_B']);

if ($no_A && $no_B && $type_A && $type_B){
	$sql_ajt = "INSERT INTO `liaisons`
		(`no` , `type_A` , `no_A` , `type_B` , `no_B` , `date_creation`)
			VALUES
		(NULL , :type_A, :no_A, :type_B, :no_B, NOW())";
	$insert_ajt = $connexion->prepare($sql_ajt);
	$insert_ajt->execute(array(':type_A'=>$type_A, ':no_A'=>$no_A, ':type_B'=>$type_B, ':no_B'=>$no_B)) or die ("Erreur ".__LINE__." : ".$sql_ajt);
	print (json_encode("Liaison ajoutée avec succès."));
}
?>