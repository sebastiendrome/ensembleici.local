<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_liaison = intval($_POST['no_liaison']);

	if ($no_liaison)
	{
		$sql_delete_liaison="DELETE FROM liaisons
					WHERE no=:no_liaison";
		$delete_liaison = $connexion->prepare($sql_delete_liaison);
		$delete_liaison->execute(array(':no_liaison'=>$no_liaison)) or die ("Erreur ".__LINE__." : ".$sql_delete_liaison);
		echo "ok";
	        $_SESSION['message'] .= "Liaison supprimée avec succès.";
	}
?>