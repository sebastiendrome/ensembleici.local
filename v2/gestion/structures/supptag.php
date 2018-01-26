<?php
	
	session_name("EspacePerso");
	session_start();
	require_once "config.php";

	$no_sous_tag = $_POST['no_sous_tag'];
	$no_structure = $_POST['no_structure'];

	if ((is_numeric($no_sous_tag)) && (is_numeric($no_structure))) {
		if ($no_sous_tag)
		{
			$sql_delete_sous_tag="DELETE FROM structure_sous_tag
						WHERE no_sous_tag=:no_sous_tag
						AND no_structure=:no_structure";
			$delete_sous_tag = $connexion->prepare($sql_delete_sous_tag);
			$delete_sous_tag->execute(array(':no_sous_tag'=>$no_sous_tag, ':no_structure'=>$no_structure)) or die ("requete ligne 35 : ".$sql_delete_sous_tag);
			echo "ok";
	        $_SESSION['message'] .= "Tag supprimé avec succès.";
		}
	}

?>