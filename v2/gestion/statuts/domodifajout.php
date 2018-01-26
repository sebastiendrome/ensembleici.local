<?php
/*****************************************************
Gestion des statuts
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Vérifications
$id_statut = intval($_POST['id_statut']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_statut){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier ou ajouter.<br/>";
} else {

    $libelle = strtolower(trim($_POST['libelle']));

    if (!$libelle)
    	$_SESSION['message'] .= "Erreur : $cc_auc_nom.<br/>";

    if (!isset($_SESSION['message'])) {

	if ($mode_ajout)
	{
	    $sql_elt = "INSERT INTO `statut` (
				`no`,
				`libelle`
			    ) VALUES (
				:no,
				:libelle
			    )";
	    $insert_elt = $connexion->prepare($sql_elt);
	    $insert_elt->execute(array(
			    ':no'=>$id_statut,
			    ':libelle'=>$libelle
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$libelle\" ajouté avec succès.<br/>";
	}
	else
	{
	    // Requête BDD
	    $sql_elt = "UPDATE `statut`
		SET libelle=:libelle
		WHERE no=:no";
	    $maj_elt = $connexion->prepare($sql_elt);
	    $maj_elt->execute(array(
			    ':no'=>$id_statut,
			    ':libelle'=>$libelle
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$libelle\" modifié avec succès.<br/>";
	}
	
	header("location:admin.php");
	exit();
    }
}

header("location:modifajout.php?id=$id_statut");
exit();
?>