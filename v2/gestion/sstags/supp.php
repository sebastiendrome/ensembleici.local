<?php
/*****************************************************
Suppression d'un sstag et de ses associations
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$id_elt = intval($_REQUEST['no']);
$non_ajax = intval($_REQUEST['non_ajax']); // suppression non ajax

if ($id_elt) {

    // Suppression des tags associés
    $sql_delete_sstag="DELETE FROM `tag_sous_tag` WHERE no_sous_tag=:no_elt";
    $delete_sstag = $connexion->prepare($sql_delete_sstag);
    $delete_sstag->execute(array(':no_elt'=>$id_elt)) or die ("Erreur ".__LINE__." : ".$sql_delete_sstag);

    // Suppression des structures associés
    $sql_delete_struct="DELETE FROM `structure_sous_tag` WHERE no_sous_tag=:no_elt";
    $delete_struct = $connexion->prepare($sql_delete_struct);
    $delete_struct->execute(array(':no_elt'=>$id_elt)) or die ("Erreur ".__LINE__." : ".$sql_delete_struct);

    // Suppression de l'element
    $sql_delete="DELETE FROM `sous_tag` WHERE no=:no_elt";
    $delete = $connexion->prepare($sql_delete);
    $delete->execute(array(':no_elt'=>$id_elt)) or die ("Erreur ".__LINE__." : ".$sql_delete);
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