<?php
/*****************************************************
Gestion des évènements
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_event = intval($_POST['id_event']);

if (!$id_event){
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
			$sql_test_clef="SELECT * FROM evenement_tag WHERE no_evenement=:no_evenement AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_evenement'=>$id_event, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur 97 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_evenements = "INSERT INTO evenement_tag (no_evenement, no_tag) VALUES (:no_evenement, :no_tag)";
				$insert = $connexion->prepare($sql_evenements);
				$insert->execute(array(':no_evenement'=>$id_event, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur 38 : ".$sql_evenements);
			}
		}
	}
	else
	{
		 // Vie sélectionnée
		$sql_vie_tag="SELECT * FROM vie_tag WHERE no_vie=:no_vie";
		$res_vie_tag = $connexion->prepare($sql_vie_tag);
		$res_vie_tag->execute(array(':no_vie'=>$no_vie_reponse)) or die ("requete ligne 97 : ".$sql_vie_tag);
		$tab_vie_tag=$res_vie_tag->fetchAll();
		
		for($indice_tag=0; $indice_tag<count($tab_vie_tag); $indice_tag++)
		{
			$sql_test_clef="SELECT * FROM evenement_tag WHERE no_evenement=:no_evenement AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_evenement'=>$id_event, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur 97 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_evenements = "INSERT INTO evenement_tag (no_evenement, no_tag) VALUES (:no_evenement, :no_tag)";
				$insert = $connexion->prepare($sql_evenements);
				$insert->execute(array(':no_evenement'=>$id_event, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur 60 : ".$sql_evenements);
			}
		}
	}


        $_SESSION['message'] .= "Tags de l'évenement modifiés avec succès.<br/>";
}
?>