<?php

// ----------------------------------------
// Traitement de la connexion à l'espace perso
// ----------------------------------------

session_name("EspacePerso");
session_start();
require ('./_var_ensemble.php');

$table_derniere_connexion = "utilisateur_connexions"; // Table des connexion

if(isset($_GET['url']))
	$url = $_GET['url'];

if(isset($_REQUEST['login'])) $_REQUEST['login'] = trim($_REQUEST['login']);
if(isset($_REQUEST['mdp'])) $_REQUEST['mdp'] = trim($_REQUEST['mdp']);

// Si rien n'est saisi
if($_REQUEST['login']==""){
	// Redirection vers l'identification
	$page = 'inscription.php?etape=etape1&erreur=login';
	header("Location: $root_site$page");
	exit;
}
if($_REQUEST['mdp']==""){
	// Redirection vers l'identification
	$page = 'inscription.php?etape=etape1&erreur=passe';
	header("Location: $root_site$page");
	exit;
}

// Variables pour la requête
// voir http://www.commentcamarche.net/faq/8821-comment-bien-stocker-et-verifier-un-mot-de-passe
if($_GET['etape']=='creation')
{
	$mdpbdd = $_REQUEST['mdp'];
}
else
{	
	$mdpbdd = md5($_REQUEST['login'].$_REQUEST['mdp'].$cle_cryptage);
}
$loginbdd = $_REQUEST['login'];
$statutbdd = 1; //actif

// Construction de la requete
require ('./_connect.php');
$strQuery = "SELECT * FROM `$table_user` WHERE mot_de_passe=:mdpbdd AND email=:loginbdd AND etat=:etat LIMIT 0, 1";
$query = $connexion->prepare($strQuery);
$query->bindParam(":mdpbdd", $mdpbdd, PDO::PARAM_STR);
$query->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
$query->bindParam(":etat", $statutbdd, PDO::PARAM_INT);
$query->execute();
$resultSelect = $query->fetch();

// $query->debugDumpParams();
$count_cores = $query->rowCount();
$query->closeCursor();
$query = NULL;

	$no_ville = $resultSelect["no_ville"];
	$no_utilisateur = $resultSelect["no"];
	// Administrateur ou editeur ?
	if ($resultSelect["droits"]=="A") $estAdmin = true;
	if ($resultSelect["droits"]=="E") $estEditeur = true;

	// Création et enregistrement d'un identifiant aléatoire
	$alea = id_aleatoire();
	$StrQueryUpd = "UPDATE `$table_user`
		    SET id_connexion=:alea
		    WHERE mot_de_passe=:mdpbdd
		    AND email=:loginbdd";
	$QueryUpd = $connexion->prepare($StrQueryUpd);
	$QueryUpd->bindParam(":mdpbdd", $mdpbdd, PDO::PARAM_STR);
	$QueryUpd->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
	$QueryUpd->bindParam(":alea", $alea, PDO::PARAM_STR);
	$QueryUpd->execute();
	$QueryUpd->closeCursor();
	$QueryUpd = NULL;

	//création de la session avec l'identifiant aléatoire et l'email
	$_SESSION['UserConnecte'] = $alea;
	$_SESSION['UserConnecte_email'] = $loginbdd;
	$_SESSION['UserConnecte_id'] = $no_utilisateur;

	// On enregistre la ville dans un cookie, pour 1 an
	// setcookie("ville", $no_ville, time() + 365*24*3600, null, null, false, true);
	setcookie("id_ville", $no_ville, time() + 365*24*3600,"/", null, false, true);

	// log connexions
	$maintenant = date("Y-m-d H:i:s");
	$ip = $_SERVER['REMOTE_ADDR'];
	$StrQueryDC = "INSERT INTO `$table_derniere_connexion`
		    VALUES ('',:maintenant,:ip,:loginbdd)";
	$QueryDC = $connexion->prepare($StrQueryDC);
	$QueryDC->bindParam(":maintenant", $maintenant, PDO::PARAM_STR);
	$QueryDC->bindParam(":loginbdd", $loginbdd, PDO::PARAM_STR);
	$QueryDC->bindParam(":ip", $ip, PDO::PARAM_STR);
	$QueryDC->execute();
	$QueryDC->closeCursor();
	$QueryDC = NULL;

	// Enregistrement dernière connexion en session
	$querytim = "SELECT quand
			FROM `$table_derniere_connexion`
			WHERE `email`='$loginbdd'
			ORDER BY `no_connexion` DESC
			LIMIT 1,1";
	$requete_prepare_1=$connexion->prepare($querytim); 
	$requete_prepare_1->execute();
	while($lignes=$requete_prepare_1->fetch(PDO::FETCH_OBJ))
	{
		$lheure = substr($lignes->quand, 11, 5);
		$dern = datefr($lignes->quand)." à ".$lheure;
		$_SESSION['UserConnecte_derniereConnexion'] = $dern;
	}

?> 