<?php
/*****************************************************
Affichage des tags d'un élement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
	
if (!$id_item)
{
  if ($_POST['id'])
    $id_item = intval($_POST['id']);
}

if ($id_item)
{
    // Tags
    $sql_tag="SELECT no, titre
		FROM `petiteannonce_tag` P, `tag` T
		WHERE P.no_tag = T.no
		AND no_petiteannonce=:no";
    $res_tag = $connexion->prepare($sql_tag);
    $res_tag->execute(array(':no'=>$id_item)) or die ("Erreur ".__LINE__." : ".$sql_tag);
    $tab_tag=$res_tag->fetchAll();

  // Affiche les tags
  if (count($tab_tag))
  {
    echo "<ul>";
    foreach ($tab_tag as $tag) {
	echo "<li>".$tag["titre"];
	$no_tag = $tag['no'];

	// Dans quelles vies ?
	$sql_vie="SELECT libelle
		    FROM `vie_tag` I, `vie` V
		    WHERE no_tag=:no_tag
		    AND I.no_vie = V.no";
	$res_vie = $connexion->prepare($sql_vie);
	$res_vie->execute(array(':no_tag'=>$no_tag)) or die ("Erreur ".__LINE__." : ".$sql_vie);
	$tab_vie=$res_vie->fetchAll();
	echo " <sup>( ";
	foreach ($tab_vie as $vie) {
	    echo $vie["libelle"]." ";
	}
	echo ")</sup>";
	
	echo "&nbsp;&nbsp;<a id=\"".$tag['no']."\" href=\"#\" class=\"deletetag\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

	echo "</li>";
    }
    echo "</ul>";
  }
}
?>
