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
    $objet = $_POST['objet'];
    $date = substr($_POST['date_debut'], 6, 4).'-'.substr($_POST['date_debut'], 3, 2).'-'.substr($_POST['date_debut'], 0, 2);
    $requete = "INSERT INTO lettreinfo(objet,date_debut,date_creation, territoires_id) VALUES(:o,:d,CURRENT_TIMESTAMP, :t)";
    $res_requete = $connexion->prepare($requete);
    $res_requete->execute(array(":o"=>$objet,":d"=> $date, ":t"=>$_SESSION["utilisateur"]["territoire"])) or die("error");
    $no_lettre = $connexion->lastInsertId();
}
else {
    $return_code = '10';
}

$tab['code'] = $return_code; 
$tab['no'] = $no_lettre;
$reponse = json_encode($tab); 
echo $reponse; 
?>
