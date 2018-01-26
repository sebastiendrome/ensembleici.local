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

if (isset($_SESSION["utilisateur"]["territoire"])) {
    
    $sel_ville = "SELECT id FROM villes WHERE nom_ville_maj = :n";
    $param_ville = array(":n" => strtoupper($_POST['ville']));
    $tab_ville = execute_requete($sel_ville,$param_ville);
    
    if (isset($tab_ville[0]['id'])) {
        $requete = "INSERT INTO communautecommune (libelle, no_ville, territoires_id) VALUES (:l, :v, :t)";
        $param = array(":l" => $_POST['communaute'], ":v" => $tab_ville[0]['id'], ":t" => $_SESSION["utilisateur"]["territoire"]);
        execute_requete($requete,$param);
        
        $select_com = "SELECT MAX(no) as max FROM communautecommune"; 
        $tabcom = execute_requete($select_com);
        $comcom = $tabcom[0]['max']; 
        
        $requete2 = "INSERT INTO communautecommune_ville (no_communautecommune, no_ville) VALUES (:c, :v)";
        $param2 = array(":c" => $comcom, ":v" => $tab_ville[0]['id']);
        execute_requete($requete2,$param2);
    }
    else {
        $return_code = '20';
    }
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
