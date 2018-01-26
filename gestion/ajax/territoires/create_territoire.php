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
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        // creation du territoire
        $requete = "INSERT INTO territoires (nom, ville, date_demarrage) VALUES (:n, :v, :d)";
        $param = array(":n" => $_POST['nom'], ":v" => $_POST['ville'], ":d" => date('Y-m-d')); 
        execute_requete($requete,$param);
        
        $select = "SELECT MAX(id) as max FROM territoires"; 
        $tabter = execute_requete($select);
        
        $monterritoire = $tabter[0]['max'];
        
//        $motdepasse = rand(100000, 999999);
        $motdepasse = '39efc9h5';
        $pass = md5($_POST['email'].$motdepasse.$cle_cryptage);
//        $pass = hash('SHA256', $motdepasse);
        // insertion de l'utilisateur
        $ins_user = "INSERT INTO utilisateur (email, no_ville, mot_de_passe, code_reinit_mot_de_passe, id_connexion, droits, etat, newsletter, code_desinscription_nl, 
            date_inscription, no_contact) VALUES (:e, :v, :pass, :c, :connex, 'A', 1, 1, :c, :d, 0)";
        $param_user = array(":e" => $_POST['email'], ":v" => $_POST['ville'], ":pass" => $pass, ":c" => id_aleatoire(), ":connex" => '', ":d" => date('Y-m-d H:i:s'));
        execute_requete($ins_user,$param_user);
        
        // récupération du numéro d'utilisateur
        $selectuser = "SELECT MAX(no) as max FROM utilisateur"; 
        $tabuser = execute_requete($selectuser);
        
        // insertion des droits 
        $requete_droits = "INSERT INTO  droit_utilisateur (no_utilisateur, no_droit) VALUES (:u, 1)";
        $param_droits = array(":u" => $tabuser[0]['max']); 
        execute_requete($requete_droits,$param_droits);
        
        // insertion communautes de communes
        $requete_com = "INSERT INTO communautecommune (libelle, no_ville, territoires_id) VALUES (:l, :v, :t)";
        $param_com = array(":l" => $_POST['pays'], ":v" => $_POST['ville'], ":t" => $monterritoire); 
        execute_requete($requete_com,$param_com);
        
        $select_com = "SELECT MAX(no) as max FROM communautecommune"; 
        $tabcom = execute_requete($select_com);
        $comcom = $tabcom[0]['max']; 
        
        $ins_com_ville = "INSERT INTO communautecommune_ville (no_communautecommune, no_ville) VALUES (:c, :v)";
        $param_com_ville = array(":c" => $comcom, ":v" => $_POST['ville']);
        execute_requete($ins_com_ville,$param_com_ville);
    }
    else {
        $return_code = '30';
    }
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
