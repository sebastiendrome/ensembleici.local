<?php
//1. Initialisation de la session
include "01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "01_include/_fonctions.php";
$requete = "SELECT id, nom_ville_maj FROM villes WHERE code_postal = 26410";
$tab = execute_requete($requete);

foreach ($tab as $k => $v) {
//    $requete = "INSERT INTO communautecommune_ville (no_communautecommune, no_ville) VALUES (6, ".$v['id'].")";
//    $tab = execute_requete($requete);
}
?>
