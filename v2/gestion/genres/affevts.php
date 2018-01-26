<?php
/*****************************************************
Association des evènements liés à un genre
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

if (!$id_genre)
{
  if ($_POST['id'])
    $id_genre = intval($_POST['id']);
}

if ($id_genre)
{
    // Tags
    $sql_evt="SELECT *
		FROM `evenement`
		WHERE no_genre=:no";
    $res_evt= $connexion->prepare($sql_evt);
    $res_evt->execute(array(':no'=>$id_genre)) or die ("Erreur ".__LINE__." : ".$sql_evt);
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
