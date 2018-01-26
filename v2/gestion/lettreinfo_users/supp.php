<?php
/*****************************************************
Suppression d'un utilisateur
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$id_elt = intval($_REQUEST['no']);
$non_ajax = intval($_REQUEST['non_ajax']); // suppression non ajax

if ($id_elt) {
    $sql_delete="DELETE FROM `newsletter` WHERE no=:no_elt";
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