<?php
/*****************************************************
Affichage des sous tags d'une structure
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

if (!$id_structure)
{
  if ($_POST['id'])
    $id_structure = intval($_POST['id']);
}

if ($id_structure)
{
  // Sous-tags
  $sql_tag="SELECT no, titre, description
	      FROM `structure_sous_tag` S, `sous_tag` T
	      WHERE S.no_sous_tag = T.no
	      AND no_structure=:no";
  $res_tag = $connexion->prepare($sql_tag);
  $res_tag->execute(array(':no'=>$id_structure)) or die ("Erreur 30 : ".$sql_tag);
  $tab_ss_tag=$res_tag->fetchAll();

  // Affiche les sous-tags
  if (count($tab_ss_tag))
  {
    echo "<ul>";
    foreach ($tab_ss_tag as $sstag) {
	echo "<li>".ucfirst($sstag["titre"]);
	$no_sous_tag = $sstag['no'];
	$sous_tag_description = $sstag['description'];

	// Dans quels tags ?
	$sql_t="SELECT titre
		    FROM `tag` T,
			 `tag_sous_tag` S
		    WHERE no_sous_tag=:no_sous_tag
		    AND T.no = S.no_tag";
	$res_t = $connexion->prepare($sql_t);
	$res_t->execute(array(':no_sous_tag'=>$no_sous_tag)) or die ("Erreur 257 : ".$sql_t);
	$tab_t=$res_t->fetchAll();
	echo " <sup>( - ";
	foreach ($tab_t as $tag) {
	    echo $tag["titre"]." - ";
	}
	echo ")</sup>";
	
	echo "&nbsp;&nbsp;<a id=\"".$sstag['no']."\" href=\"#\" class=\"deletetag\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"12\" width=\"12\" class=\"icone\" /></a>";
	if (!empty($sous_tag_description))
	    echo "<ul class='uldescript'><li>".utf8_encode($sous_tag_description)."</li></ul>";

	echo "</li>";
    }
    echo "</ul>";
  }
}
?>
