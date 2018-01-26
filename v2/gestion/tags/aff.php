<?php
/*****************************************************
Gestion des tags
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Date pour comparaison
$date_jour = explode("/", date('d/m/Y'));
$date_jour = $date_jour[2].$date_jour[1].$date_jour[0];

$query = "SELECT * FROM `tag`";
$res = $connexion->prepare($query);
$res->execute() or die ("Erreur 16.");
$nombre_res = $res->rowCount();

  if($nombre_res>0)
  {
      echo "<div class=\"filtres\">";
      echo "<form name=\"ETri\" id=\"ETri\" action=\"\" method=\"post\" accept-charset=\"UTF-8\">
	  &nbsp; Mode suppression : <input type=\"checkbox\" name=\"modesupp\" id=\"modesupp\" /> 
      </form>";
      echo "<script type=\"text/javascript\">
	  $(function(){
	      $(\"form#ETri select\").change(function() {
		$(\"form#ETri\").submit();
	      });
	      
	      // Mode suppression (désactive les boutons delete)
	      $('#modesupp').click(function() {
		    if($('#modesupp').is(':checked')) {
		      $('.delete').css({ opacity: 1 });		      
		      $('#modesupp').attr('Checked','Checked'); 
		    } else {
		      $('.delete').css({ opacity: 0.5 });
		      $('#modesupp').removeAttr('Checked');
		    }
	      });
	      if ($('#modesupp').not(':checked')) {
		$('.delete').css({ opacity: 0.5 });
	      }
	  });
      </script>";
      echo "</div>";

    echo "<p class='nb_evts'>".$nombre_res." tags</p>";

    echo '<form id="adform1" action="" method="post" class="formA avecajout">';
    echo '<a href="modifajout.php?ajout=1" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout">Ajout d\''.$cc_une.'</a></form>';

    echo "<table class=\"tablesorter\">\n";
  	echo "<thead>\n";
  	echo "<tr>\n";
  	echo "<th>Nom $cc_de</th>\n";
  	echo "<th>Nb évèn.</th>\n";
  	echo "<th>Nb sous-tags.</th>\n";
  	echo "<th>Nb vies</th>\n";
  	echo "<th colspan=\"2\"></th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	{
	    $i++;

	    // Nb d'evts liés ?
	    $sqle="SELECT COUNT(*) AS nb_evts
			      FROM `evenement_tag` T
			      WHERE no_tag=:no_tag";
	    $rese = $connexion->prepare($sqle);
	    $rese->execute(array(':no_tag'=>$row["no"])) or die ("Erreur 88 : ".$sqle);
	    $rowe=$rese->fetchAll();
	    $nb_evts = $rowe[0]["nb_evts"];

	    // Nb de sous tags liés ?
	    $sqls="SELECT COUNT(*) AS nb_sstags
			      FROM `tag_sous_tag` T
			      WHERE no_tag=:no_tag";
	    $ress = $connexion->prepare($sqls);
	    $ress->execute(array(':no_tag'=>$row["no"])) or die ("Erreur 97 : ".$sqls);
	    $rows=$ress->fetchAll();
	    $nb_sstags = $rows[0]["nb_sstags"];
	    
	    // Nb de vies liés ?
	    $sqlv="SELECT COUNT(*) AS nb_vies
			      FROM `vie_tag` v
			      WHERE no_tag=:no_tag";
	    $resv = $connexion->prepare($sqlv);
	    $resv->execute(array(':no_tag'=>$row["no"])) or die ("Erreur 106 : ".$sqlv);
	    $rowv=$resv->fetchAll();
	    $nb_vies = $rowv[0]["nb_vies"];
	    
	    
	    // Evenement importé ?
	    if ((!$valide)&&(!empty($row["source_nom"])))
	      $validlib = "<span>3</span><img src=\"../../img/admin/bullet_blue.png\" alt=\"Importé, non validé\" title=\"Importé, non validé\" height=\"16\" width=\"16\" class=\"icone\" />";
	    
	    echo "<tr>\n";

	    // Nom
	    echo "<td><a title=\"".$row['titre']."\" href='modifajout.php?id=".$row['no']."'>".$row["titre"]."</a></td>\n";
	    
	    // Nb evts / structs / vie
	    echo "<td>".$nb_evts."</td>\n";
	    echo "<td>".$nb_sstags."</td>\n";
	    echo "<td>".$nb_vies."</td>\n";
      
	    echo "<td class=\"avecicone\"><a href='modifajout.php?id=".$row["no"]."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a> </td>\n";
	    echo "<td class=\"avecicone\"><a href='#' id=\"".$row["no"]."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
	  echo "</tr>\n";
  	}
    // Aucune ligne ?
  	if (!$i) echo "<tr><td colspan =\"6\">Aucune</td></tr>";
  	echo "</tbody>\n";
  	echo "</table>";
  } else {
  	echo "<p>Aucun élément</p>"; 
  }

?>