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
    $requete_select = "SELECT * FROM lettreinfo_edito WHERE no_lettre = :no";
    $res_requete_select = $connexion->prepare($requete_select);
    $res_requete_select->execute(array(":no" => $no));
    $tab_select = $res_requete_select->fetchAll();
    
    if (sizeof($tab_select) > 0) {
        // mise à jour
        $requete = "UPDATE lettreinfo_edito SET corps = :corps, mention_permanente = :mention, avant = :avant, etape_valide = 1, territoires_id = :t WHERE no_lettre = :no";
    }
    else {
        // insertion
        $requete = "INSERT INTO lettreinfo_edito (no_lettre, corps, mention_permanente, avant, etape_valide, territoires_id) 
            VALUES(:no, :corps, :mention, :avant, 1, :t)";
    }
    $res_requete = $connexion->prepare($requete);
    $res_requete->execute(array(":no" => $no, ":corps" => $_POST['edito'], ":mention" => $_POST['mention'], ":avant" => 0, ":t" => $_SESSION["utilisateur"]["territoire"]));
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
