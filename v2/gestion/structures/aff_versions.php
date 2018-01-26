<?php
/*****************************************************
Affichage de la liste des versions d'une structure
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
    // Des versions dans l'historique ?
    $sql_m = "SELECT * FROM `structure_modification` M, `structure_temp` T, `utilisateur` U    
				WHERE M.no_utilisateur = U.no
				AND M.no_structure_temp = T.no
				AND no_structure=:no_structure
				ORDER BY date_modification DESC";
    $res_m = $connexion->prepare($sql_m);
    $res_m -> execute(array(':no_structure'=>$id_structure)) or die ("Erreur ".__LINE__." : ".$sql_m);
    $tab_m = $res_m->fetchAll();

  // Affiche les versions
  if (count($tab_m))
  {
    echo "<table class='tablesorter'>";
    echo "<thead><tr>
	    <th>N°</th>
	    <th>Modifié par</th>
	    <th>Date de révision</th>
	    <th></th>
	    <th>Actions</th>
	  </tr></thead>";
    echo "<tbody>";
    foreach ($tab_m as $m) {
      echo "<tr>";

	echo "<td>".$m["no_structure_temp"]."</td>";
	echo "<td>".$m["email"]."</td>";
	echo "<td>".datefr($m["date_modification"],$avecheure=true)."</td>";  
	echo "<td></td>";
  
	echo "<td>";
	  echo "<a id=\"".$m['no_structure_temp']."\" href=\"#\" class=\"voir_version\"><img src=\"../../img/admin/icoad-rech.png\" alt=\"Voir cette version\" title=\"Voir cette version\" class=\"icone\" /></a>";
	  echo "&nbsp;&nbsp;<a id=\"".$m['no_structure_temp']."\" href=\"#\" class=\"restaurer_version\"><img src=\"../../img/admin/icoad_arrow_merge.png\" alt=\"Restaurer cette version\" title=\"Restaurer cette version\" class=\"icone\" /></a>";
	  echo "&nbsp;&nbsp;<a id=\"".$m['no_structure_temp']."\" href=\"#\" class=\"delete_version\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" class=\"icone\" /></a>";
	echo "</td>";
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

  } else
  {
    echo "Aucune autre que celle-ci.";    
  }
}
?>
