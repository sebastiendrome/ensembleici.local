<?php
// ----------------------------------------
// Vérification si l'internaute est connecté ou non, pour les pages de l'espace perso
// /!\ Fichier à appeler après avoir déclaré la session
// ----------------------------------------

require ('_var_ensemble.php');

// Si aucune information de session, on indique au membre qu'il faut se connecter
//if(!@$_SESSION['UserConnecte'])
//{
//
//	// Enregistre la page cible pour redirection après connexion
//	if ((!isset($_SESSION['connexion_redirect_page']) || $_SESSION['connexion_redirect_page'] == "" ) && (isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] !== "" ))
//			$_SESSION['connexion_redirect_page'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
//
//	// si provenance d'un autre domaine on supprime la redirection
//	if (substr($_SESSION['connexion_redirect_page'], 0, strlen($root_site)) != $root_site)
//		unset($_SESSION['connexion_redirect_page']);
//
//	// Redirection vers l'identification
//	$page = 'identification.php';
//	header("Location: $root_site$page");
//	exit;
//}
//else
//{
	$UserConnecte_id_fromSession = addslashes($_SESSION['UserConnecte']);
        $UserConnecte_id_fromSession = 27;
        $UserConnecte_email = 'olivier@africultures.com';
	// Connexion MySQL
	require ('_connect.php');
	// y a t il une connexion avec cet identifiant ?
	if (isset($pageGestion))
	{
		// Pages admin
//            $strQuery = "SELECT no,droits FROM `utilisateur`
//				WHERE id_connexion=:UserConnecte_id_fromSession
//				AND `email`=:UserConnecte_email
//				AND (`droits`='A' OR `droits`='E')";
		$strQuery = "SELECT no,droits FROM `utilisateur`
				WHERE  `email`=:UserConnecte_email
				AND (`droits`='A' OR `droits`='E')";
	}
	else
	{
		$strQuery = "SELECT no FROM `utilisateur`
				WHERE id_connexion=:UserConnecte_id_fromSession
				AND `email`=:UserConnecte_email";
	}
        
	
	// Execution requête préparée
	$verif = $connexion->prepare($strQuery);
	$verif->execute(array(':UserConnecte_id_fromSession' => $UserConnecte_id_fromSession,':UserConnecte_email' => $UserConnecte_email));

	// Admin : on récupère les droits
	if (isset($pageGestion))
	{
		$vl = $verif->fetch(PDO::FETCH_OBJ);
		$connexion_admin_droits = $vl->droits;
                $connexion_admin_droits = 'A';
	}

	$verifnb = $verif->rowCount();
	$verif->closeCursor();
//	$verif = NULL;
//	if ($verifnb)
//	{
//		//S'il n'en existe pas
//		if($verifnb === 0)	
//		{
//			//On détruit la session afin de ne pas faire de boucle infinie
//			session_unset();
//			session_destroy();
//		
//			// Redirection vers l'identification
//			$page = 'inscription.php?etape=etape1&erreur=connexion';
//			header("Location: $root_site$page");
//			exit;
//		}
//	}
//	else
//	{
//		// ERREUR => Redirection vers l'identification
//		$page = 'identification.html?erreur=couple';
//		header("Location: $root_site$page");
//		exit;			
//	}
//}
?>