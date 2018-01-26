<?php
/*****************************************************
**  Activer / désactiver
******************************************************/
session_name("EspacePerso");
session_start();
// Utilisateur connecté ?
require ('connexion_verif.php');

require_once('_connect.php');
require_once('_var_ensemble.php');
$_SESSION['message']=false;
$action = $_GET['action'];
$id_item = intval($_GET['id']);

//si une variable manque
if ((!$id_item) && (!$action)) {
	$_SESSION['message'] = "Erreur. Veuillez sélectionner une petite annonce à modifier.";
} else {

   switch ($action) {
    case "desact":
    	$query = "UPDATE `petiteannonce` SET etat=0 WHERE no=:id_item";
    	$envoye = "Petite annonce désactivée avec succès.";
    	break;
    case "act":
    	$query = "UPDATE `petiteannonce` SET etat=1 WHERE no=:id_item";
    	$envoye = "Petite annonce activée avec succès.";
    	break;
    }
  
    if (!$_SESSION['message']){
	$activer = $connexion->prepare($query);
	$activer->execute(array(':id_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$query);
      	$_SESSION['message'] = $envoye;
    }

}
echo $_SESSION['message'];

?>