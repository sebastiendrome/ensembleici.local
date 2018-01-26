<?php
  // Affichage une publicité aléatoire
  require_once ('01_include/_connect.php');

  $sql_pub = "SELECT * FROM `publicites`
            WHERE etat = :etat
            AND validite_du <= CURDATE()
            AND validite_au > CURDATE()";


  // $id_vie peut être à 0
  if (isset($id_vie))
    $sql_pub .= " AND id_vie = :id_vie";

  $sql_pub .= " ORDER BY rand()
             LIMIT 0,1";
  $res_pub = $connexion->prepare($sql_pub);
  $act_pub = 1;
  $res_pub->bindParam(':etat', $act_pub, PDO::PARAM_INT);
  if (isset($id_vie))
    $res_pub->bindParam(':id_vie', $id_vie, PDO::PARAM_INT);

  $res_pub->execute();

  while($tab_pub = $res_pub->fetch(PDO::FETCH_ASSOC))
  {
    if ((!empty($tab_pub["titre"]))||(!empty($tab_pub["contenu"])))
    {
      echo "<div id=\"publicite1\" class=\"blocA\">";
      // if (!empty($tab_pub["titre"])) echo "<h2>".$tab_pub["titre"]."</h2>";
      echo "<h2>Publicité / Réseaux</h2>";
      if (!empty($tab_pub["site"])) echo "<a href=\"".$tab_pub["site"]."\" target=\"_blank\" title=\"".$tab_pub["titre"]."\">";
      if (!empty($tab_pub["contenu"])) echo "<div class=\"contenu\">".nl2br($tab_pub["contenu"])."</div>";
      if (!empty($tab_pub["url_image"])) echo "<div class=\"contenuimg\"><img src=\"".$tab_pub["url_image"]."\" alt=\"".$tab_pub["titre"]."\"  width=\"266\" /></div>";              
      if (!empty($tab_pub["site"])) echo "</a>";
      
      echo "</div>";
    }
  }
?>