<?php
/*****************************************************
Suppression d'un item
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

/* Formatage */
$id_item = intval($_REQUEST['no_item']);
$non_ajax = intval($_REQUEST['non_ajax']); //suppression non ajax

if ($id_item) {

    // Récup des noms des fichiers illustrations à supprimer
    $sql_img="SELECT url_image
		      FROM `petiteannonce`
		      WHERE no=:no_item";
    $delete_img = $connexion->prepare($sql_img);
    $delete_img->execute(array(':no_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$sql_img);
    $tab_delete_img=$delete_img->fetchAll();
    $fichier_img = $tab_delete_img[0][url_image];
    if (($fichier_img) && (file_exists("../../".$fichier_img)))
      unlink("../../".$fichier_img);
    // Suppression des images dans le cache
    $cacheName = md5($fichier_img).'-'.basename($fichier_img);
    $chemin_img_mini_g = "../../".$chemin_img_mini.$cacheName;
    foreach (glob($chemin_img_mini_g."*") as $filename) {
        unlink($filename);
    }

    // Suppression des tags associés
    $sql_delete_tag="DELETE FROM `petiteannonce_tag`
			    WHERE no_petiteannonce=:no_item";
    $delete_tag = $connexion->prepare($sql_delete_tag);
    $delete_tag->execute(array(':no_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);

    // Boucle sur les contacts de l'item
    $sql_co="SELECT * 
		FROM `petiteannonce_contact`
		WHERE no_petiteannonce=:no_item";
    $res_co = $connexion->prepare($sql_co);
    $res_co->execute(array(':no_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$sql_co);
    $rows_co=$res_co->fetchAll();
    foreach($rows_co as $row_co)
    {
	// Suppression du contact
	$sql_delete_asso1="DELETE FROM `contact`
				WHERE no=:no_contact";
	$delete_asso1 = $connexion->prepare($sql_delete_asso1);
	$delete_asso1->execute(array(':no_contact'=>$row_co["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_delete_asso1);

	// Boucle sur les modifications de ce contact
	$sql_ca="SELECT *
		    FROM `contact_modification`
		    WHERE no_contact=:no_contact";
	$res_ca = $connexion->prepare($sql_ca);
	$res_ca->execute(array(':no_contact'=>$row_co["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_ca);
	$rows_ca=$res_ca->fetchAll();
	foreach($rows_ca as $row_ca)
	{
	    // Suppression du contact backup
	    $sql_delete_a="DELETE FROM `contact_temp`
				    WHERE no=:no_contact";
	    $delete_a = $connexion->prepare($sql_delete_a);
	    $delete_a->execute(array(':no_contact'=>$row_ca["no_contact_temp"])) or die ("Erreur ".__LINE__." : ".$sql_delete_a);
	}
	// Suppression de l'association entre le contact et ses modifications
	$sql_delete_b="DELETE FROM `contact_modification`
			WHERE no_contact=:no_contact";
	$delete_b = $connexion->prepare($sql_delete_b);
	$delete_b->execute(array(':no_contact'=>$row_co["no_contact"])) or die ("Erreur ".__LINE__." : ".$sql_delete_b);
    }
    // Suppression de l'association entre l'evt et le contact principal
    $sql_delete_contact="DELETE FROM `petiteannonce_contact`
			    WHERE no_petiteannonce=:no_item";
    $delete_contact = $connexion->prepare($sql_delete_contact);
    $delete_contact->execute(array(':no_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$sql_delete_contact);

    // Suppression de l'évenement
    $sql_delete="DELETE FROM `petiteannonce`
			    WHERE no=:no_item";
    $delete = $connexion->prepare($sql_delete);
    $delete->execute(array(':no_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$sql_delete);
    $nb_supp = $delete->rowCount();
}

// Message de retour
if ($nb_supp)
{
    $_SESSION['message'] .= $cc_supp." avec succès.<br/>";
    if (!$non_ajax) echo "ok";
}
else
    $_SESSION['message'] .= "Erreur dans la suppression ".$cc_de.".<br/>".$sql_delete;

if ($non_ajax)
    header("location:admin.php");
?>