<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_tag = $_POST['no_tag'];
	$no_item = $_POST['no_item'];

	if ((is_numeric($no_tag)) && (is_numeric($no_item)))
	{
		if ($no_tag)
		{
			$sql_delete_tag="DELETE FROM petiteannonce_tag
						WHERE no_tag=:no_tag
						AND no_petiteannonce=:no_item";
			$delete_tag = $connexion->prepare($sql_delete_tag);
			$delete_tag->execute(array(':no_tag'=>$no_tag, ':no_item'=>$no_item)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);
			echo "ok";
	        $_SESSION['message'] .= "Tag supprimé avec succès.";
		}
	}
?>