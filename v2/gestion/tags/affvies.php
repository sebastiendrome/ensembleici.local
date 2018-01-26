<?php
/*****************************************************
Association des vies à un tag
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
    $sql_vie="SELECT *
		FROM `vie_tag` T, `vie` V
		WHERE T.no_vie = V.no
		AND no_tag=:no";
    $res_vie= $connexion->prepare($sql_vie);
    $res_vie->execute(array(':no'=>$id_tag)) or die ("Erreur 23 : ".$sql_vie);
    $tab_vie=$res_vie->fetchAll();

  // Affiche les tags
  if (count($tab_vie))
  {
    echo "<ul>";
    foreach ($tab_vie as $vie) {
	echo "<li>".$vie["libelle"];
	$no_tag = $vie['no'];	
	echo "&nbsp;&nbsp;<a id=\"".$vie['no']."\" href=\"#\" class=\"deletevie\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer cette association\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

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
