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

if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    // recherche des infos utilisateur
    $requete_utilisateur = "SELECT no, email, pseudo, no_ville, newsletter FROM utilisateur WHERE no = :iduser";
    $tab_utilisateur = execute_requete($requete_utilisateur,array(":iduser"=>$_POST["id"]));

    $requete_update = "UPDATE utilisateur SET email = :email, pseudo = :pseudo, no_ville = :no_ville, newsletter = :news";
    if ($_POST['password'] != 'monpass') {
        $pass = md5($_POST['email'].trim($_POST['password']).$cle_cryptage);
        $requete_update .= ", mot_de_passe = :pass";
        $param_update = array(":no" => $_POST['id'], ":email" => $_POST["email"], ":pseudo" => filter_var($_POST['pseudo'], FILTER_SANITIZE_STRING), 
        ":no_ville" => $_POST['no_ville'], ":news" => $_POST['news'], ":pass" => $pass);
    }
    else {
        $param_update = array(":no" => $_POST['id'], ":email" => $_POST["email"], ":pseudo" => filter_var($_POST['pseudo'], FILTER_SANITIZE_STRING), 
        ":no_ville" => $_POST['no_ville'], ":news" => $_POST['news']);
    }
    $requete_update .= " WHERE (no = :no)";
//    $param_update = array(":no" => $_POST['id'], ":email" => $_POST["email"], ":pseudo" => filter_var($_POST['pseudo'], FILTER_SANITIZE_STRING), 
//        ":no_ville" => $_POST['no_ville'], ":news" => $_POST['news'], ":pass" => $pass);
    $mareq = execute_requete($requete_update,$param_update);
    
    
}
else {
    $return_code = '1';
}

$tab = array(); 
$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
