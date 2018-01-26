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
    $requete_select = "SELECT * FROM lettreinfo WHERE no = :no";
    $res_requete_select = $connexion->prepare($requete_select);
    $res_requete_select->execute(array(":no" => $no));
    $tab_select = $res_requete_select->fetchAll();
    
    if (sizeof($tab_select) > 0) {
        // mise à jour
        $valid = 0;
        $requete = "UPDATE lettreinfo SET "; 
        if ($_POST['agenda'] != '') {
            $requete .= "pdf_agenda = '".$_POST['agenda']."'";
            $valid = 1;
        }
        if ($_POST['annonces'] != '') {
            if ($valid == 1) {
                $requete .= ',';
            }
            $requete .= "pdf_annonces = '".$_POST['annonces']."'";
            $valid = 1;
        }
        $requete .= " WHERE no = ".$no;
        if ($valid == 1) {
            $res_requete = $connexion->prepare($requete);
            $res_requete->execute();
        }
        else {
            $return_code = '30';
        }
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
