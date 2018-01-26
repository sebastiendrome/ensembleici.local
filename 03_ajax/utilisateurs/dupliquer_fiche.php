<?php
//1. Initialisation de la session
include "../../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../../01_include/_init_var.php";

$return_code = '0';
if(est_connecte()){
//    $page = $_POST['page'];
    $id = $_POST['ref'];
    $table = 'evenement';
    $table_contact = 'evenement_contact';
    $table_tag = 'evenement_tag';

//    switch ($page) {
//        case 'editorial' : $table = 'editorial'; break;
//        case 'agenda' : $table = 'evenement'; break;
//        case 'evenement' : $table = 'evenement'; break;
//        case 'structure' : $table = 'structure'; break;
//        case 'petiteannonce' : $table = 'petiteannonce'; break;
//        case 'petite-annonce' : $table = 'petiteannonce';  break;
//        default: $table = 'forum'; break;
//    }
    
    $requete = "SELECT * FROM $table WHERE no = :no";
    $tab_fiche = execute_requete($requete,array(":no" => $id));
    if (isset($tab_fiche[0])) {
        $elem = $tab_fiche[0];
        unset($elem['no']); 
        $prems = 1;
        $values = ''; $datas = ''; $params = array();
        foreach ($elem as $k => $v) {
            if ($prems) {
                $prems = 0;
            }
            else {
                $values .= ','; $datas .= ',';
            }
            switch ($k) {
                case 'etat': $param_v = 0; break;
                case 'validation': $param_v = 0; break;
                case 'apparition_lettre': $param_v = 0; break;
                case 'nb_aime': $param_v = 0; break;
                case 'no_utilisateur_creation': $param_v = $_SESSION["utilisateur"]["no"]; break;
                case 'date_creation': $param_v = date('Y-m-d'); break;
                case 'date_modification': $param_v = date('Y-m-d H:i:s'); break;
                default: $param_v = $v; break;
            }
            $values .= $k; 
            $datas .= ':'.$k; 
            $params[':'.$k] = $param_v;
        }
//        $values .= ')'; $datas .= ')';
        
        $sql_duplicate = "INSERT INTO $table ($values) VALUES ($datas)";
        $new_no = execute_requete($sql_duplicate,$params);
        
        
        // recherche des infos contact
        $sql_contact = "SELECT * FROM $table_contact WHERE no_evenement = :no";
        $tab_contacts = execute_requete($sql_contact,array(":no" => $id));
        foreach ($tab_contacts as $k1 => $v1) {
            $insert_contact = "INSERT INTO $table_contact (no_evenement, no_contact, no_role, traite) VALUES (:no_evenement, :no_contact, :no_role, :traite)";
            execute_requete($insert_contact,array(":no_evenement" => $new_no, ":no_contact" => $v1['no_contact'], ":no_role" => $v1['no_role'], ":traite" => $v1['traite']));
        }
        
        // recherche des infos tags
        $sql_tags = "SELECT * FROM $table_tag WHERE no_evenement = :no";
        $tab_tags = execute_requete($sql_tags,array(":no" => $id));
        foreach ($tab_tags as $k2 => $v2) {
            $insert_tag = "INSERT INTO $table_tag (no_evenement, no_tag) VALUES (:no_evenement, :no_tag)";
            execute_requete($insert_tag,array(":no_evenement" => $new_no, ":no_tag" => $v2['no_tag']));
        }
    }
    else {
        $return_code = '20';
    }
    
}
else {
    $return_code = '10';
}

$tab = array(); 
$tab['code'] = $return_code; 
$tab['no'] = $new_no;

$reponse = json_encode($tab); 
echo $reponse; 
?>
