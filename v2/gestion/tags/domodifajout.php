<?php
/*****************************************************
Gestion des tag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// Vérifications
$id_tag = intval($_POST['id_tag']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_tag){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier ou ajouter.<br/>";
} else {

    $titre = $_POST['titre'];
    if (empty($titre))
    	$_SESSION['message'] .= "Erreur : $cc_auc_nom.<br/>";

    if (!isset($_SESSION['message'])) {

	if ($mode_ajout)
	{
	    // Variables non déclarées
	    $no_utilisateur_creation = intval($_POST['no_utilisateur_creation']);
	    
	    $sql_elt = "INSERT INTO `tag` (
				`no`,
				`titre`
			    ) VALUES (
				:no,
				:titre
			    )";
	    $insert_elt = $connexion->prepare($sql_elt);
	    $insert_elt->execute(array(
			    ':no'=>$id_tag,
			    ':titre'=>$titre
	    )) or die ("Erreur 40 : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$titre\" ajouté avec succès.<br/>";
	}
	else
	{
	    // Requête BDD
	    $sql_elt = "UPDATE `tag`
		SET titre=:titre
		WHERE no=:no";
	    $maj_elt = $connexion->prepare($sql_elt);
	    $maj_elt->execute(array(
			    ':titre'=>$titre,
			    ':no'=>$id_tag
	    )) or die ("Erreur 139 : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$titre\" modifié avec succès.<br/>";
	}
	
	header("location:admin.php");
	exit();
    }
}

header("location:modifajout.php?id=$id_tag");
exit();
?>