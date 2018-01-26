<?php
/*****************************************************
Gestion des utilisateurs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$query = "SELECT * FROM `utilisateur` U, `villes` V
WHERE U.no_ville=V.id";
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
  	echo "<th>Ad</th>\n";
  	echo "<th>Email $cc_de</th>\n";
  	echo "<th>Ville</th>\n";
  	echo "<th>Nb évèn.</th>\n";
  	echo "<th>Nb struct.</th>\n";
  	echo "<th>Lettre</th>\n";
  	echo "<th colspan=\"3\"></th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	{
	    $des_users = true;

	    // Nb d'evts liés ?
	    $sqle="SELECT COUNT(*) AS nb_evts
			      FROM `evenement` E
			      WHERE no_utilisateur_creation=:no";
	    $rese = $connexion->prepare($sqle);
	    $rese->execute(array(':no'=>$row["no"])) or die ("Erreur ".__LINE__." : ".$sqle);
	    $rowe=$rese->fetchAll();
	    $nb_evts = $rowe[0]["nb_evts"];
	    
	    // Nb de structures liées ?
	    $sqlv="SELECT COUNT(*) AS nb_structs
			      FROM `structure` S
			      WHERE no_utilisateur_creation=:no";
	    $resv = $connexion->prepare($sqlv);
	    $resv->execute(array(':no'=>$row["no"])) or die ("Erreur ".__LINE__." : ".$sqlv);
	    $rowv=$resv->fetchAll();
	    $nb_structs = $rowv[0]["nb_structs"];
	    
	    // Actif ?
	    if ($row["etat"]==1){
	      $actif = true;
	      $actdesactlib = "<img src=\"../../img/admin/icoad-checkbox-on.png\" alt=\"Cliquez pour désactiver\" title=\"Cliquez pour désactiver\" height=\"16\" width=\"16\" class=\"icone\" />";
	      $action = "desact";
	    } else {
	      $actif = false;
	      $actdesactlib = "<img src=\"../../img/admin/icoad-checkbox-off.png\" alt=\"Cliquez pour activer\" title=\"Cliquez pour activer\" height=\"16\" width=\"16\" class=\"icone\" />";
	      $action = "act";
	    }

	    if ($actif)
	    {
		echo "<tr>\n";
	    }
	    else
	    {
		echo "<tr class='nonactif'>\n";
	    }

	    // Admin ?
	    if ($row["droits"]=="A")
			echo "<td class=\"aveclib\"><span>1</span><img src=\"../../img/admin/medal_gold_3.png\" alt=\"Administrateur\" height=\"16\" width=\"16\" class=\"icone\" /></td>\n";
		elseif ($row["droits"]=="E")
			echo "<td class=\"aveclib\"><span>2</span><img src=\"../../img/admin/medal_silver_3.png\" alt=\"Editeur\" height=\"16\" width=\"16\" class=\"icone\" /></td>\n";
	    else
			echo "<td></td>\n";
	    
	    // Nom
	    echo "<td><a title=\"".$row['email']."\" href='modifajout.php?id=".$row['no']."'>".$row["email"]."</a></td>\n";
	    
	    // Ville
	    echo "<td>".$row["nom_ville_maj"]." (".$row["code_postal"].")</td>\n";
	    
	    // Nb evts / structs / vie
	    echo "<td>".$nb_evts."</td>\n";
	    echo "<td>".$nb_structs."</td>\n";
		
		//Abonnement NL
		if((bool)$row["newsletter"])
			$btn_src = "../../img/admin/nl_on.png";
		else
			$btn_src = "../../img/admin/nl_off.png";
	    echo "<td><img src=\"".$btn_src."\" onclick=\"change_abonnement(this,".$row["no"].");\" style=\"cursor:pointer;width:50px;\" /></td>\n";
	    
    		echo "<td class=\"avecicone\"><a href='activ.php?action=".$action."&id=".$row[no]."'>".$actdesactlib."</a> </td>\n";
	    echo "<td class=\"avecicone\"><a href='modifajout.php?id=".$row["no"]."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a> </td>\n";
	    echo "<td class=\"avecicone\"><a href='#' id=\"".$row["no"]."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
	  echo "</tr>\n";
  	}
	// Aucune ligne ?
	if (!$des_users) echo "<tr><td colspan =\"6\">Aucune</td></tr>";
	echo "</tbody>\n";
	echo "</table>";
  } else {
  	echo "<p>Aucun élément</p>"; 
  }

?>