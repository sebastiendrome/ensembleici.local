<?php
/*****************************************************
Gestion des sous tags : ajout d'un tag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Vérifications
$id_sstag = intval($_POST['id_sstag']);
$id_tag = intval($_POST['id_tag']);

if (!$id_sstag){
	// aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {

	$tab_tag_reponse=$_POST['tag'];
	$no_vie_reponse=$_POST['form_vie'];
	
	if(count($tab_tag_reponse)>0)
	{
		// Tags cochés
		for($indice_tag=0; $indice_tag<count($tab_tag_reponse); $indice_tag++)
		{
			// Test pour vérifier que le couple n'est pas déjà associé
			$sql_test_clef="SELECT * FROM tag_sous_tag WHERE no_sous_tag=:no_sous_tag AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_sous_tag'=>$id_sstag, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur 28 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_tags = "INSERT INTO tag_sous_tag (no_sous_tag, no_tag) VALUES (:no_sous_tag, :no_tag)";
				$insert = $connexion->prepare($sql_tags);
				$insert->execute(array(':no_sous_tag'=>$id_sstag, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur 34 : ".$sql_tags);
			}
		}
	}
	else
	{
		 // Vie sélectionnée
		$sql_vie_tag="SELECT * FROM vie_tag WHERE no_vie=:no_vie";
		$res_vie_tag = $connexion->prepare($sql_vie_tag);
		$res_vie_tag->execute(array(':no_vie'=>$no_vie_reponse)) or die ("requete ligne 44 : ".$sql_vie_tag);
		$tab_vie_tag=$res_vie_tag->fetchAll();
		
		for($indice_tag=0; $indice_tag<count($tab_vie_tag); $indice_tag++)
		{
			$sql_test_clef="SELECT * FROM tag_sous_tag WHERE no_sous_tag=:no_sous_tag AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_sous_tag'=>$id_sstag, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur 51 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_tags = "INSERT INTO tag_sous_tag (no_sous_tag, no_tag) VALUES (:no_sous_tag, :no_tag)";
				$insert = $connexion->prepare($sql_tags);
				$insert->execute(array(':no_sous_tag'=>$id_sstag, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur 57 : ".$sql_tags);
			}
		}
	}


        $_SESSION['message'] .= "Tag(s) associé(s) avec succès.<br/>";
}
?>