<?php
/*****************************************************
Recherche un ID en fonction de mots clés
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$q = strtolower($_GET['term']);
if(preg_match("/[^a-zA-Z ]/",$_GET['type_A']))
    $type_A = strtolower($_GET['type_A']);
if(preg_match("/[^a-zA-Z ]/",$_GET['type_B']))
    $type_B = strtolower($_GET['type_B']);
$no_A = intval($_GET['no_A']);

if ($q && $type_A && $no_A && $type_B) {
   // Récupérer le nom du champ contenant le titre
   switch ($type_B)
   {
      case "evenement" :
	 $champs_nom = "titre";
      break;
      case "structure" :
	 $champs_nom = "nom";
      break;
   }
   $tab = array();
   
   // Pour éviter une liaison sur lui-même
   if ($type_A == $type_B)
      $ajout_cond = " AND no <> :no_A ";
   
   // Lance la recherche
   $sql_re="SELECT $champs_nom AS label, no AS value
	       FROM `$type_B`
	       WHERE $champs_nom LIKE :mot_recherche
	       $ajout_cond
	       ORDER BY $champs_nom ASC";

   $recherche_re = $connexion->prepare($sql_re);
   $recherche_re->bindValue(':mot_recherche', "%".$q."%", PDO::PARAM_STR);
   if ($type_A == $type_B)
      $recherche_re->bindValue(':no_A', $no_A, PDO::PARAM_INT);
   $recherche_re->execute() or die ("Erreur ".__LINE__." : ".$sql_re);
   $tab = $recherche_re->fetchAll();
   
   // Retourne le nom avec l'id entre () pour info
   for($i=0;$i<sizeof($tab);$i++) {
       $tab[$i]["label"] = $tab[$i]["label"]." (".$tab[$i]["value"].")";
   }

   echo json_encode($tab);
}
?>