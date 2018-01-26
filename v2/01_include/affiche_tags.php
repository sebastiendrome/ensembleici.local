<?php
  // Affichage du bandeau des tags
  // Uniquement si une vie ou un tag est sélectionné
if ((($id_vie)&&(!empty($id_vie)))||(($id_tag)&&(!empty($id_tag))))
{
  require_once ('01_include/_connect.php');

  if (!empty($id_vie)) {
    
    // Quels sont les tags de la vie sélectionnée
    $sql_tag="SELECT no, titre
                FROM `vie_tag` V, `tag` T
                WHERE V.no_tag = T.no
                AND no_vie = :id_vie
                ORDER BY T.titre";
    $res_tag = $connexion->prepare($sql_tag);
    $res_tag->execute(array(':id_vie'=>$id_vie));
    $nb_tags = $res_tag->rowCount();

    if ($nb_tags>0)
    {
      echo "<div id=\"bandes-tags\" class=\"blocA\">";
      echo "<h4>Mots clés ($nb_tags) :</h4>";
      while($t_tag = $res_tag->fetch(PDO::FETCH_ASSOC))
      {
	$tag_actif = "";
        if ((!empty($t_tag["no"]))||(!empty($t_tag["titre"])))
        {
	    if ($id_tag==$t_tag["no"]) $tag_actif = " boutonbleuactif";
            $lien = $titre_ville_url.".".url_rewrite($t_tag["titre"]).".tag.".$id_ville.".".$t_tag["no"].".".$id_vie.".html";
            echo "<a href=\"".$lien."\" title=\"Afficher ".$t_tag["titre"]."\" class=\"boutonbleu$tag_actif\">".$t_tag["titre"]."</a>";
        }
      }
      echo "</div>";
      
    }
  }


}
?>

