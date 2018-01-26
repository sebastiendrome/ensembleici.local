<?php
/*****************************************************
Association des evènements à un tag
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
	
if (!$id_tag)
{
  if ($_POST['id'])
    $id_tag = intval($_POST['id']);
}

if ($id_tag)
{
    // Tags
    $sql_evt="SELECT *
		FROM `evenement_tag` T, `evenement` E
		WHERE T.no_evenement = E.no
		AND no_tag=:no";
    $res_evt= $connexion->prepare($sql_evt);
    $res_evt->execute(array(':no'=>$id_tag)) or die ("Erreur 23 : ".$sql_evt);
    $tab_evt=$res_evt->fetchAll();

  // Affiche les tags
  if (count($tab_evt))
  {
    echo "<ul>";
    foreach ($tab_evt as $evt) {
	$expire = false;
	$actif = false;
	$no_evt = $evt['no'];

	echo "<li>";
	
	$date_jour = explode("/", date('d/m/Y'));
	$date_jour = $date_jour[2].$date_jour[1].$date_jour[0];
	$date_expire = explode("/", datefr($evt["date_fin"]));
	$date_expire = $date_expire[2].$date_expire[1].$date_expire[0];
	if ($date_jour>$date_expire) $expire = true; else $expire = false;
	if ($expire) 
	  echo "<img src=\"../../img/admin/icoad-expire.png\" alt=\"Expiré\" title=\"Expiré\" height=\"16\" width=\"16\" class=\"icone\" />\n";

	if ($evt["etat"]==1)
	  $actif = true;

	echo "&nbsp;&nbsp;<a href='../events/modifajout.php?id=".$no_evt."'>";
	if (($expire)OR(!$actif))
	  echo "<span class=\"enrouge\">".$evt["titre"]."</span>";
	else
	  echo $evt["titre"];
	echo "</a>";

	echo "&nbsp;&nbsp;<a href='../events/modifajout.php?id=".$no_evt."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a>\n";
	echo "&nbsp;<a id=\"".$evt['no']."\" href=\"#\" class=\"deleteevt\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer cette association\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

	echo "</li>";
    }
    echo "</ul>";
  }
  else
  {
    echo "Aucun";
  }
}
?>
