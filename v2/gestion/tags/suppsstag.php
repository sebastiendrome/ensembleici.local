<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_tag = $_POST['no_tag'];
	$no_sstag = $_POST['no_sstag'];

	if ((is_numeric($no_tag)) && (is_numeric($no_sstag)))
	{
		if ($no_tag)
		{
			$sql_delete_ter="DELETE FROM tag_sous_tag
						WHERE no_tag=:no_tag
						AND no_sous_tag=:no_sstag";
			$delete_ter = $connexion->prepare($sql_delete_ter);
			$delete_ter->execute(array(
						   ':no_tag'=>$no_tag,
						   ':no_sstag'=>$no_sstag
						)) or die ("requete ligne 20 : ".$sql_delete_ter);

		}
	}


?>