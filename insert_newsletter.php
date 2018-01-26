<?php
//1. Initialisation de la session
include "01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "01_include/_fonctions.php";
$requete = "SELECT * FROM Feuille1";
$tab = execute_requete($requete);

$now = date('Y-m-d H:i:s');
$nb = 0;
foreach ($tab as $k => $v) {
    $requete = "INSERT INTO newsletter (email, no_ville, etat, code_desinscription_nl, date_inscription) VALUES ('".$v['email']."', 9467, 1, '', '".$now."')";
    $tab = execute_requete($requete);
    $nb++;
}
echo $nb." adersses insérées";
?>
