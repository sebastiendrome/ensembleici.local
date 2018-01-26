<?php
/*****************************************************
Suppression d'un évènement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

/* Formatage */
$id_structure = intval($_REQUEST['no_structure']);
$non_ajax = intval($_REQUEST['non_ajax']); //suppression non ajax
    
if ($id_structure) {

    // Récup des noms des fichiers illustrations à supprimer
    $sql_img="SELECT url_logo
		      FROM `structure`
		      WHERE no=:no_structure";
    $delete_img = $connexion->prepare($sql_img);
    $delete_img->execute(array(':no_structure'=>$id_structure)) or die ("Erreur 19 : ".$sql_img);
    $tab_delete_img=$delete_img->fetchAll();
    $fichier_img = $tab_delete_img[0]['url_logo'];
    if (($fichier_img) && (file_exists("../../".$fichier_img)))
      unlink("../../".$fichier_img);
      
    // Suppression des images dans le cache
    $cacheName = md5($fichier_img).'-'.basename($fichier_img);
    $chemin_img_mini_g = "../../".$chemin_img_mini.$cacheName;
    foreach (glob($chemin_img_mini_g."*") as $filename) {
        unlink($filename);
    }

    // Suppression des sous-tags associés
    $sql_delete_tag="DELETE FROM `structure_sous_tag`
			    WHERE no_structure=:no_structure";
    $delete_tag = $connexion->prepare($sql_delete_tag);
    $delete_tag->execute(array(':no_structure'=>$id_structure)) or die ("Erreur 29 : ".$sql_delete_tag);
    // Suppression des sous-tags backup associés
    $sql_delete_tagb="DELETE FROM `structure_sous_tag_temp`
			    WHERE no_structure_temp=:no_structure";
    $delete_tagb = $connexion->prepare($sql_delete_tagb);
    $delete_tagb->execute(array(':no_structure'=>$id_structure)) or die ("Erreur ".__LINE__." : ".$sql_delete_tagb);

    // Suppression des contacts associés
    
    // Boucle sur les contacts de la structure
    $sql_co="SELECT *
		FROM `structure_contact`
		WHERE no_structure=:no_structure";
    $res_co = $connexion->prepare($sql_co);
    $res_co->execute(array(':no_structure'=>$id_structure)) or die ("Erreur ".__LINE__." : ".$sql_co);
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
    $sql_delete_contact="DELETE FROM `structure_contact`
			    WHERE no_structure=:no_structure";
    $delete_contact = $connexion->prepare($sql_delete_contact);
    $delete_contact->execute(array(':no_structure'=>$id_structure)) or die ("Erreur ".__LINE__." : ".$sql_delete_contact);



    // Suppression des liaisons associées
    $sql_mod="SELECT *
		FROM `structure_modification`
		WHERE no_structure=:no_structure";
    $res_mod = $connexion->prepare($sql_mod);
    $res_mod->execute(array(':no_structure'=>$id_structure)) or die ("Erreur 59 : ".$sql_mod);
    $rows_mod=$res_mod->fetchAll();
    foreach($rows_mod as $row_mod)
    {
        // Supprimer les images des structures de `structure_temp`
	$sql_img_temp="SELECT url_logo
			  FROM `structure_temp`
			  WHERE no=:no_structure";
	$delete_img_temp = $connexion->prepare($sql_img_temp);
	$delete_img_temp->execute(array(':no_structure'=>$row_mod['no_structure_temp'])) or die ("Erreur ".__LINE__." : ".$sql_img_temp);
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

        // Suppression des structures temps
        $sql_delete_temp="DELETE FROM `structure_temp`
                                WHERE no=:no_structure_temp";
        $delete_temp = $connexion->prepare($sql_delete_temp);
        $delete_temp->execute(array(':no_structure_temp'=>$row_mod['no_structure_temp'])) or die ("Erreur ".__LINE__." : ".$sql_delete_temp);
        
    }

    // Suppression
    $sql_delete="DELETE FROM `structure`
			    WHERE no=:no_structure";
    $delete = $connexion->prepare($sql_delete);
    $delete->execute(array(':no_structure'=>$id_structure)) or die ("Erreur 41 : ".$sql_delete);
    $nb_supp = $delete->rowCount();
}

// Message de retour
if ($nb_supp)
{
    $_SESSION['message'] .= "Structure supprimée avec succès.<br/>";
    if (!$non_ajax) echo "ok";
}
else
    $_SESSION['message'] .= "Erreur dans la suppression de la structure.<br/>".$sql_delete;

if ($non_ajax)
    header("location:admin.php");
?>