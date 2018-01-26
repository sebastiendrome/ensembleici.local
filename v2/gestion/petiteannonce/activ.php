<?php
/*****************************************************
**  Activer / désactiver
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

$action = $_GET['action'];
$id_item = intval($_GET['id']);

//si une variable manque
if ((!$id_item) && (!$action)) {
	$_SESSION['message'] = "Erreur. Veuillez sélectionner un élément à modifier.";
} else {

   switch ($action) {
    case "desact":
    	$query = "UPDATE `petiteannonce` SET etat=0 WHERE no=:id_item";
    	$envoye = $cc_desact." avec succès.";
    	break;
    case "act":
    	$query = "UPDATE `petiteannonce` SET etat=1 WHERE no=:id_item";
    	$envoye = $cc_act." avec succès.";
    	break;
    }
  
    if (!$_SESSION['message']){
	$activer = $connexion->prepare($query);
	$activer->execute(array(':id_item'=>$id_item)) or die ("Erreur ".__LINE__." : ".$query);
      	$_SESSION['message'] = $envoye;
    }

}
header("location:admin.php");
exit();

?>