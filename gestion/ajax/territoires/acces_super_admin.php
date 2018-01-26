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
    if ($_POST['pass'] == '39efc9h5') {
        setcookie("is_sa", 1, time() + 24*3600,"/", null, false, true);
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
