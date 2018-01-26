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

$requete_contact = "SELECT no_contactType, valeur FROM contact_contactType WHERE no_contactType IN (1,2) AND no_contact = ".$_POST['ref'];
$tab_infos = execute_requete($requete_contact,array());

if (sizeof($tab_infos) > 0) {
    foreach ($tab_infos as $k => $v) {
        $tab[$k]['type'] = $v['no_contactType'];
        $tab[$k]['valeur'] = $v['valeur'];
    }
}

$reponse = json_encode($tab); 
echo $reponse; 
?>
