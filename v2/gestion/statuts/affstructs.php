<?php
/*****************************************************
Association des structures liées à un statut
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

if (!$id_statut)
{
  if ($_POST['id'])
    $id_statut = intval($_POST['id']);
}

if ($id_statut)
{
    // Structures
    $sql_struct="SELECT *
		FROM `structure`
		WHERE no_statut=:no";
    $res_struct= $connexion->prepare($sql_struct);
    $res_struct->execute(array(':no'=>$id_statut)) or die ("Erreur ".__LINE__." : ".$sql_struct);
    $tab_struct=$res_struct->fetchAll();

  if (count($tab_struct))
  {
    echo "<ul>";
    foreach ($tab_struct as $struct) {
		$expire = false;
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
