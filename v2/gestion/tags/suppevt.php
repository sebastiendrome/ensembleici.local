<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_tag = $_POST['no_tag'];
	$no_evt = $_POST['no_evt'];

	if ((is_numeric($no_tag)) && (is_numeric($no_evt)))
	{
		if ($no_tag)
		{
			$sql_delete_qua="DELETE FROM evenement_tag
						WHERE no_tag=:no_tag
						AND no_evenement=:no_evt";
			$delete_qua = $connexion->prepare($sql_delete_qua);
			$delete_qua->execute(array(
						   ':no_tag'=>$no_tag,
						   ':no_evt'=>$no_evt
						)) or die ("requete ligne 20 : ".$sql_delete_qua);

		}
	}


?>