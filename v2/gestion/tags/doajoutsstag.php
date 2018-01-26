<?php
/*****************************************************
Gestion des tags : ajout de sous-tags
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_tag = intval($_POST['id_tag']);

if (!$id_tag){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {

	$tab_tag_reponse=$_POST['sous_tag'];
	$no_sstag_reponse=$_POST['form_sstag'];
	
	if(count($tab_tag_reponse)>0)
	{
		// SS-Tags cochés
		for($indice_tag=0; $indice_tag<count($tab_tag_reponse); $indice_tag++)
		{
			$sql_test_clef="SELECT * FROM tag_sous_tag WHERE no_tag=:no_tag AND no_sous_tag=:no_sous_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_tag'=>$id_tag, ':no_sous_tag'=>$tab_tag_reponse[$indice_tag])) or die ("requete ligne 28 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_sstags = "INSERT INTO tag_sous_tag (no_tag, no_sous_tag) VALUES (:no_tag, :no_sous_tag)";
				$insert = $connexion->prepare($sql_sstags);
				$insert->execute(array(':no_tag'=>$id_tag, ':no_sous_tag'=>$tab_tag_reponse[$indice_tag])) or die ("requete ligne 34 : ".$sql_sstags);
			}
		}
	}
	else
	{
		
		$sql_sstag_tag="SELECT * FROM tag_sous_tag WHERE no_tag=:no_tag";
		$res_sstag_tag = $connexion->prepare($sql_sstag_tag);
		$res_sstag_tag->execute(array(':no_tag'=>$no_sstag_reponse)) or die ("requete ligne 43 : ".$sql_sstag_tag);
		$tab_sstag_tag=$res_sstag_tag->fetchAll();
		
		for($indice_tag=0; $indice_tag<count($tab_sstag_tag); $indice_tag++)
		{
			$sql_test_clef="SELECT * FROM tag_sous_tag WHERE no_tag=:no_tag AND no_sous_tag=:no_sous_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_tag'=>$id_tag, ':no_sous_tag'=>$tab_sstag_tag[$indice_tag]['no_sous_tag'])) or die ("requete ligne 50 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_sstags = "INSERT INTO tag_sous_tag (no_tag, no_sous_tag) VALUES (:no_tag, :no_sous_tag)";
				$insert = $connexion->prepare($sql_sstags);
				$insert->execute(array(':no_tag'=>$id_tag, ':no_sous_tag'=>$tab_sstag_tag[$indice_tag]['no_sous_tag'])) or die ("requete ligne 56 : ".$sql_sstags);
			}
		}
	}


        $_SESSION['message'] .= "Sous-tag(s) associé(s) avec succès.<br/>";
}

?>