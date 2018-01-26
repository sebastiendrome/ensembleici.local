<?php
/*****************************************************
Suppression de l'image
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$id_item = intval($_POST['id']);
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
    $chemin_img_mini = "../../".$chemin_img_mini.$cacheName;
    echo $chemin_img_mini;
    foreach (glob($chemin_img_mini."*") as $filename) {
        unlink($filename);
    }

    // Update de l'evt pour supprimer le fichier 
    $sql_upd = "UPDATE `petiteannonce` 
	SET url_image = :url_image
	WHERE no=:no_item";
    $upd_c = $connexion->prepare($sql_upd);
    $upd_c->execute(array(
	':url_image'=>"",
	':no_item'=>$id_item
    )) or die ("Erreur ".__LINE__." : ".$sql_upd);

}
?>