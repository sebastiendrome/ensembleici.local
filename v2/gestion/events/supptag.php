<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_tag = $_POST['no_tag'];
	$no_evenement = $_POST['no_evenement'];

	if ((is_numeric($no_tag)) && (is_numeric($no_evenement)))
	{
		if ($no_tag)
		{
			$sql_delete_tag="DELETE FROM evenement_tag
						WHERE no_tag=:no_tag
						AND no_evenement=:no_evenement";
			$delete_tag = $connexion->prepare($sql_delete_tag);
			$delete_tag->execute(array(':no_tag'=>$no_tag, ':no_evenement'=>$no_evenement)) or die ("requete ligne 35 : ".$sql_delete_tag);
			echo "ok";
	        $_SESSION['message'] .= "Tag supprimé avec succès.";
		}
	}
?>