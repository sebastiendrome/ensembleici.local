<?php
/*****************************************************
Suppression d'un tag et de ses associations
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$id_elt = intval($_REQUEST['no']);
$non_ajax = intval($_REQUEST['non_ajax']); // suppression non ajax

if ($id_elt) {

    // Suppression des vies associés
    $sql_delete_vie="DELETE FROM `vie_tag` WHERE no_tag=:no_elt";
    $delete_vie = $connexion->prepare($sql_delete_vie);
    $delete_vie->execute(array(':no_elt'=>$id_elt)) or die ("Erreur 17 : ".$sql_delete_vie);

    // Suppression des sous-tags associés
    $sql_delete_sstag="DELETE FROM `tag_sous_tag` WHERE no_tag=:no_elt";
    $delete_sstag = $connexion->prepare($sql_delete_sstag);
    $delete_sstag->execute(array(':no_elt'=>$id_elt)) or die ("Erreur 22 : ".$sql_delete_sstag);

    // Suppression des evenements associés
    $sql_delete_evt="DELETE FROM `evenement_tag` WHERE no_tag=:no_elt";
    $delete_evt = $connexion->prepare($sql_delete_evt);
    $delete_evt->execute(array(':no_elt'=>$id_elt)) or die ("Erreur 27 : ".$sql_delete_evt);

    // Suppression de l'element
    $sql_delete="DELETE FROM `tag` WHERE no=:no_elt";
    $delete = $connexion->prepare($sql_delete);
    $delete->execute(array(':no_elt'=>$id_elt)) or die ("Erreur 32 : ".$sql_delete);
    $nb_supp = $delete->rowCount();
}

// Message de retour
if ($nb_supp)
{
    $_SESSION['message'] .= "$cc_supp avec succès.<br/>";
    if (!$non_ajax) echo "ok";
}
else
    $_SESSION['message'] .= "Erreur dans la suppression $cc_de.<br/>".$sql_delete;

if ($non_ajax)
    header("location:admin.php");
?>