<?php
/*****************************************************
Gestion de l'affichage
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Filtre haut de page
if (isset($_POST['pa_non_expires']))
{
    $pa_non_expires = intval($_POST['pa_non_expires']);
    $_SESSION['pa_non_expires'] = $pa_non_expires;
}
elseif (isset($_SESSION['pa_non_expires']))
    $pa_non_expires = intval($_SESSION['pa_non_expires']);
else
    $pa_non_expires = 1;

// Date pour comparaison
$date_jour = explode("/", date('d/m/Y'));
$date_jour = $date_jour[2].$date_jour[1].$date_jour[0];

$query = "SELECT P.*, UNIX_TIMESTAMP(P.date_creation) AS date_creation_ts, P.no AS no_pa
	  FROM petiteannonce P, communautecommune_ville C, communautecommune B WHERE P.no_ville = C.no_ville AND C.no_communautecommune = B.no AND B.territoires_id = ".$territoire;
if ($pa_non_expires) $query .= " AND P.date_fin>=CURDATE()";
$query .= " ORDER BY P.date_fin DESC";

$res = $connexion->prepare($query);
$res->execute() or die ("Erreur ".__LINE__.".");
$nombre_res = $res->rowCount();

// Nombre d'elements actifs
$queryact = "SELECT * FROM `petiteannonce`
	    WHERE etat = 1
	    AND date_fin>=CURDATE()
	    AND no_ville <> 0
	    ORDER BY date_creation DESC";
$resact = $connexion->prepare($queryact);
$resact->execute() or die ("Erreur  ".__LINE__.".");
$nombre_res_act = $resact->rowCount();

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
    <select id="sel_territoire_annonce">
        <?php foreach ($rows_ter as $row ) { ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $territoire) ? 'selected="selected"' : '' ?>><?= $row['nom'] ?></option>
        <?php } ?>
    </select>
</div>
    <?php
      echo "<form name=\"ETri\" id=\"ETri\" action=\"\" method=\"post\" accept-charset=\"UTF-8\">
	  Afficher les petites annonces : 
	  <select name=\"pa_non_expires\">
		  <option value=\"1\"";
      if ($pa_non_expires==1) echo " selected";
      echo ">Non expirés</option>
		  <option value=\"0\"";
      if ($pa_non_expires==0) echo " selected";
      echo ">Tous</option>
	  </select>
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

    echo "<p class='nb_evts'>".$nombre_res." petites annonces ($nombre_res_act actives)</p>";

    echo '<form id="adform1" action="" method="post" class="formA avecajout">';
    echo '<a href="modifajout.php?ajout=1" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout">Ajout d\''.$cc_une.'</a></form>';

    echo "<table class=\"tablesorter\">\n";
  	echo "<thead>\n";
  	echo "<tr>\n";
  	echo "<th></th>\n";
  	echo "<th></th>\n";
  	echo "<th>Nom $cc_de</th>\n";
  	echo "<th>Ville</th>\n";
  	echo "<th>Date fin</th>\n";
  	echo "<th>Exp.</th>\n";
  	echo "<th>Création</th>\n";
  	echo "<th>Diffusion</th>\n";
  	echo "<th colspan=\"3\"></th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	  {
	    
	    // Récup de la ville
	    $sql_ville="SELECT nom_ville_maj, nom_ville_url, code_postal
			      FROM `villes`
			      WHERE id=:no_ville";
	    $resville = $connexion->prepare($sql_ville);
	    $resville->execute(array(':no_ville'=>$row["no_ville"])) or die ("Erreur ".__LINE__." : ".$sql_ville);
	    $rowville=$resville->fetchAll();
	    $nom_ville = $rowville[0][nom_ville_maj];
	    $nom_ville_url = $rowville[0][nom_ville_url];	    
	    $CP_ville = $rowville[0][code_postal];

	    $i++;
	    $date_expire = explode("/", datefr($row[date_fin]));
	    $date_expire = $date_expire[2].$date_expire[1].$date_expire[0];
	    if ($date_jour>$date_expire) $expire = true; else $expire = false;
	    // Actif ?
	    if ($row[etat]==1){
	      $actif = true;
	      $actdesactlib = "<img src=\"../../img/admin/icoad-checkbox-on.png\" alt=\"Cliquez pour désactiver\" title=\"Cliquez pour désactiver\" height=\"16\" width=\"16\" class=\"icone\" />";
	      $action = "desact";
	    } else {
	      $actif = false;
	      $actdesactlib = "<img src=\"../../img/admin/icoad-checkbox-off.png\" alt=\"Cliquez pour activer\" title=\"Cliquez pour activer\" height=\"16\" width=\"16\" class=\"icone\" />";
	      $action = "act";
	    }

	    // validé (éditorial) ?
	    if ($row['validation']==1){
	      $valide = true;
	      $validlib = "<span>1</span><img src=\"../../img/admin/bullet_green.png\" alt=\"Validé\" title=\"Validé\" height=\"16\" width=\"16\" class=\"icone\" />";
	    } elseif ($row['validation']==2){
	      $valide = false;
	      $validlib = "<span>2</span><img src=\"../../img/admin/bullet_yellow.png\" alt=\"Modifié, non validé\" title=\"Modifié, non validé\" height=\"16\" width=\"16\" class=\"icone\" />";
	    } else {
	      $valide = false;
	      $validlib = "<span>0</span><img src=\"../../img/admin/bullet_black.png\" alt=\"Non validé\" title=\"Non validé\" height=\"16\" width=\"16\" class=\"icone\" />";
	    }

	    // Lien voir
	    if ((!$expire)&&($actif)&&($row["no_ville"]))
	    {

		// Préparation du lien pour voir
		$titre_pour_lien = coupe_chaine($row["titre"],130,false);
		// Lien vers le détails. 
		$lien_voir = strtolower($root_site."petiteannonce.".$nom_ville_url.".".url_rewrite($titre_pour_lien).".".$row["no_ville"].".".$row["no_pa"].".html");

		echo "<tr class='actif'>\n";
		echo "<td class=\"avecicone aveclib\"><span>1</span><a href=\"".$lien_voir."\" title=\"Voir\" target=\"_blank\"><img src=\"../../img/admin/icoad-voir.png\" alt=\"Voir\" title=\"Voir\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
	    }
	    else
	    {
		echo "<tr class='nonactif'>\n";
		echo "<td></td>\n";
	    }

	    // Validé
	    echo "<td class=\"avecicone aveclib\">".$validlib."</td>\n";

	    // Nom
	    echo "<td><a title=\"\" href='modifajout.php?id=".$row['no_pa']."'>".$row[titre]."</a></td>\n";
	    // Ville
	    if ($nom_ville)
	      echo "<td>".$nom_ville." (".$CP_ville.")</td>\n";
	    else 
	      echo "<td></td>\n";
	      if ($date_expire != "00000000")
	      {
		echo "<td>".datefr($row["date_fin"])."</td>\n";
	      }
	      else 
		echo "<td></td>\n";
	    
	    // Expiré ?
	    if ($expire) 
	      echo "<td class=\"avecicone aveclib\"><span>1</span><img src=\"../../img/admin/icoad-expire.png\" alt=\"Expiré\" title=\"Expiré\" height=\"16\" width=\"16\" class=\"icone\" /></td>\n";
	    else
	      echo "<td></td>\n";
    
	    // echo "<td><span class='cache'>".$row["date_creation_ts"]."</span>".datefr($row["date_creation"])."</td>\n";
	    echo "<td>".datefr($row["date_creation"])."</td>\n";

	    // Nb de Like
//	    echo "<td>".$row["nb_aime"]."</td>\n";
            echo "<td>".$row["apparition_lettre"]."</td>\n";
      
    		echo "<td class=\"avecicone\"><a href='activ.php?action=".$action."&id=".$row[no_pa]."'>".$actdesactlib."</a> </td>\n";
    		echo "<td class=\"avecicone\"><a href='modifajout.php?id=".$row[no_pa]."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a> </td>\n";
    		echo "<td class=\"avecicone\"><a href='#' id=\"".$row[no_pa]."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
	  echo "</tr>\n";
  	}
    // Aucune ligne ?
  	if (!$i) echo "<tr><td colspan =\"7\">Aucune</td></tr>";
  	echo "</tbody>\n";
  	echo "</table>";
  } else {
  	echo "<p>Aucun élément</p>";
    echo '<form id="adform1" action="" method="post" class="formA avecajout">';
    echo '<a href="modifajout.php?ajout=1" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout">Ajout d\''.$cc_une.'</a></form>';
  }

?>
<form class="formA">
<fieldset>
 <legend>Légende</legend>
<div class="moitie">
  <img src="../../img/admin/bullet_black.png" alt="Non validé" height="16" width="16" class="icone" /> Non validé<br/>
  <img src="../../img/admin/bullet_yellow.png" alt="Modifié, non validé" height="16" width="16" class="icone" /> Modifié, non validé<br/>
  <img src="../../img/admin/bullet_green.png" alt="Validé" height="16" width="16" class="icone" /> Validé<br/>
  <img src="../../img/admin/bullet_blue.png" alt="Importé, non validé" height="16" width="16" class="icone" /> Importé, non validé<br/>
  
  <img src="../../img/admin/icoad-expire.png" alt="Expiré" title="Expiré" height="16" width="16" class="icone" /> : expiré
</div>
<div class="moitie">
  <span class="enrouge">En rouge : non actif (expiré ou désactivé)</span><br/>
  En noir : actif<br/>
  </div>
</fieldset>
</form>
