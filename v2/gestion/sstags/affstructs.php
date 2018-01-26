<?php
/*****************************************************
Association des structures à un tag
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
    $sql_struct="SELECT *
		FROM `structure_sous_tag` T, `structure` S
		WHERE T.no_structure = S.no
		AND no_sous_tag=:no";
    $res_struct= $connexion->prepare($sql_struct);
    $res_struct->execute(array(':no'=>$id_sstag)) or die ("Erreur 22 : ".$sql_struct);
    $tab_struct=$res_struct->fetchAll();

  // Affiche les tags
  if (count($tab_struct))
  {
    echo "<ul>";
    foreach ($tab_struct as $struct) {
	$actif = false;
	$no_struct = $struct['no'];

	echo "<li>";
	
	if ($struct["etat"]==1)
	  $actif = true;

	echo "&nbsp;&nbsp;<a href='../structures/modifajout.php?id=".$no_struct."'>";
	if (!$actif)
	  echo "<span class=\"enrouge\">".$struct["nom"]."</span>";
	else
	  echo $struct["nom"];
	echo "</a>";

	echo "&nbsp;&nbsp;<a href='../structures/modifajout.php?id=".$no_struct."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a>\n";
	echo "&nbsp;<a id=\"".$struct['no']."\" href=\"#\" class=\"deletestruct\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer cette association\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

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
