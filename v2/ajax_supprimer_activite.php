<?php
	
	session_name("EspacePerso");
	session_start();
	require_once ('01_include/connexion_verif.php');
	require_once ('01_include/_connect.php');

	// Structure
	if ((is_numeric($_POST['no_sous_tag'])) && (is_numeric($_POST['no_structure']))) {
		$no_sous_tag = $_POST['no_sous_tag'];
		$no_structure = $_POST['no_structure'];
		if ($no_sous_tag)
		{
			$sql_delete_sous_tag="DELETE FROM structure_sous_tag
						WHERE no_sous_tag=:no_sous_tag
						AND no_structure=:no_structure";
			$delete_sous_tag = $connexion->prepare($sql_delete_sous_tag);
			$delete_sous_tag->execute(array(':no_sous_tag'=>$no_sous_tag, ':no_structure'=>$no_structure)) or die ("Erreur ".__LINE__." : ".$sql_delete_sous_tag);
		}
	}
	
	// Evenement
	if ((is_numeric($_POST['no_tag'])) && (is_numeric($_POST['no_evenement']))) {
		$no_tag = $_POST['no_tag'];
		$no_evenement = $_POST['no_evenement'];
		if ($no_tag)
		{
			$sql_delete_tag="DELETE FROM evenement_tag
						WHERE no_tag=:no_tag
						AND no_evenement=:no_evenement";
			$delete_tag = $connexion->prepare($sql_delete_tag);
			$delete_tag->execute(array(':no_tag'=>$no_tag, ':no_evenement'=>$no_evenement)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);

		}
	}

	// Petite annonce
	if ((is_numeric($_POST['no_tag'])) && (is_numeric($_POST['no_pa']))) {
		$no_tag = $_POST['no_tag'];
		$no_pa = $_POST['no_pa'];
		if ($no_tag)
		{
			$sql_delete_tag="DELETE FROM petiteannonce_tag
						WHERE no_tag=:no_tag
						AND no_petiteannonce=:no_pa";
			$delete_tag = $connexion->prepare($sql_delete_tag);
			$delete_tag->execute(array(':no_tag'=>$no_tag, ':no_pa'=>$no_pa)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);

		}
	}


?>