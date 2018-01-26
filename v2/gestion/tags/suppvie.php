<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_tag = $_POST['no_tag'];
	$no_vie = $_POST['no_vie'];

	if ((is_numeric($no_tag)) && (is_numeric($no_vie)))
	{
		if ($no_tag)
		{
			$sql_delete_bis="DELETE FROM vie_tag
						WHERE no_tag=:no_tag
						AND no_vie=:no_vie";
			$delete_bis = $connexion->prepare($sql_delete_bis);
			$delete_bis->execute(array(
						   ':no_tag'=>$no_tag,
						   ':no_vie'=>$no_vie
						)) or die ("requete ligne 20 : ".$sql_delete_bis);

		}
	}


?>