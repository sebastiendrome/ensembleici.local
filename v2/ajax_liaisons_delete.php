<?php
/*****************************************************
Ajout d'une liaison
******************************************************/
session_name("EspacePerso");
session_start();
require_once ('01_include/connexion_verif.php');
require_once ('01_include/_connect.php');

// type_source et id_source

// Vérifications
$no_liaison = intval($_POST['no_liaison']);
$id_source = intval($_POST['id_source']);
if (preg_match("/^[A-Za-z\\-\\., \']+$/",$_POST['type_source']))
    $type_source_ok = trim(strtolower($_POST['type_source']));

if ($no_liaison && $id_source && $type_source_ok)
{
	$sql_delete_liaison="DELETE FROM `liaisons`
				WHERE no=$no_liaison
				AND (
					(type_A='$type_source_ok' AND no_A=$id_source)
					OR
					(type_B='$type_source_ok' AND no_B=$id_source)					
				)";
	$count = $connexion->exec($sql_delete_liaison);
	if ($count) echo "Liaison supprimée avec succès.";
}
?>