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
    $requete_liste = "SELECT no_ville FROM communautecommune_ville";
    $tab_liste = execute_requete($requete_liste,array());
    $notin = '('; $prems = 1;
    foreach ($tab_liste as $k => $v) {
        if ($prems) {
            $prems = 0;
            $notin .= $v['no_ville'];
        }
        else {
            $notin .= ','.$v['no_ville'];
        }
    }
    $notin .= ')';
    
    $param = array();
    if (($_POST['code'] == '') && ($_POST['ville'] == '')) {
        $return_code = '20';
    }
    else {
        $selvilles = "SELECT id, nom_ville_maj, code_postal FROM villes WHERE";
        if ($_POST['code'] != '') {
            // recherche par code postal
            $selvilles .= " code_postal = :c";
            $param[':c'] = $_POST['code'];
        }
        if ($_POST['ville'] != '') {
            // recherche par code postal
            if ($_POST['code'] != '') {
                $selvilles .= ' AND';
            }
            $selvilles .= " nom_ville LIKE '%".$_POST['ville']."%'";
        }
        
        $selvilles .= " AND id NOT IN ".$notin." ORDER BY nom_ville_maj";
        
        $tabvilles = execute_requete($selvilles,$param);
        
        if (sizeof($tabvilles) > 0) {
            $chaine = '';
            foreach ($tabvilles as $k => $v) {
                $chaine .= "<div style='float: left; width: 33%; height: 30px;'>";
                $chaine .= "<input type='checkbox' name='add_territoire_ville' data-ref='".$v['id']."'  />";
                $chaine .= "<span style='margin-left: 15px;'>".$v['nom_ville_maj']." (".$v['code_postal'].")</span>";
                $chaine .= "</div>";
            }
            $chaine .= '<div style="clear:both;">';
            $tab['chaine'] = $chaine;
        }
        else {
            $return_code = '30';
        }
    }
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
