<?php
/*****************************************************
Suppression d'une version de l'historique d'un evenement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$no_item = intval($_POST['no_item']);
$no_evenement = intval($_POST['no_evenement']);

if (($no_item) && ($no_evenement))
{
	// Suppression de la sauvegarde
	$sql_delete_version="DELETE FROM evenement_temp
				WHERE no=:no_item";
	$delete_version = $connexion->prepare($sql_delete_version);
	$delete_version->execute(array(':no_item'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_delete_version);
	$nb_supp_version = $delete_version->rowCount();

	// Suppression de l'association evt / sauvegarde
	$sql_delete_asso="DELETE FROM evenement_modification
				WHERE no_evenement_temp=:no_item
				AND no_evenement=:no_evenement";
	$delete_asso = $connexion->prepare($sql_delete_asso);
	$delete_asso->execute(array(':no_item'=>$no_item, ':no_evenement'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_delete_asso);
	$nb_supp_asso = $delete_asso->rowCount();

	// Suppression des tags de la sauvegarde
	$sql_delete_tag="DELETE FROM evenement_tag_temp
				WHERE no_evenement_temp=:no_item";
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