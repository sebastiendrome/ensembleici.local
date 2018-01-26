<?php
session_name("EspacePerso");
session_start();

header('Content-type: text/html; charset=utf-8'); 
include ('01_include/_connect.php');
include ('01_include/_var_ensemble.php');

$no = intval($_REQUEST['no']);
$type = strtolower($_REQUEST['type']);
$mail_envoye = false;

if ((preg_match("#^(structure|evenement|annonce|petiteannonce)$#", $type))&&($no))
{
	// Annonce
	if ($type == "annonce")
		$type = "evenement";

	// récupération de l'email
	if ( ($type == "evenement") || ($type == "structure") )
	{
		if ($type == "evenement")
		{
		    $type_accentue = "&eacute;v&egrave;nement";
		    $chp_titre = "titre";
		}
		else if ($type == "structure")
		{
		    $type_accentue = "structure";
		    $chp_titre = "nom";
		}
		
		// Structures et evements
		$sql_previsu="SELECT email, ".$chp_titre." FROM ".$type." WHERE no=:no AND etat = 1";
		$res_previsu = $connexion->prepare($sql_previsu);
		$res_previsu->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
		$pv=$res_previsu->fetchAll();
		$destinataire = $pv[0]['email'];
		$nom_elt = $pv[0][$chp_titre];
	}
	elseif ($type == "petiteannonce")
	{
		$type_accentue = "petite annonce";
		$chp_titre = "titre";
		// Petites annonces => 1er contact

		//recuperation des contacts
		$sql_liaison_contact="SELECT no_contact FROM ".$type."_contact WHERE no_".$type."=:no ORDER BY no_contact DESC";
		$res_liaison_contact = $connexion->prepare($sql_liaison_contact);
		$res_liaison_contact->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
		$tab_liaison_contact=$res_liaison_contact->fetchAll();
		
		//recuperation du premier contact
		$sql_contact="SELECT email FROM contact WHERE no=:no";
		$res_contact = $connexion->prepare($sql_contact);
		$res_contact->execute(array(':no'=>$tab_liaison_contact[0]['no_contact'])) or die ("Erreur ".__LINE__.".");
		$tab_contact=$res_contact->fetchAll();
		$destinataire = $tab_contact[0]['email'];

		// Récup du nom de l'élement
		$sql_previsu="SELECT ".$chp_titre." FROM ".$type." WHERE no=:no AND etat = 1";
		$res_previsu = $connexion->prepare($sql_previsu);
		$res_previsu->execute(array(':no'=>$no)) or die ("Erreur ".__LINE__.".");
		$pv=$res_previsu->fetchAll();
		$nom_elt = $pv[0][$chp_titre];
	}

	// utilisateur connecté ? on récupère son email
	$id_utilisateur = intval($_SESSION['UserConnecte_id']);
	if ($id_utilisateur)
	{
		$sql_utilisateurs="SELECT * FROM $table_user WHERE no = :no";
		$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
		$res_utilisateurs->execute(array(':no'=>$id_utilisateur));
		$tab_utilisateur=$res_utilisateurs->fetchAll();
		if(count($tab_utilisateur))
			$expediteur = $tab_utilisateur[0]['email'];
	}

	if ($destinataire != "")
	{
		//verification du code de sécurité
		if (isset($_POST['userCaptchaCode']))
		{
			if (!empty($_POST['userCaptchaCode']))
			{
				$userCaptchaCode = $_POST['userCaptchaCode'];
				/* Cryptage saisie en MD5 +  comparaison avec session */
				if (md5($userCaptchaCode) != $_SESSION['sysCaptchaCode'])
					$erreurs[]='Le code de sécurité saisi est erronné.';
				else
				{

					$objet = "Contact sur www.ensembleici.fr";
					$defo_expediteur = $email_admin;
					
					// on vérifie en php et on envoi le mail
					$erreurs = array();
					
					$nom = $mail = $message = "";
					
					foreach($_POST as $k=>$v) {
						if(ini_get('magic_quotes_gpc')) $_POST[$k]=stripslashes($_POST[$k]);	
						$_POST[$k]=htmlspecialchars(strip_tags(utf8_decode($_POST[$k])));
					}
					
					if ($_POST['nom']) {
					  if(strlen($_POST['nom'])>=200){
						$erreurs[]='Nom : Taille trop importante : '.strlen($_POST['nom']).' carract&egrave;res (200 autoris&eacute;s).';
					  } else {
					    // nom OK, formatage pour le mail
					    $nom = "<b>Nom : </b>".addslashes(htmlspecialchars(trim($_POST['nom'])))."<br/>";
					  }
					}
					else
						$erreurs[]='Veuillez saisir votre nom.';
					
					// Email
					if ($_POST['mail']) {
					  $_POST['mail'] = mb_strtolower($_POST['mail']);
					  if(!preg_match('~^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$~',$_POST['mail'])){
						$erreurs[]='EMail : Format invalide. ('.$_POST['mail'].")";
					  } else {
						$expediteur = $_POST['mail'];
						$addr_mail = "<b>EMail : </b><a href='mailto:".$expediteur."'>".$expediteur."</a><br/>\n";
					  }
					} else {
						$erreurs[]='Veuillez saisir votre email.';
						$expediteur = $defo_expediteur;
					}
					
					if ($_POST['messageenv']) {
					  // verif taille > 4000 carractères
					  if(strlen($_POST['messageenv'])>=4000){
						$erreurs[]='Message : Taille trop importante : '.strlen($_POST['messageenv']).' carract&egrave;res (4000 autoris&eacute;s).';
					  } else {
						$message .= "<b>Message : </b><br/>".nl2br(htmlspecialchars(strip_tags(trim($_POST['messageenv']))))."<br/>\n";
					  }
					}
					else
						$erreurs[]='Quel est votre message ?';
					
					
					/* Sécurité domaine */
					$referer= getenv("HTTP_REFERER");
					if ($referer == "") { $erreurs[] = "Vous ne pouvez pas appeler ce script directement !"; }
					
					// Préparation contenu
					$datedemande = date("d/m/Y")." &agrave; ".date("H:i");
					$contenu = "<h3>Contact sur ensembleici.fr : </h3>\n";
					$contenu .= "<b>Date : </b>".$datedemande."<br/>\n";
					$contenu .= $nom.$addr_mail.$message;

					// Nom source si définie
					if( (isset($nom_elt)) || ($nom_elt != "" ) )
					{
						$contenu .= "<br/><br/>- - -<br/>Ce message concerne votre ".$type_accentue." : ".$nom_elt;
						// en provenance de notre domaine ?
						if (!substr($_SERVER['HTTP_REFERER'], 0, strlen($root_site)) != $root_site)
							$mon_referer = $_SERVER['HTTP_REFERER'];

						if(isset($mon_referer) || $mon_referer != "" )
							$contenu .= "<br/>Visible &agrave; l'adresse suivante : <a href=\"".$mon_referer."\" title='voir sur Ensemble ici'>".$mon_referer."</a>";

					}

						
					$message = $emails_header.utf8_encode($contenu).$emails_footer;
					$boundary = "-----=" . md5( uniqid ( rand() ) );
					$headers = "From: $expediteur \n"; 
					$headers .="Reply-To:".$expediteur.""."\n"; 
					$headers .= "X-Mailer: PHP/".phpversion()."\n";
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-Type: text/html; charset='UTF-8'; boundary=\"$boundary\"";
					$headers .='Content-Transfer-Encoding: quoted-printable';
					
					if(count($erreurs)==0) {
					  $mail_envoye = mail($destinataire,$objet,$message,$headers);
					  if ($mail_envoye)
						$_SESSION['message'] .= "Votre message a été envoyé avec succès.";
					  else
						$_SESSION['message'] .= "<strong>Erreur dans l'envoi du message.</strong> Nous vous prions de nous excuser pour le d&eacute;sagr&eacute;ment.";
					}
				}
			}
		}
		// Affichage des erreurs
		if(count($erreurs))
		{
			$_SESSION['message'] .= "<ul class=\"message-alertes\">";
			// affichage erreurs détectées au début du script
			for($i=0;$i<count($erreurs);$i++)
			{
			      $_SESSION['message'] .= "<li>".$erreurs[$i]."</li>";
			}
			$_SESSION['message'] .= "</ul>";
		}

	}
}
else
{
	// Erreur type ou no => Ferme la colorbox
	$_SESSION['message'] .= "<p>Impossible d'envoyer un email à l'auteur de cette fiche.</p>";
}
?>