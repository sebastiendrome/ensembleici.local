<?php
// ----------------------------------------
// Intégration du contenu
// ----------------------------------------

	// Eviter les carractères spéciaux
	if ((isset($_GET['etape'])) && (!preg_match("([[:alnum:]])", $_GET['etape'])))
		$_GET['etape']="";

	if(isset($_GET['etape']) && ($_GET['etape']!=""))
		$page_inscription=$_GET['etape'];
	
	switch($page_inscription) 
	{
		case "etape1":
			$titre_inscription = "Identification";
			$mainPage = "identification.php";
			break;
		case "validation_inscription":
			$titre_inscription="Validation du formulaire d'inscription";
			$mainPage = "validation_inscription.php";
			break;
		default :
			$titre_inscription = "Bienvenue,";
			$mainPage = "accueil.php";
			break;
	}
// Gestion des erreurs de connexion
	if( isset($_GET['erreur']) AND (preg_match("/[a-z]/i",$_GET['erreur'])))
	{
		switch($_GET['erreur']) 
		{
			case "login":
				$msg_err = "Vous devez obligatoirement entrer un login (votre email d'inscription) pour vous connecter à votre espace personnel.";
				break;
			case "passe":
				$msg_err = "Vous devez obligatoirement entrer un mot de passe pour vous connecter.";		
				break;
			case "couple":
				$msg_err = "Erreur dans l'identification. Merci de recommencer.";		
				break;
			case "connexion":
				$msg_err = "Merci de vous identifier pour acc&eacute;der &agrave; votre espace personnel.";		
				break;
			case "nologin":
				$msg_err = "L'email d'inscription que vous avez saisi n'est pas valide ou n'est plus inscrit sur ensemble ici.";
				break;
			case "envoimdp":
				$msg_err = "Un email a été envoyé à l'adresse indiquée. Cliquez sur le lien qu'il contient pour modifier votre mot de passe.";
				break;
		}
	}


?>