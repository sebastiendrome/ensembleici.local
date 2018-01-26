<?php
/*****************************************************
Suppression d'un évènement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

/* Formatage */
$id_event = intval($_REQUEST['no_evenement']);
$non_ajax = intval($_REQUEST['non_ajax']); //suppression non ajax

if ($id_event) {

    // Récup des noms des fichiers illustrations à supprimer
    $sql_img="SELECT url_image
		      FROM `evenement`
		      WHERE no=:no_event";
    $delete_img = $connexion->prepare($sql_img);
    $delete_img->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_img);
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
    $sql_delete_tag="DELETE FROM `evenement_tag`
			    WHERE no_evenement=:no_event";
    $delete_tag = $connexion->prepare($sql_delete_tag);
    $delete_tag->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_delete_tag);
    
    // Suppression des contacts associés
    
    // Boucle sur les contacts de l'évt
    $sql_co="SELECT *
		FROM `evenement_contact`
		WHERE no_evenement=:no_event";
    $res_co = $connexion->prepare($sql_co);
    $res_co->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_co);
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
    $sql_delete_contact="DELETE FROM `evenement_contact`
			    WHERE no_evenement=:no_event";
    $delete_contact = $connexion->prepare($sql_delete_contact);
    $delete_contact->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_delete_contact);

    // Suppression des liaisons associées
    // SELECT pour recupérer les infos de evenement_modification
    $sql_mod="SELECT *
		FROM `evenement_modification`
		WHERE no_evenement=:no_event";
    $res_mod = $connexion->prepare($sql_mod);
    $res_mod->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_mod);
    $rows_mod=$res_mod->fetchAll();
    foreach($rows_mod as $row_mod)
    {
	// Suppression des tags associés à la sauvegarde
	$sql_delete_tagb="DELETE FROM `evenement_tag_temp`
				WHERE no_evenement_temp=:no_evenement_temp";
	$delete_tagb = $connexion->prepare($sql_delete_tagb);
	$delete_tagb->execute(array(':no_evenement_temp'=>$row_mod['no_evenement_temp'])) or die ("Erreur ".__LINE__." : ".$sql_delete_tagb);

        // Supprimer les images des evenements sauvegarde
	$sql_img_temp="SELECT url_image
			  FROM `evenement_temp`
			  WHERE no=:no_event";
	$delete_img_temp = $connexion->prepare($sql_img_temp);
	$delete_img_temp->execute(array(':no_event'=>$row_mod['no_evenement_temp'])) or die ("Erreur ".__LINE__." : ".$sql_img_temp);
	$tab_delete_img_temp=$delete_img_temp->fetchAll();
	$fichier_img_temp = $tab_delete_img_temp[0]['url_image'];
	if (($fichier_img_temp) && (file_exists("../../".$fichier_img_temp)))
	  unlink("../../".$fichier_img_temp);
	// Suppression des images dans le cache
	$cacheName = md5($fichier_img_temp).'-'.basename($fichier_img_temp);
	$chemin_img_mini_t = "../../".$chemin_img_mini.$cacheName;
	foreach (glob($chemin_img_mini_t."*") as $filename) {
	    unlink($filename);
	}

        // Suppression des évenements temps
        $sql_delete_temp="DELETE FROM `evenement_temp`
                                WHERE no=:no_event_temp";
        $delete_temp = $connexion->prepare($sql_delete_temp);
        $delete_temp->execute(array(':no_event_temp'=>$row_mod['no_evenement_temp'])) or die ("Erreur ".__LINE__." : ".$sql_delete_temp);
        
    }

    // Suppression de l'évenement
    $sql_delete="DELETE FROM `evenement`
			    WHERE no=:no_event";
    $delete = $connexion->prepare($sql_delete);
    $delete->execute(array(':no_event'=>$id_event)) or die ("Erreur ".__LINE__." : ".$sql_delete);
    $nb_supp = $delete->rowCount();
}

// Message de retour
if ($nb_supp)
{
    $_SESSION['message'] .= "Evènement supprimée avec succès.<br/>";
    if (!$non_ajax) echo "ok";
}
else
    $_SESSION['message'] .= "Erreur dans la suppression de l'évènement.<br/>".$sql_delete;

if ($non_ajax)
    header("location:admin.php");
?>