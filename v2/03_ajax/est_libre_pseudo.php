<?php
session_name("EspacePerso");
session_start();
require ('../01_include/_var_ensemble.php');
require ('../01_include/_connect.php');
$requete = "SELECT pseudo FROM utilisateur WHERE pseudo=:p";
$res = $connexion->prepare($requete);
$res->execute(array(":p"=>urldecode($_POST['p'])));
$tab = $res->fetchAll();
echo json_encode((count($tab)==0));
?>
