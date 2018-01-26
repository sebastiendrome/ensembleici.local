<?php
/*****************************************************
Gestion des contenus des pages
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$query = "SELECT * 
          FROM `contenu_blocs`
	  ORDER BY no ASC"; 

$res = $connexion->prepare($query);
$res->execute() or die ("Erreur 14.");
$nombre_res = $res->rowCount();
		  
    if($nombre_res>0)
    {
	echo "<table id=\"tableau\" class=\"tablesorter\">\n";
  	echo "<thead>\n";
  	echo "<tr>\n";
  	echo "<th>Nom $cc_de</th>\n";
  	echo "<th colspan>Actions</th>\n";
  	echo "</tr>\n";
  	echo "</thead>\n";
  	echo "<tbody>\n";
	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row )
	{
	  echo "<tr>\n";
	  echo "<td><strong><a href='modif.php?id=".$row["no"]."'>".$row["nom_bloc"]."</a></strong></td>\n";
    		echo "<td class=\"avecicone\"><a href='modifajout.php?id=".$row["no"]."'><img src=\"../../img/admin/icoad-modif.png\" alt=\"Modifier\" title=\"Modifier\" height=\"16\" width=\"16\" class=\"icone\" /></a> </td>\n";
	  echo "</tr>\n";
	}
  	echo "</tbody>\n";
  	echo "</table>\n";
	  
  } else {
  	echo "<p>Aucun élément</p>"; 
  }

// mysql_close($linkbase);
?>