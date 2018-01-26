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

$pass = md5($_POST['email'].trim($_POST['password']).$cle_cryptage);
$requete_update = "UPDATE utilisateur SET mot_de_passe = :pass WHERE (no = :no)";
$param_update = array(	":no" => $_POST['id'], ":pass" => $pass);
$mareq = execute_requete($requete_update,$param_update);

$tab = array(); 
$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
