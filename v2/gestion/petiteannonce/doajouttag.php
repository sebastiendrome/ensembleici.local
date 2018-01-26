<?php
/*****************************************************
Ajout d'un tag à un élement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
// require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_elt = intval($_POST['id_elt']);

if (!$id_elt) {
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
			$sql_test_clef="SELECT * FROM petiteannonce_tag WHERE no_petiteannonce=:no_petiteannonce AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_petiteannonce'=>$id_elt, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur 97 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_petiteannonces = "INSERT INTO petiteannonce_tag (no_petiteannonce, no_tag) VALUES (:no_petiteannonce, :no_tag)";
				$insert = $connexion->prepare($sql_petiteannonces);
				$insert->execute(array(':no_petiteannonce'=>$id_elt, ':no_tag'=>$tab_tag_reponse[$indice_tag])) or die ("Erreur 38 : ".$sql_petiteannonces);
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
			$sql_test_clef="SELECT * FROM petiteannonce_tag WHERE no_petiteannonce=:no_petiteannonce AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_petiteannonce'=>$id_elt, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur 97 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_petiteannonces = "INSERT INTO petiteannonce_tag (no_petiteannonce, no_tag) VALUES (:no_petiteannonce, :no_tag)";
				$insert = $connexion->prepare($sql_petiteannonces);
				$insert->execute(array(':no_petiteannonce'=>$id_elt, ':no_tag'=>$tab_vie_tag[$indice_tag]['no_tag'])) or die ("Erreur 60 : ".$sql_petiteannonces);
			}
		}
	}


        $_SESSION['message'] .= "Tags ".$cc_de." modifié(s) avec succès.<br/>";
}
?>