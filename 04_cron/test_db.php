<?php
header('Content-type: text/plain; charset=utf-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";


$requete = "SELECT * FROM newsletterbis";
$tab = execute_requete($requete);
$j = 0;
foreach ($tab as $k => $v) {
    $requete_test = "SELECT * FROM newsletter WHERE email = '".$v['email']."'";
    $tab_test = execute_requete($requete_test);
    if (!isset($tab_test[0]['no'])) {
        print_r($v['email']); 
        $j++;
    }
}
echo 'j='.$j;
?>
