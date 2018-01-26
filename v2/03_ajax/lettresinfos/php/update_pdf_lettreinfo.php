<?php
session_name("EspacePerso");
session_start();
require ('../../../01_include/_var_ensemble.php');
require ('../../../01_include/_connect.php');

$agenda = '';
if ($_POST['agenda'] != '') {
    $agenda = $_POST['agenda'];
}
$annonces = '';
if ($_POST['annonce'] != '') {
    $annonces = $_POST['annonce'];
}
$requete_update = "UPDATE lettreinfo SET pdf_agenda = :agenda, pdf_annonces = :annonces WHERE no = :no";
$res_update = $connexion->prepare($requete_update);
$res_update->execute(array(":no" => $_POST['id'], ":agenda" => $agenda, ":annonces" => $annonces)) or die ("requete ligne 15 : ".$requete_update);

$tab = array(); 
$tab['code'] = 0;
$reponse = json_encode($tab); 
echo $reponse;

?>