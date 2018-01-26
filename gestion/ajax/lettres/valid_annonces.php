<?php
session_start();
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
$no = $_POST['no'];

if (isset($_SESSION["utilisateur"]["territoire"])) {
    $requete_info_annonces = "SELECT * FROM lettreinfo_petiteannonce WHERE no_lettre=:no";
    $res_info_annonces = $connexion->prepare($requete_info_annonces);
    $res_info_annonces->execute(array(":no"=>$no));
    $tab_info_annonces = $res_info_annonces->fetch();
    
    if (isset($tab_info_annonces['liste_petiteannonce_valide'])) {
        // mise à jour
        $requete = "UPDATE lettreinfo_petiteannonce SET liste_petiteannonce_complete = :e, liste_petiteannonce = :e, liste_petiteannonce_valide = :e, date_modification = CURRENT_TIMESTAMP, etape_valide = 1 WHERE no_lettre = :no";
        $res_requete = $connexion->prepare($requete);
        $res_requete->execute(array(":no" => $no, ":e" => $_POST['liste']));
    }
    else {
        // insertion
        $requete = "INSERT INTO  lettreinfo_petiteannonce (no_lettre, liste_petiteannonce_complete, liste_petiteannonce, liste_petiteannonce_valide, date_modification, etape_valide) 
            VALUES(:no, :e, :e, :e, CURRENT_TIMESTAMP, 1)";
        $res_requete = $connexion->prepare($requete);
        $res_requete->execute(array(":no" => $no, ":e" => $_POST['liste']));
    }
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
