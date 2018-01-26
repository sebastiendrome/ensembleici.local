<?php
/*****************************************************
Gestion des genres
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$query = "SELECT * FROM `genre` G";
$res = $connexion->prepare($query);
$res->execute() or die ("Erreur ".__LINE__." : ".$query);
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

    echo "<p class='nb_evts'>".$nombre_res." $cc_min(s)</p>";

    echo '<form id="adform1" action="" method="post" class="formA avecajout">';
    echo '<a href="modifajout.php?ajout=1" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout">Ajout d\''.$cc_une.'</a></form>';

    echo "<table class=\"tablesorter\">\n";
  	echo "<thead>\n";
  	echo "<tr>\n";
  	echo "<th>Nom $cc_de</th>\n";
  	echo "<th>Type</th>\n";
  	echo "<th>Nb évèn.</th>\n";
  	echo "<th colspan=\"2\"></th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	{
	    $des_users = true;

	    // Nb d'evts liés ?
	    $sqle="SELECT COUNT(*) AS nb_evts
			      FROM `evenement` E
			      WHERE no_genre=:no";
	    $rese = $connexion->prepare($sqle);
	    $rese->execute(array(':no'=>$row["no"])) or die ("Erreur ".__LINE__." : ".$sqle);
	    $rowe=$rese->fetchAll();
	    $nb_evts = $rowe[0]["nb_evts"];

	    // Actif ?
	    if ($row["type_genre"]=="A"){
	      $libAE = "Annonce";
	    } else {
	      $libAE = "Evènement";
	    }

		echo "<tr>\n";

	    // Nom
	    echo "<td><a title=\"".$row['libelle']."\" href='modifajout.php?id=".$row['no']."'>".$row["libelle"]."</a></td>\n";

	    // Type Annonce / Evenement
	    echo "<td>".$libAE."</td>\n";

	    // Nb evts
	    echo "<td>".$nb_evts."</td>\n";

	    echo "<td class=\"avecicone\"><a href='modifajout.php?id=".$row["no"]."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a> </td>\n";
	    echo "<td class=\"avecicone\"><a href='#' id=\"".$row["no"]."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
	  echo "</tr>\n";
  	}
	// Aucune ligne ?
	if (!$des_users) echo "<tr><td colspan =\"5\">Aucune</td></tr>";
	echo "</tbody>\n";
	echo "</table>";
  } else {
  	echo "<p>Aucun élément</p>"; 
  }

?>