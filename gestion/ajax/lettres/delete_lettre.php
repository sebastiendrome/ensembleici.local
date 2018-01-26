<?php
//1. Initialisation de la session
include "../../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../../01_include/_init_var.php";

$return_code = '0';
$tab = array();

$requete_lettre = "SELECT * FROM lettreinfo WHERE no = :no";
$tab_fiche = execute_requete($requete_lettre,array(":no"=>$_POST["ref"]));

if (!empty($tab_fiche)) {
    // suppression en base
    
    // suppression du répertoire de la lettre d'infos
    
    // suppression pdf agenda et annonces
    
//    $requete_delete = "DELETE FROM $mapage WHERE no = :no";
//    execute_requete($requete_delete,array(":no"=>$_POST["id"]));
}
else {
    $return_code = '1';
}

//$tab['code'] = $return_code; 
//$reponse = json_encode($tab); 
//echo $reponse; 
?>
