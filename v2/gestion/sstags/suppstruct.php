<?php
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_sstag = $_POST['no_sstag'];
	$no_struct = $_POST['no_struct'];

	if ((is_numeric($no_sstag)) && (is_numeric($no_struct)))
	{
		if ($no_sstag)
		{
			$sql_delete_qua="DELETE FROM structure_sous_tag
						WHERE no_sous_tag=:no_tag
						AND no_structure=:no_struct";
			$delete_qua = $connexion->prepare($sql_delete_qua);
			$delete_qua->execute(array(
						   ':no_tag'=>$no_sstag,
						   ':no_struct'=>$no_struct
						)) or die ("requete ligne 20 : ".$sql_delete_qua);

		}
	}


?>