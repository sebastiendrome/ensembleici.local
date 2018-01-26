<?php
//1. Initialisation de la session
include "../../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../../01_include/_init_var.php";

$tab = array();

$requete_contact = "SELECT no, nom FROM contact WHERE nom  LIKE '%".$_POST['name']."%' LIMIT 0,10";
$tab_contacts = execute_requete($requete_contact,array());
$tabnom = array();

if (sizeof($tab_contacts) > 0) {
    foreach ($tab_contacts as $k => $v) {
        if (!in_array($v['nom'], $tabnom)) {
            $tab[$k]['nom'] = $v['nom'];
            $tab[$k]['no'] = $v['no'];
            $tabnom[] = $v['nom'];
        }
    }
}

$reponse = json_encode($tab); 
echo $reponse; 
?>
