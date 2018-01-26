<?php
/*****************************************************
**  Activer / désactiver un utilisateur
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$action = $_GET['action'];
$id = intval($_GET['id']);

//si une variable manque
if ((!$id) && (!$action)) {
	$_SESSION['message'] = "Erreur. Veuillez sélectionner un élément à modifier.";
} else {

   switch ($action) {
    case "desact":
    	$query = "UPDATE `newsletter` SET etat=0 WHERE no=:id";
    	$envoye = $cc_desact." avec succès.";
    	break;
    case "act":
    	$query = "UPDATE `newsletter` SET etat=1 WHERE no=:id";
    	$envoye = $cc_act." avec succès.";
    	break;
    }
  
    if (!$_SESSION['message']){
	$activer = $connexion->prepare($query);
	$activer->execute(array(':id'=>$id)) or die ("Erreur ".__LINE__." : ".$query);
      	$_SESSION['message'] = $envoye;
    }

}
header("location:admin.php");
exit();

?>