<?php
header('Content-type: text/html; charset=utf-8'); 
include "_var_ensemble.php";
//  Paramètres d'envoi
$destinataire = $email_admin.",olivier@africultures.com,maxime@africultures.com";
if ($email_admin_2) $destinataire .= ",".$email_admin_2;
$defo_expediteur = $email_admin;
$objet = "[ensembleici.fr] Contact sur Ensemble ici";
$erreurs = array();

$nom = $telephone = $adresse = $addr_mail = $message = $expediteur = "";

foreach($_POST as $k=>$v) {
	if(ini_get('magic_quotes_gpc')) $_POST[$k]=stripslashes($_POST[$k]);	
	$_POST[$k]=htmlspecialchars(strip_tags($_POST[$k]));
}

if(!preg_match('/[^a-z]/',$_POST['type'])){
	if ($_POST['type'] == "abus") {
		$form_abus = true;
		$objet = "Abus signalé sur www.ensembleici.fr";
		if(!preg_match('/[^a-zA-Z ]/',$_POST['objet'])){
			$objetabus = "<b>Objet : </b>".$_POST['objet']."<br/>\n";
		}
	}
}

if ($_POST['nom']) {
  if(strlen($_POST['nom'])>=200){
  	$erreurs[]='Nom : Taille trop importante : '.strlen($_POST['nom']).' carract&egrave;res (200 autoris&eacute;s).';
  } else {
    // nom OK, formatage pour le mail
    $current_encoding_nom = mb_detect_encoding($_POST['nom'], 'auto'); 
    if ($current_encoding_nom == "UTF-8")
      $nom_prep = $_POST['nom'];
    else
      $nom_prep = iconv($current_encoding_nom, 'UTF-8', $_POST['nom']);
    // $nom_prep = htmlentities($nom_prep, ENT_QUOTES, "UTF-8");
    // $nom_prep = nl2br($nom_prep);
    $nom = "<b>Nom : </b>".$nom_prep."<br/>";
  }
} else {
  	if (!$form_abus) $erreurs[]='Veuillez saisir votre nom.';
}

if ($_POST['telephone']) {
  $tel = preg_replace('/[^0-9]/', '', $_POST['telephone']); // supprime tout ce qui n'est pas un nombre
//  $tel = ereg_replace("[^0-9]","",$_POST['telephone']);
  if(!preg_match("#^0[1-9][0-9]{8}$#",$tel)){
  	$erreurs[]='T&eacute;l&eacute;phone : Format incorrect. (0123456789)';
  } else {
    $telephone = "<b>T&eacute;l&eacute;phone : </b>".$tel."<br/>\n";
  }
}

if ($_POST['adresse']) {
  if(strlen($_POST['adresse'])>=500){
  	$erreurs[]='Adresse : Taille trop importante : '.strlen($_POST['adresse']).' carract&egrave;res (500 autoris&eacute;s).';
  } else {
    $current_encoding_adr = mb_detect_encoding($_POST['adresse'], 'auto'); 
    if ($current_encoding_adr == "UTF-8")
      $adresse_prep = $_POST['adresse'];
    else
      $adresse_prep = iconv($current_encoding_adr, 'UTF-8', $_POST['adresse']);
    $adresse_prep = htmlentities($adresse_prep, ENT_QUOTES, "UTF-8");
    $adresse_prep = nl2br($adresse_prep);
    $adresse = "<b>Adresse : </b>".$adresse_prep."<br/>\n";
  }
}

if ($_POST['email']) {
  $_POST['email'] = mb_strtolower($_POST['email']);
  if(!preg_match('~^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$~',$_POST['email'])){
  	$erreurs[]='EMail : Format invalide. ('.$_POST['email'].")";
  } else {
  	$expediteur = $_POST['email'];
		$addr_mail = "<b>EMail : </b><a href='mailto:".$expediteur."'>".$expediteur."</a><br/>\n";
  }
} else {
  	if (!$form_abus) $erreurs[]='Veuillez saisir votre email.';
  	$expediteur = $defo_expediteur;
}

if ($_POST['message']) {
  // verif taille > 4000 carractères
  if(strlen($_POST['message'])>=4000){
  	$erreurs[]='Message : Taille trop importante : '.strlen($_POST['message']).' carract&egrave;res (4000 autoris&eacute;s).';
  } else {
	if ($objetabus) $message = $objetabus;
  
  $current_encoding_msg = mb_detect_encoding($_POST['message'], 'auto'); 
  if ($current_encoding_msg == "UTF-8")
    $message_prep = $_POST['message'];
  else
    $message_prep = iconv($current_encoding_msg, 'UTF-8', $_POST['message']);
  $message_prep = htmlentities($message_prep, ENT_QUOTES, "UTF-8");
  $message_prep = nl2br($message_prep);
  $message .= "<b>Message : </b><br/>".$message_prep."<br/>\n";

  }
} else {
  	$erreurs[]='Quel est votre message ?';
}

/* Sécurité domaine */
$referer= getenv("HTTP_REFERER");
if ($referer == "") { $erreurs[] = "Vous ne pouvez pas appeler ce script directement !"; }

$headers = 'From: ' . $expediteur . "\r\n";
$headers .='Content-Type: text/html; charset="UTF-8"'."\r\n";

// Préparation contenu
$datedemande = date("d-m-Y")." &agrave; ".date("H:i");
$contenu = "<h3>Contact sur le site Ensemble ici</h3>\n";
$contenu .= "<b>Date : </b>".$datedemande."<br/>\n";
$contenu .= $nom.$telephone.$adresse.$addr_mail.$message;

$message = $emails_header.$contenu.$emails_footer;

/*
$boundary = "-----=" . md5( uniqid ( rand() ) );
$headers = "From: $expediteur \n"; 
$headers .="Reply-To:".$email_admin.""."\n"; 
$headers .= "X-Mailer: PHP/".phpversion()."\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html; charset='UTF-8'; boundary=\"$boundary\"";
$headers .='Content-Transfer-Encoding: quoted-printable';
*/

if(count($erreurs)==0) {
  $resultat_mail = mail($destinataire,$objet,$message,$headers);
  if ($resultat_mail){
    	$reponse = "true";
  	} else {
    	$reponse = "<strong>Erreur dans l'envoi du formulaire.</strong> Nous vous prions de nous excuser pour le d&eacute;sagr&eacute;ment, et nous vous conseillons d'utiliser l'email : ".$destinataire." .";
  	} 
} else {
  $reponse = "<ol class=\"message-alertes\">";
  // affichage erreurs détectées au début du script
	for($i=0;$i<count($erreurs);$i++)
	{
  	$reponse .= "<li>".$erreurs[$i]."</li>";
	}
  $reponse .= "</ol>";
}

$array['reponse'] = $reponse;
echo json_encode($array);
?>