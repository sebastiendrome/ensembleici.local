<?php
/*****************************************************
Gestion des utilisateurs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

//On récupère le nombre d'entrée total
$query_count = "SELECT COUNT(N.no) AS nb FROM newsletter N, communautecommune_ville T, communautecommune C WHERE N.no_ville = T.no_ville AND T.no_communautecommune = C.no AND C.territoires_id = ".$territoire;
$res_count = $connexion->prepare($query_count);
$res_count->execute() or die ("Erreur ".__LINE__." : ".$query_count);
$nombre_res = $res_count->fetchAll();
$nombre_res = $nombre_res[0]["nb"];
$nb_page = ceil($nombre_res/$limite_par_page);
//On récupère le numéro de page
if($_GET["p"]!=null)
	$page = (int)$_GET["p"];
else
	$page = 1;
if($page>$nb_page)
	$page = $nb_page;
else if($page<1)
	$page = 1;
//On calcul le début de la limite
$limite_deb = $limite_par_page*($page-1);
if ($limite_deb < 0) {
    $limite_deb = 0;
}
$limite_fin = ($limite_deb+$limite_par_page);
if($limite_fin>$nombre_res)
	$limite_fin = $nombre_res;

$query = "SELECT newsletter.*,communautecommune.libelle,communautecommune.no_ville FROM `newsletter` JOIN communautecommune ON communautecommune.no_ville=newsletter.no_ville AND communautecommune.territoires_id = ".$territoire." ORDER BY email LIMIT ".$limite_deb.",".$limite_par_page;
$res = $connexion->prepare($query);
$res->execute() or die ("Erreur ".__LINE__." : ".$query);

$sql_ter = "SELECT * FROM territoires";
$res_ter = $connexion->prepare($sql_ter);
$res_ter->execute() or die ("Erreur ".__LINE__." : ".$sql_ter);
$rows_ter=$res_ter->fetchAll(PDO::FETCH_ASSOC);

echo "<div class=\"filtres\">";
      ?>
<div style="text-align: left; margin-left: 10px;">
    Territoire : 
    <select id="sel_territoire_newsletter">
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
  if($nombre_res>0)
  {
      

    echo "<p class='nb_evts'>".($limite_deb+1)." à ".$limite_fin." sur <b>".$nombre_res."</b> $cc_min(s)</p>";

    echo '<form id="adform1" action="" method="post" class="formA avecajout">';
    echo '<a href="" title="Ajout d\''.$cc_une.'" class="boutonbleu ico-ajout" id="ajouter_mail">Ajout d\''.$cc_une.'</a><a href="../lettreinfo/admin.php" title="Retour à la liste des lettres d\'information" class="boutonbleu ico-fleche">Retour à la liste des lettres d\'information</a></form>';

    echo "<table class=\"tablesorter\">\n";
  	echo "<thead>\n";
  	echo "<tr>\n";
  	echo "<th>Email $cc_de</th>\n";
  	echo "<th>Communauté de commune</th>\n";
  	echo "<th colspan=\"3\"></th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
	echo "<tfoot>\n";
  	echo "<tr>\n";
  	echo "<td colspan=\"5\"><input type=\"text\" value=\"".$page."\" onchange=\"document.location='admin.php?p='+this.value;\" />&nbsp;/&nbsp;".$nb_page."</td>";
  	echo "</tr>\n";
  	echo "</tfoot>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	{
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
	    
	    // Nom
	    echo "<td><a title=\"".$row['email']."\" href='' class='ecrire_mail' id='ecrire_".$row["no"]."'>".$row["email"]."</a></td>\n";
	    echo "<td><span style=\"cursor:pointer;\" onclick=\"prepare_modif_ville(this,".$row["no_ville"].",".$row["no"].")\">".$row['libelle']."</span></td>\n";
	    
    		echo "<td class=\"avecicone\"><a href='activ.php?action=".$action."&id=".$row["no"]."'>".$actdesactlib."</a> </td>\n";
	    echo "<td class=\"avecicone\" onclick=\"click_btn_modif(this,".$row["no"].")\" style=\"cursor:pointer;\"><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></td>\n";
	    echo "<td class=\"avecicone\"><a href='#' id=\"".$row["no"]."\" class=\"delete\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" /></a></td>\n";
	  echo "</tr>\n";
  	}
	echo "</tbody>\n";
	echo "</table>";
  } else {
  	echo "<p>Aucun élément</p>"; 
  }

?>