<?php
session_name("EspacePerso");
session_start();
require ('../01_include/_var_ensemble.php');
require ('./_connect.php');
$requete = "SELECT pseudo FROM utilisateur WHERE no=:no";
$res = $connexion->prepare($requete);
$res->execute(array(":no"=>$_SESSION['UserConnecte_id']));
$tab = $res->fetchAll();
echo $tab[0]["pseudo"];
?>
