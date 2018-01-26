<?php
/*****************************************************
Gestion des genres
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Vérifications
$id_genre = intval($_POST['id_genre']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_genre){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier ou ajouter.<br/>";
} else {

    $libelle = strtolower(trim($_POST['libelle']));

    if (!$libelle)
    	$_SESSION['message'] .= "Erreur : $cc_auc_nom.<br/>";

    // Variables
    $type = $_POST['type'];
    if (!$type) $type = "E";

    if (!isset($_SESSION['message'])) {

	if ($mode_ajout)
	{
	    $sql_elt = "INSERT INTO `genre` (
				`no`,
				`libelle`,
				`type_genre`
			    ) VALUES (
				:no,
				:libelle,
				:type_genre
			    )";
	    $insert_elt = $connexion->prepare($sql_elt);
	    $insert_elt->execute(array(
			    ':no'=>$id_genre,
			    ':libelle'=>$libelle,
			    ':type_genre'=>$type
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$libelle\" ajouté avec succès.<br/>";
	}
	else
	{
	    // Requête BDD
	    $sql_elt = "UPDATE `genre`
		SET libelle=:libelle,
		    type_genre=:type_genre
		WHERE no=:no";
	    $maj_elt = $connexion->prepare($sql_elt);
	    $maj_elt->execute(array(
			    ':no'=>$id_genre,
			    ':libelle'=>$libelle,
			    ':type_genre'=>$type
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$libelle\" modifié avec succès.<br/>";
	}
	
	header("location:admin.php");
	exit();
    }
}

header("location:modifajout.php?id=$id_genre");
exit();
?>