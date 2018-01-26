<?php
session_name("EspacePerso");
session_start();
require ('../../../01_include/_var_ensemble.php');
require ('../../../01_include/_connect.php');


$requete = "INSERT INTO partenaireinstitutionnel (libelle, image, url, territoires_id) VALUE (:l, :i, :u, :t);";
$res = $connexion->prepare($requete);
$res->execute(array(":l" => $_POST['nom'], ":i" => 'img/'.$_POST['logo'], ":u" => $_POST['site'], ":t" => $_POST['territoire'])) or die ("requete ligne 15 : ".$requete);

?>