<?php
/*****************************************************
Suppression de l'image d'une structure
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$id_structure = intval($_POST['id']);
if ($id_structure) {
   
    // Récup des noms des fichiers illustrations à supprimer
    $sql_img="SELECT url_logo
		      FROM `structure`
		      WHERE no=:no_structure";
    $delete_img = $connexion->prepare($sql_img);
    $delete_img->execute(array(':no_structure'=>$id_structure)) or die ("Erreur 19 : ".$sql_img);
    $tab_delete_img=$delete_img->fetchAll();
    $fichier_img = $tab_delete_img[0][url_logo];
    if (($fichier_img) && (file_exists("../../".$fichier_img)))
      unlink("../../".$fichier_img);

    // Suppression des images dans le cache
    $cacheName = md5($fichier_img).'-'.basename($fichier_img);
    $chemin_img_mini = "../../".$chemin_img_mini.$cacheName;
    echo $chemin_img_mini;
    foreach (glob($chemin_img_mini."*") as $filename) {
        unlink($filename);
    }

    // Update pour supprimer le fichier 
    $sql_upd = "UPDATE `structure` 
	SET url_logo = :url_logo
	WHERE no=:no_structure";
    $upd_c = $connexion->prepare($sql_upd);
    $upd_c->execute(array(
	':url_logo'=>"",
	':no_structure'=>$id_structure
    )) or die ("Erreur 41 : ".$sql_upd);
    
}
?>