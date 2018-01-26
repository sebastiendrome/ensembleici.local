<?php
/*****************************************************
Affichage les liaisons d'un type source (évènement, structure,...)
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
	
if (!$id_source)
{
  if ($_POST['id'])
    $id_source = intval($_POST['id']);
}

if (($id_source)&&($type_source))
{
    // Liaisons actives
    // Champs A
    $sql_liaison_A="SELECT no, type_B AS type_lie, no_B AS no_lie
		FROM `liaisons` E
		WHERE
		  type_A=:type
		AND
		  no_A=:no";
    // Champs B
    $sql_liaison_B="SELECT no, type_A AS type_lie, no_A AS no_lie
		FROM `liaisons` E
		WHERE
		  type_B=:type
		    AND
		  no_B=:no";

    $sql_liaison = "($sql_liaison_A) UNION ($sql_liaison_B) ORDER BY type_lie";
    $res_liaison = $connexion->prepare($sql_liaison);
    $res_liaison->execute(array(':type'=>$type_source,':no'=>$id_source)) or die ("Erreur ".__LINE__." : ".$sql_liaison);
    $tab_liaison=$res_liaison->fetchAll();

  // Affiche les tags
  if (count($tab_liaison))
  {
    foreach ($tab_liaison as $liaison) {

      if ($type_actif!=$liaison["type_lie"])
      {
	// Début d'un nouveau type
	$type_actif = $liaison["type_lie"];
	switch ($type_actif)
	{
	   case "evenement" :
	      $type_actif_affiche = "Evènement(s)";
	      $url_lien = "/events/";
	   break;
	   case "structure" :
	      $type_actif_affiche = "Structure(s)";
	      $url_lien = "/structures/";
	   break;
	}
	// ce n'est pas le 1er
	if ($i) echo "</li></ul></div>";
	echo "<div class=\"clear\">
	<label>".$type_actif_affiche." :<sup>(N°)</sup></label>
	<div class=\"chps_non_input avecliste\">
	<ul>\n";
      }

      $no_liaison = $liaison['no_lie'];
      echo "<li>";

      // Récupère le nom de la liaison
      $sql_nl="SELECT * FROM ".$liaison["type_lie"]."
		WHERE no=:no_lie";
      $res_nl = $connexion->prepare($sql_nl);
      $res_nl->execute(array(':no_lie'=>$no_liaison)) or die ("Erreur ".__LINE__." : ".$sql_nl);
      $liaison_nom=$res_nl->fetch(PDO::FETCH_ASSOC);
      
      // Champs Titre ou nom selon les tables
      if (isset($liaison_nom["titre"]))
	$liaison_nom_aff = $liaison_nom["titre"];
      else
	$liaison_nom_aff = $liaison_nom["nom"];
           
      // Affiche le nom de la liaison
      if ($liaison_nom_aff)
	echo "<a href=\"..".$url_lien."modifajout.php?id=".$no_liaison."\" target=\"_blank\">".$liaison_nom_aff."</a> <sup>(".$no_liaison.")</sup>";

      // Suppression
      echo "&nbsp;&nbsp;<a id=\"".$liaison['no']."\" href=\"#\" class=\"deleteliaison\"><img src=\"../../img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"12\" width=\"12\" class=\"icone\" /></a>";

      echo "</li>";
      $i++;
    }
    // Ferme le dernier type
    if ($i) echo "</ul></div></div>";

  }
}
?>
