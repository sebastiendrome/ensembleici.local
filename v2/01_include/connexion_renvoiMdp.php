<?php
// ----------------------------------------
// Traitement du renvoi du mot de passe
// ----------------------------------------
require ('./_var_ensemble.php');

if(isset($_POST['email'])) $_POST['email'] = trim($_POST['email']);

// Si rien n'est saisi
if($_POST['email']==""){
	// Redirection vers l'identification
	$page = 'inscription.php?etape=etape1&erreur=nologin';
	header("Location: $root_site$page");
	exit;
}

// Variables pour la requête
$loginbdd = $_POST['email'];

// L'utilisateur existe ?
require ('./_connect.php');
$strQuery = "SELECT no FROM `$table_user` WHERE email=:loginbdd AND etat=1 LIMIT 0, 1";
$query = $connexion->prepare($strQuery);
$query->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
$query->execute();
$resultSelect = $query->fetch();
// $query->debugDumpParams();
$count_cores = $query->rowCount();
$query->closeCursor();
$query = NULL;

if($count_cores === 1){
// L'utilisateur existe
	
	// Création et enregistrement du code de vérification
	$code_alea = id_aleatoire();
	$StrQueryUpd = "UPDATE `$table_user`
		    SET code_reinit_mot_de_passe=:code_alea
		    WHERE email=:loginbdd";
	$QueryUpd = $connexion->prepare($StrQueryUpd);
	$QueryUpd->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
	$QueryUpd->bindParam(":code_alea", $code_alea, PDO::PARAM_STR);
	$QueryUpd->execute();
	$QueryUpd->closeCursor();
	$QueryUpd = NULL;
	
	// envoi email avec le lien pour modif du mot de passe
	$sujet = "[ Ensemble Ici ] Modification de votre mot de passe";
	$message = "Bonjour,<br/><br/>
	Vous avez demandé la modification de votre mot de passe d'accès à votre espace personnel 'Ensemble ici'.<br/>S'il s'agit d'une erreur, ignorez ce message et la demande ne sera pas prise en compte.<br/><br/>
	Votre identifiant : $loginbdd<br/><br/>
	Pour renouveler votre mot de passe, cliquez sur le lien suivant :<br/>
	<a style='font-style:italic; font-size:11px; color:#E16A0C' href='".$root_site."modification_mot_de_passe.php?key=$code_alea&login=$loginbdd'>".$root_site."modification_mot_de_passe.php?key=<br/>$code_alea&login=$loginbdd</a>";
	$message = $emails_header.$message.$emails_footer;
	$boundary = "-----=" . md5( uniqid ( rand() ) );
	$headers = "From: $email_admin \n"; 
	$headers .="Reply-To:".$email_admin.""."\n"; 
	$headers .= "X-Mailer: PHP/".phpversion()."\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-Type: text/html; charset='UTF-8'; boundary=\"$boundary\"";
	$headers .='Content-Transfer-Encoding: quoted-printable';
	$mail=strtolower($loginbdd);
	$destinataire = $dest;
	@mail($mail,$sujet,$message,$headers);

	// Redirection vers la page de l'espace perso
	$page = 'inscription.php?etape=etape1&erreur=envoimdp';
	header("Location: $root_site$page");
	exit;	
}
else
{
	// L'utilisateur n'existe pas
	$page = 'inscription.php?etape=etape1&erreur=nologin';
	header("Location: $root_site$page");
	exit;
}


?> 

