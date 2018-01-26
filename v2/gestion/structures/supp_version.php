<?php
/*****************************************************
Suppression d'une version de l'historique d'une structure
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$no_item = intval($_POST['no_item']);
$no_structure = intval($_POST['no_structure']);

if (($no_item) && ($no_structure))
{
	// Suppression de la sauvegarde
	$sql_delete_version="DELETE FROM structure_temp
				WHERE no=:no_item";
	$delete_version = $connexion->prepare($sql_delete_version);
	$delete_version->execute(array(':no_item'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_delete_version);
	$nb_supp_version = $delete_version->rowCount();

	// Suppression de l'association structure / sauvegarde
	$sql_delete_asso="DELETE FROM structure_modification
				WHERE no_structure_temp=:no_item
				AND no_structure=:no_structure";
	$delete_asso = $connexion->prepare($sql_delete_asso);
	$delete_asso->execute(array(':no_item'=>$no_item, ':no_structure'=>$no_structure)) or die ("Erreur ".__LINE__." : ".$sql_delete_asso);
	$nb_supp_asso = $delete_asso->rowCount();

	// Suppression des tags de la sauvegarde
	$sql_delete_tag="DELETE FROM structure_sous_tag_temp
				WHERE no_structure_temp=:no_item";
	$res_delete_tag = $connexion->prepare($sql_delete_tag);
	$res_delete_tag->execute(array(':no_item'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);
	$nb_supp_tag = $res_delete_tag->rowCount();
	
	if (($nb_supp_version)&&($nb_supp_asso))
	{
		echo "ok";
		$_SESSION['message'] .= "Version supprimée avec succès.<br/>";
	}
}
?>