<?php
/*****************************************************
Gestion des utilisateurs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$query = "SELECT lettreinfo.no,lettreinfo.date_debut,lettreinfo.objet,lettreinfo.repertoire,lettreinfo.date_modification,lettreinfo.date_creation,lettreinfo_envoi.no AS no_envoi,lettreinfo_envoi.date_fin FROM `lettreinfo` LEFT JOIN lettreinfo_envoi ON lettreinfo_envoi.no=lettreinfo.no_envoi WHERE lettreinfo.territoires_id = ".$territoire;
$res = $connexion->prepare($query);
$res->execute() or die ("Erreur ".__LINE__." : ".$query);
$nombre_res = $res->rowCount();

// Nb d'de lettres envoyées
$sqle="SELECT COUNT(*) AS nb_let
		  FROM `lettreinfo`
		  WHERE no_envoi<>0 AND territoires_id = ".$territoire;
$rese = $connexion->prepare($sqle);
$rese->execute() or die ("Erreur ".__LINE__." : ".$sqle);
$rowe=$rese->fetchAll();
$nb_lettre_envoyees = $rowe[0]["nb_let"];

$sql_ter = "SELECT * FROM territoires";
$res_ter = $connexion->prepare($sql_ter);
$res_ter->execute() or die ("Erreur ".__LINE__." : ".$sql_ter);
$rows_ter=$res_ter->fetchAll(PDO::FETCH_ASSOC);

  if($nombre_res>0)
  { 
      echo "<div class=\"filtres\">";
      ?>
<div style="text-align: left; margin-left: 10px;">
    Territoire : 
    <select id="sel_territoire">
        <?php foreach ($rows_ter as $row ) { ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $territoire) ? 'selected="selected"' : '' ?>><?= $row['nom'] ?></option>
        <?php } ?>
    </select>
</div>
    <?php
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

    echo "<p class='nb_evts'>".$nombre_res." $cc_min (".$nb_lettre_envoyees." envoyée(s))</p>";
	
	echo '';

    echo '<form id="adform1" action="" method="post" class="formA avecajout">';
//    echo '<a id="ajout_lettre" href="modifajout.php?ajout=1" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout">Ajout d\''.$cc_une.'</a><a href="../lettreinfo_users/admin.php" title="Voir la liste de diffusion (hors membres)" class="boutonbleu ico-fleche">Voir la liste de diffusion (hors membres)</a></form>';
    echo '<a id="ajout_lettre" href="" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout">Ajout d\''.$cc_une.'</a><a href="../lettreinfo_users/admin.php" title="Voir la liste de diffusion (hors membres)" class="boutonbleu ico-fleche">Voir la liste de diffusion (hors membres)</a></form>';

	
	
    echo "<table class=\"tablesorter\">\n";
  	echo "<thead>\n";
  	echo "<tr>\n";
  	echo "<th></th>\n";
  	echo "<th></th>\n";
  	echo "<th>Objet $cc_de</th>\n";
  	echo "<th>Date</th>\n";
  	echo "<th>Création</th>\n";
  	echo "<th>Modification</th>\n";
  	echo "<th>Date d'envoi</th>\n";
  	echo "<th colspan=\"2\"></th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	{
	    $des_lettres = true;
	    
		/*
	    // Actif ?
	    if ($row["date_envoi"]!="0000-00-00"){
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
		*/
		echo "<tr>\n";
		
		// envoyé (éditorial) ?
	    if ($row['date_fin']!=null){
	      $envoye = true;
	      $validlib = "<span>3</span><img src=\"../../img/admin/bullet_green.png\" alt=\"Validé\" title=\"Validé\" height=\"16\" width=\"16\" class=\"icone\" />";
		  $lien_voir = $row['repertoire'];
	    } else if ($row['no_envoi']!=null&&$row['repertoire']!=null&&$row['repertoire']!=""){
	      $envoye = false;
	      $validlib = "<span>1</span><img src=\"../../img/admin/bullet_yellow.png\" alt=\"Modifié, non validé\" title=\"Modifié, non validé\" height=\"16\" width=\"16\" class=\"icone\" />";
		   $lien_voir = $row['repertoire'];
	    } else {
	      $envoye = false;
	      $validlib = "<span>0</span><img src=\"../../img/admin/bullet_black.png\" alt=\"Non validé\" title=\"Non validé\" height=\"16\" width=\"16\" class=\"icone\" />";
		  $lien_voir = false;
	    }
		
		if($lien_voir!=false)
			echo "<td class=\"avecicone aveclib\"><span>1</span><a href=\"".$lien_voir."\" title=\"Voir\" target=\"_blank\"><img src=\"../../img/admin/icoad-voir.png\" alt=\"Voir\" title=\"Voir la lettre d'information\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
		else
			echo "<td></td>\n";
		
		echo "<td class=\"avecicone aveclib\">".$validlib."</td>\n";
			
	    	    
	    // Objet
		if(!$lien_voir)
			echo "<td><a title=\"".$row['objet']."\" href='modifajout.php?id=".$row['no']."'>".$row["objet"]."</a></td>\n";
		else
			echo "<td>".$row["objet"]."</td>\n";
	    
	    // Date
			//On affiche la date de début, et la date de fin de la lettre.
			$date_creation = datefr($row["date_creation"],false);
			$date_modification = datefr($row["date_modification"]);
			$date_debut = datefr($row["date_debut"]);
				$time_fin = strtotime($row["date_debut"]);
				$time_fin = $time_fin+10*24*60*60; //+10 jours
			$date_fin = date('d/m/Y', $time_fin);
	    echo "<td>du ".$date_debut." au ".$date_fin."</td>\n";
	    
	    // Nb evts / structs / vie
	    echo "<td>".$date_creation."</td>\n";
	    echo "<td>".$date_modification."</td>\n";
		if($row['date_fin']!=null)
			echo "<td>".datefr($row["date_fin"])."</td>\n";
		else
			echo "<td></td>\n";
	    
    		// echo "<td class=\"avecicone\"><a href='activ.php?action=".$action."&id=".$row[no]."'>".$actdesactlib."</a> </td>\n";
		if(!$lien_voir){
			echo "<td class=\"avecicone\"><a href='modifajout.php?id=".$row["no"]."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a> </td>\n";
			echo "<td class=\"avecicone\"><a href='javascript:return false;' id=\"".$row["no"]."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
		}
		else{
			if($envoye){
				echo "<td class=\"avecicone\"></td>\n";
				echo "<td class=\"avecicone\"></td>\n";
			}
			else{
				echo "<td class=\"avecicone\"><a href='envoi.php?id=".$row["no"]."'><img src=\"../../img/admin/icoad_envoi.png\" alt=\"Envoyer\" title=\"Envoyer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
				echo "<td class=\"avecicone\"></td>\n";
			}
		}
		echo "</tr>\n";
  	}
	// Aucune ligne ?
	if (!$des_lettres) echo "<tr><td colspan =\"6\">Aucune</td></tr>";
	echo "</tbody>\n";
	echo "</table>";
  } else {
  	echo "<p>Aucun élément</p>"; 
  }

?>
<form class="formA">
<fieldset>
 <legend>Légende</legend>
<div class="moitie">
  <img src="../../img/admin/bullet_black.png" alt="Non validé" height="16" width="16" class="icone" /> En cours<br/>
  <img src="../../img/admin/bullet_yellow.png" alt="Modifié, non validé" height="16" width="16" class="icone" /> Validée, non envoyée<br/>
  <img src="../../img/admin/bullet_green.png" alt="Validé" height="16" width="16" class="icone" /> Envoyée<br/>
</div>
</fieldset>
</form>