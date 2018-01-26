<?php
/*****************************************************
**  Activer / désactiver un évenement
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$action = $_GET['action'];
$id_event = intval($_GET['id']);

//si une variable manque
if ((!$id_event) && (!$action)) {
	$_SESSION['message'] = "Erreur. Veuillez sélectionner un élément à modifier.";
} else {

   switch ($action) {
    case "desact":
    	$query = "UPDATE `evenement` SET etat=0 WHERE no=:id_event";
    	$envoye = $cc_desact." avec succès.";
    	break;
    case "act":
    	$query = "UPDATE `evenement` SET etat=1 WHERE no=:id_event";
    	$envoye = $cc_act." avec succès.";
    	break;
    }
  
    if (!$_SESSION['message']){
	$activer = $connexion->prepare($query);
	$activer->execute(array(':id_event'=>$id_event)) or die ("Erreur 30 : ".$query);
      	$_SESSION['message'] = $envoye;
    }

}
header("location:admin.php");
exit();

?>