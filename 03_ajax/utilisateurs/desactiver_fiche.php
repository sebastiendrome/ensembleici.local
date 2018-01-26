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
    $page = $_POST['page'];
    $id = $_POST['id'];

    switch ($page) {
        case 'editorial' : $table = 'editorial'; break;
        case 'agenda' : $table = 'evenement'; break;
        case 'evenement' : $table = 'evenement'; break;
        case 'structure' : $table = 'structure'; break;
        case 'petiteannonce' : $table = 'petiteannonce'; break;
        case 'petite-annonce' : $table = 'petiteannonce';  break;
        default: $table = 'forum'; break;
    }
    
    $requete = "UPDATE $table SET etat = 0 WHERE no = ".$id;
    $res = $connexion->prepare($requete);
    $res->execute();
}
else {
    $return_code = '10';
}

$tab = array(); 
$tab['code'] = $return_code; 

$reponse = json_encode($tab); 
echo $reponse; 
?>
