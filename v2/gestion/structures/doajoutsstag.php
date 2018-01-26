<?php
/*****************************************************
Gestion des structure : ajout de sous-tags
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_structure = intval($_POST['id_structure']);

if (!$id_structure){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {

	$tab_tag_reponse=$_POST['sous_tag'];
	$no_vie_reponse=$_POST['form_vie'];
	
	if(count($tab_tag_reponse)>0)
	{
		// SS-Tags cochés
		for($indice_tag=0; $indice_tag<count($tab_tag_reponse); $indice_tag++)
		{
			$sql_test_clef="SELECT * FROM structure_sous_tag WHERE no_structure=:no_structure AND no_sous_tag=:no_sous_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_structure'=>$id_structure, ':no_sous_tag'=>$tab_tag_reponse[$indice_tag])) or die ("requete ligne 97 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_structures = "INSERT INTO structure_sous_tag (no_structure, no_sous_tag, description) VALUES (:no_structure, :no_sous_tag, :description)";
				$insert = $connexion->prepare($sql_structures);
				$insert->execute(array(':no_structure'=>$id_structure, ':no_sous_tag'=>$tab_tag_reponse[$indice_tag], ':description'=>$_POST['description'])) or die ("requete ligne 24 : ".$sql_structures);
			}
		}
	}
	else
	{
		
		$sql_vie_tag="SELECT * FROM tag_sous_tag WHERE no_tag=:no_tag";
		$res_vie_tag = $connexion->prepare($sql_vie_tag);
		$res_vie_tag->execute(array(':no_tag'=>$no_vie_reponse)) or die ("requete ligne 97 : ".$sql_vie_tag);
		$tab_vie_tag=$res_vie_tag->fetchAll();
		
		for($indice_tag=0; $indice_tag<count($tab_vie_tag); $indice_tag++)
		{
			$sql_test_clef="SELECT * FROM structure_sous_tag WHERE no_structure=:no_structure AND no_sous_tag=:no_sous_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_structure'=>$id_structure, ':no_sous_tag'=>$tab_vie_tag[$indice_tag]['no_sous_tag'])) or die ("requete ligne 97 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_structures = "INSERT INTO structure_sous_tag (no_structure, no_sous_tag, description) VALUES (:no_structure, :no_sous_tag, :description)";
				$insert = $connexion->prepare($sql_structures);
				$insert->execute(array(':no_structure'=>$id_structure, ':no_sous_tag'=>$tab_vie_tag[$indice_tag]['no_sous_tag'], ':description'=>$_POST['description'])) or die ("requete ligne 24 : ".$sql_structures);
			}
		}
	}


        $_SESSION['message'] .= "Sous-tags de la structure modifiés avec succès.<br/>";
}

?>