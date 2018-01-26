<?php
/*****************************************************
Association des sstags à un tag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
	
if (!$id_tag)
{
  if ($_POST['id'])
    $id_tag = intval($_POST['id']);
}

if ($id_tag)
{
    // Tags
    $sql_elt="SELECT *
		FROM `tag_sous_tag` T, `sous_tag` S
		WHERE T.no_sous_tag = S.no
		AND no_tag=:no";
    $res_elt= $connexion->prepare($sql_elt);
    $res_elt->execute(array(':no'=>$id_tag)) or die ("Erreur 23 : ".$sql_elt);
    $tab_elt=$res_elt->fetchAll();

  // Affiche les tags
  if (count($tab_elt))
  {
    echo "<ul>";
    foreach ($tab_elt as $sstag) {
	echo "<li>".$sstag["titre"];
	// $no_tag = $sstag['no'];	
	echo "&nbsp;&nbsp;<a id=\"".$sstag['no']."\" href=\"#\" class=\"deletesstag\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer cette association\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

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
