<?php
  // Affichage des descriptions des vies
  // Uniquement si une vie est sélectionnée
if (($id_vie)&&(!empty($id_vie)))
{
  require_once ('01_include/_connect.php');

  $sql_titre="SELECT description, libelle
              FROM `vie`
              WHERE no = :id_vie";
  $res_titre = $connexion->prepare($sql_titre);
  $res_titre->execute(array(':id_vie'=>$id_vie));
  $t_titre = $res_titre->fetch(PDO::FETCH_ASSOC);
  if ($t_titre["description"])
  {
    echo "<div id=\"intro_vie\"><div class=\"ladesc\">".utf8_encode($t_titre["description"])."</div></p>";
    echo "Ci-dessous, une série de mots-clé vous permet de consulter les pages thématiques associées à la ".strtolower($t_titre["libelle"]).". ";
    // echo "<a href=\"espace_personnel.html\" title=\"Ajouter une information\">Pour ajouter une information, cliquez ici.</a>";
    echo "</div>";
  }


}
?>

