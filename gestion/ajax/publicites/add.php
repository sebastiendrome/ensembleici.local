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
if (isset($_SESSION["utilisateur"]["territoire"])) {
    if (($_POST['site'] != '') && !filter_var($_POST['site'], FILTER_VALIDATE_URL)) {
        $return_code = '11';
    }  
    else {
        if (checkdate(substr($_POST['debut'], 3, 2), substr($_POST['debut'], 0, 2), substr($_POST['debut'], 6, 4)) && 
                checkdate(substr($_POST['fin'], 3, 2), substr($_POST['fin'], 0, 2), substr($_POST['fin'], 6, 4))) {
            $date_debut = substr($_POST['debut'], 6, 4).'-'.substr($_POST['debut'], 3, 2).'-'.substr($_POST['debut'], 0, 2);
            $date_fin = substr($_POST['fin'], 6, 4).'-'.substr($_POST['fin'], 3, 2).'-'.substr($_POST['fin'], 0, 2);
            $image = '02_medias/07_pubs/'.$_POST['fichier'];
            $requete_publicite= "INSERT INTO publicites (titre, validite_du, validite_au, url_image, site, type, territoires_id, page, vente) 
                VALUES (:titre, :debut, :fin, :image, :site, :type, :t, :p, :v)";
            $param_insert = array(":titre" => $_POST['titre'], ":debut" => $date_debut, ":fin" => $date_fin, ":image" => $image, 
                ":site" => $_POST['site'], ":type" => $_POST['type'], ":t" => $_SESSION["utilisateur"]["territoire"], ":p" => $_POST['page'], 
                ":v" => $_POST['vente']);
            execute_requete($requete_publicite,$param_insert);
        }
        else {
            $return_code = '12';
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
