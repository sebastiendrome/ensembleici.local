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
        case 'editorial' : $table = 'editorial'; $fields = 'no, titre'; break;
        case 'agenda' : $table = 'evenement'; $fields = 'no, titre'; break;
        case 'evenement' : $table = 'evenement'; $fields = 'no, titre'; break;
        case 'structure' : $table = 'structure'; $fields = 'no, nom'; break;
        case 'petiteannonce' : $table = 'petiteannonce'; $fields = 'no, titre'; break;
        case 'petite-annonce' : $table = 'petiteannonce'; $fields = 'no, titre';  break;
        default: $table = 'forum'; $fields = 'no, titre'; break;
    }
    
    $requete = "SELECT $fields FROM $table WHERE no = ".$id;
    $res = $connexion->prepare($requete);
    $res->execute();
    $tab = $res->fetch();
    if ($table == 'structure') {
        $titre = $tab['nom'];
    }
    else {
        $titre = $tab['titre'];
    }
}
else {
    $return_code = '10';
}

$tab = array(); 
$tab['code'] = $return_code; 
if ($return_code == '0') {
    $tab['id'] = $id;
    $tab['page'] = $page;
    $tab['titre'] = $titre;
}

$reponse = json_encode($tab); 
echo $reponse; 
?>
