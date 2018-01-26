<?php
/*****************************************************
Gestion des structures
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";
require_once('../../01_include/fonction_redim_image.php');

// V�rifications
$objet = $_POST['objet_lettre'];
$date = $_POST['date_debut'];
$requete = "INSERT INTO lettreinfo(objet,date_debut,date_creation, territoires_id) VALUES(:o,:d,CURRENT_TIMESTAMP, :t)";
$res_requete = $connexion->prepare($requete);
$res_requete->execute(array(":o"=>$objet,":d"=>date("Y-m-d",$date), ":t"=>$_GET['territoire'])) or die("error");
$no_lettre = $connexion->lastInsertId();
header("location:modifajout.php?id=$no_lettre");
exit();
?>