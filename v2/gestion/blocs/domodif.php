<?php
/*****************************************************
Gestion des contenus des blocs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Vérifications
$id = intval($_POST['id_bloc']);
if (!$id){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier.<br/>";
} else {
  $titre = $_POST["titre"];
  $nom_bloc = $_POST["nom_bloc"];
  $contenu_bloc = $_POST['contenu'];

    if (!isset($_SESSION['message'])) {

      // Modif BDD
      $query = "UPDATE `contenu_blocs` SET
	  titre = :titre,
	  contenu = :contenu_bloc
	    WHERE `no`=:no
	    LIMIT 1";
	$maj = $connexion->prepare($query);
	$maj->execute(array(
			':titre'=>$titre,
			':contenu_bloc'=>$contenu_bloc,
			':no'=>$id
	)) or die ("Erreur 31 : ".$query);
	
        $_SESSION['message'] .= "Bloc \"$nom_bloc\" modifié avec succès.<br/>";
        header("location:admin.php");
        exit();
	
    }
}

header("location:modif.php?id=$id");
exit();
?>