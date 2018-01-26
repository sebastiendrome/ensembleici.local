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

$requete_tag = "UPDATE tag SET titre = :t WHERE no = :no";
$param_insert = array(":t" => $_POST["libelle"], ":no" => $_POST['no']);
execute_requete($requete_tag,$param_insert);

if (isset($_POST['type']) && ($_POST['type'] != -1)) {
    $requete_vie = "INSERT INTO vie_tag (no_vie, no_tag) VALUES(:v, :t)";
    $param_vie = array(":t" => $_POST['no'], ":v" => $_POST['type']);
    execute_requete($requete_vie,$param_vie);
}

$tab['code'] = $return_code; 
$reponse = json_encode($tab); 
echo $reponse; 
?>
