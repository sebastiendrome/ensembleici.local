<?php
/*****************************************************
Gestion des vies associées à un tag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Vérifications
$id_tag = intval($_POST['id_tag']);

if (!$id_tag){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {

	$tab_vie_reponse=$_POST['form_vie'];
	
	if(count($tab_vie_reponse)>0)
	{
		// vies cochés
		for($indice_vie=0; $indice_vie<count($tab_vie_reponse); $indice_vie++)
		{
			$sql_test_clef="SELECT * FROM vie_tag WHERE no_vie=:no_vie AND no_tag=:no_tag";
			$res_test_clef = $connexion->prepare($sql_test_clef);
			$res_test_clef->execute(array(':no_tag'=>$id_tag, ':no_vie'=>$tab_vie_reponse[$indice_vie])) or die ("Erreur 26 : ".$sql_test_clef);
			$tab_test_clef=$res_test_clef->fetchAll();
			if(count($tab_test_clef)==0)
			{
				$sql_ajout_couple = "INSERT INTO vie_tag (no_vie, no_tag) VALUES (:no_vie, :no_tag)";
				$insert_couple = $connexion->prepare($sql_ajout_couple);
				$insert_couple->execute(array(':no_tag'=>$id_tag, ':no_vie'=>$tab_vie_reponse[$indice_vie])) or die ("Erreur 32 : ".$sql_ajout_couple);
			}
		}
	}

        $_SESSION['message'] .= "Vie associée au tag avec succès.<br/>";
}
?>