<?php
/*******************
Ce fichier récupère : no_message, no_sujet, no_utilisateur, et checked
**/

require ('_connect.php');
//verifica si una persona ya ha escrito en un ville

$no_utilisateur = $_POST['no_utilisateur'];
$no_sujet =  $_POST['no_ville'];
$no_message =  $_POST['no_msg'];
$checked = (bool)$_POST['checked'];

//On récupère l'entrée dans la base de données.
$req_select = "SELECT * FROM message_utilisateur WHERE no_utilisateur=:nou AND no_message=:nom AND no_sujet=:nos";
$res_select = $connexion->prepare($req_select);
$res_select->execute(array(":nou"=>$no_utilisateur,":nos"=>$no_sujet,":nom"=>$no_message)) or die("ligne 16");
$tab_select = $res_select->fetchAll();
//S'il n'existe pas, on l'ajoute.
if(count($tab_select)==0){
	$req_insert = "INSERT INTO message_utilisateur(no_utilisateur,no_sujet,no_message) VALUES(:nou,:nos,:nom)";
	$res_insert = $connexion->prepare($req_insert);
	$res_insert->execute(array(":nou"=>$no_utilisateur,":nos"=>$no_sujet,":nom"=>$no_message)) or die("ligne 22");
}

if($checked){ //Abonnement
	$req_update = "UPDATE message_utilisateur SET inscrit=1 WHERE no_utilisateur=:nou AND no_message=:nom AND no_sujet=:nos";
}
else{ //Désabonnement
	$req_update = "UPDATE message_utilisateur SET inscrit=0 WHERE no_utilisateur=:nou AND no_message=:nom AND no_sujet=:nos";
}
$res_update = $connexion->prepare($req_update);
$res_update->execute(array(":nou"=>$no_utilisateur,":nos"=>$no_sujet,":nom"=>$no_message)) or die("ligne 32");
?>