<?php
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

if (isset($_SESSION['utilisateur']['territoire'])) {
    $territoire = $_SESSION['utilisateur']['territoire'];
}
else {
    $territoire = 1;
}
$requete_diaporama = "SELECT nom FROM diaporamas WHERE territoires_id = :t AND valide = 1 ORDER BY ordre ASC";
$tab_diaporama = execute_requete($requete_diaporama,array(":t" => $territoire));
$tab = array('');
foreach ($tab_diaporama as $k => $v) {
    $tab[$k]['nom'] = $v['nom'];
}
$reponse = json_encode($tab);
echo $reponse;

?>
