<?php
/*****************************************************
Affichage les liaisons d'un type source (évènement, structure,...)
******************************************************/
session_name("EspacePerso");
session_start();
//require_once ('01_include/connexion_verif.php');
require_once ('01_include/_connect.php');
require_once ('01_include/fonctions.php');

if (!$id_source)
{
  if ($_POST['id'])
    $id_source = intval($_POST['id']);
}

if (!isset($depuis_formulaire))
    $depuis_formulaire = intval($_POST['depuis_formulaire']);

if (!$type_source)
    $type_source = $_POST['type'];

if (preg_match("/^[A-Za-z\\-\\., \']+$/",$type_source))
    $type_source_ok = strtolower($type_source);

if (($id_source)&&($type_source_ok))
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
    // echo $sql_liaison;
    $res_liaison = $connexion->prepare($sql_liaison);
    $res_liaison->execute(array(':type'=>$type_source_ok,':no'=>$id_source)) or die ("Erreur ".__LINE__." : ".$sql_liaison);
    $tab_liaison=$res_liaison->fetchAll();

  // Affiche les tags
  if (count($tab_liaison))
  {

	if (count($tab_liaison)>1) $s = "s";

	if ($depuis_formulaire)
	{
	  echo "<table class=\"tablo-styleA\">";
	  echo "\n<thead>";
	  echo "<tr><td>Nom</td><td>Type</td><td></td></tr>";
	  echo "</thead>";	  
	}
	else
	{
	  echo "<div class=\"blocD\">
		  <h3>Liaisons</h3>
		  <div class=\"contenu\">
		  <table class=\"tablo-styleA\">";		
	}
	echo "<tbody>";

	foreach ($tab_liaison as $liaison)
	{

	    echo "<tr>\n";

	    $type_actif = $liaison["type_lie"];
	    $no_liaison = $liaison['no_lie'];
	    $desactive = false;
	    $etat = "";

	    switch ($type_actif)
	    {
	       case "evenement" :
		  $type_actif_affiche = "Evènement";
	       break;
	       case "structure" :
		  $type_actif_affiche = "Structure";
	       break;
	       case "petiteannonce" :
		  $type_actif_affiche = "Petite annonce";
	       break;
	    }

		// Récupère le nom de la liaison
		$sql_nl="SELECT * FROM ".$type_actif."
			  WHERE no=:no_lie";
		$res_nl = $connexion->prepare($sql_nl);
		$res_nl->execute(array(':no_lie'=>$no_liaison)) or die ("Erreur ".__LINE__." : ".$sql_nl);
		$liaison_nom=$res_nl->fetch(PDO::FETCH_ASSOC);
		$id_ville = $liaison_nom["no_ville"];
		
		if (!$liaison_nom["etat"])
		{
		  $desactive = true;
		  $etat = " (désactivé)";
		}
		// Champs Titre ou nom selon les tables
		if (isset($liaison_nom["titre"]))
		  $liaison_nom_aff = $liaison_nom["titre"].$etat;
		else
		  $liaison_nom_aff = $liaison_nom["nom"].$etat;

		$sql_ville="SELECT * FROM villes
				WHERE id = :idville";
				
		$res_ville = $connexion->prepare($sql_ville);
		$res_ville->execute(array(':idville'=>$id_ville));
		$tab_ville = $res_ville->fetch(PDO::FETCH_ASSOC);
		$titre_ville = $tab_ville["nom_ville_maj"];
		$cp_ville = $tab_ville["code_postal"];		
		$titre_ville_url = $tab_ville["nom_ville_url"];
		
	    // Affiche le nom de la liaison
	    if ($desactive)
	    {
	      if ($liaison_nom_aff)
		echo "<td>$liaison_nom_aff</td>";
	      else
		echo "<td></td>";	      
	    }
	    else
	    {
	      $lien = $type_actif.".".$titre_ville_url.".".url_rewrite($liaison_nom_aff).".".$id_ville.".".$no_liaison.".html?apercu=1";
	      if ($liaison_nom_aff)
		echo "<td><a href=\"".$lien."\" target=\"_blank\">$liaison_nom_aff</a></td>";
	      else
		echo "<td><a href=\"".$lien."\" target=\"_blank\">Lien</a></td>";
	    }
      
	    echo "<td>$type_actif_affiche</td>";
	    
	    echo "<td class='agauche'>";

	    if (!$desactive)
	    {
	      echo "<a href=\"".$lien."\" target=\"_blank\">";
	      if ($depuis_formulaire)
		echo "<img src=\"img/admin/icoad-voir.png\" alt=\"voir\" title=\"Ouvrir dans un nouvel onglet\" height=\"16\" width=\"16\" class=\"icone\" />";
	      else
		echo "Voir";
		
	      echo "</a>";
	    }
	    // Suppression
	    if ($depuis_formulaire)
	    {
	      echo "<a id=\"".$liaison['no']."\" href=\"#\" class=\"supprimliaison\">
		<img src=\"img/admin/icoad-supp.png\" alt=\"Supprimer\" title=\"Supprimer\" height=\"16\" width=\"16\" class=\"icone\" />
		</a>";
	    }
	    echo "</td>";

	    // echo "</li>";
	    echo "</tr>\n";


	}
	echo "</tbody>";
	echo "</table>";

	if (!$depuis_formulaire)
	  echo "</div></div>";
	  
  }
}
?>