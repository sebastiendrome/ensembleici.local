<?php
/*****************************************************
Association des tags à un sstag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
	
if (!$id_sstag)
{
  if ($_POST['id'])
    $id_sstag = intval($_POST['id']);
}

if ($id_sstag)
{
    // Tags
    $sql_elt="SELECT *
		FROM `tag_sous_tag` T, `tag` S
		WHERE T.no_tag = S.no
		AND no_sous_tag=:no";
    $res_elt= $connexion->prepare($sql_elt);
    $res_elt->execute(array(':no'=>$id_sstag)) or die ("Erreur 23 : ".$sql_elt);
    $tab_elt=$res_elt->fetchAll();

  // Affiche les tags
  if (count($tab_elt))
  {
    echo "<ul>";
    foreach ($tab_elt as $tag) {
	echo "<li>".$tag["titre"];
	// $no_tag = $sstag['no'];	
	echo "&nbsp;&nbsp;<a id=\"".$tag['no']."\" href=\"#\" class=\"deletetag\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer cette association\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

	echo "</li>";
    }
    echo "</ul>";
  }
  else
  {
    echo "Aucun";
  }
}
?>
