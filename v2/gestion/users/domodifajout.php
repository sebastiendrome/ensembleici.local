<?php
/*****************************************************
Gestion des utilisateurs
******************************************************/
session_name("EspacePerso");
session_start();
require_once "config.php";

// Vérifications
$id_user = intval($_POST['id_user']);
$mode_ajout = intval($_POST['mode_ajout']);
if (!$id_user){
  // aucun id à modifier
	$_SESSION['message'] .= "Erreur : ".$cc_aucun." à modifier ou ajouter.<br/>";
} else {

    $email = strtolower(trim($_POST['email']));
    $email_ancien = strtolower(trim($_POST['mail_1_verif']));

    if (!$email)
    	$_SESSION['message'] .= "Erreur : $cc_auc_nom.<br/>";
    else
    {
	if (!valid_email($email))
	{
	    // Email saisi invalide
	    $_SESSION['message'] .= "Erreur : Email $cc_de invalide.<br/>";
	}
	else
	{
	    // Email saisi ok
	    // email modifié ? Test de la dispo du nouveau nom
	    if ($email != $email_ancien)
	    {
		$sql_existe = "SELECT * FROM `utilisateur` WHERE email=:email";
		$res_existe = $connexion->prepare($sql_existe);
		$res_existe->execute(array(':email'=>$email)) or die ("Erreur ".__LINE__." : ".$sql_existe);
		$nb_user_existe = $res_existe->rowCount();
		if($nb_user_existe)
		    $_SESSION['message'] .= "Erreur : Cet email existe déjà pour un utilisateur.<br/>";
	    }
	}
    }

    // Variables
    $id_ville = intval($_POST['rech_idville']);
    $droits = $_POST['droits'];
    $etat = intval($_POST['etat']);
    $inscrit_newsletter = intval($_POST['inscrit_newsletter']);
    $verification_email = intval($_POST['verification_email']);
    $mdp1 = trim($_POST['mdp']);
    $mdp2 = trim($_POST['mdp2']);
    
    if (!isset($_SESSION['message'])) {

	// Mdp modifié ?
	if(($mdp1)&&($mdp2)&&($mdp1==$mdp2))
	{
		// login choisi
		if (!empty($email))
		    $loginbdd = $email;
		else
		    $loginbdd = $email_ancien;
		
		$mdp = md5($loginbdd.$mdp1.$cle_cryptage);
	}
	else
	{
		// Ancien mot de passe
		$mdp = $_POST['mdp_verif'];
	}

	if ($mode_ajout)
	{
		$code_alea = id_aleatoire();

	    $sql_elt = "INSERT INTO `utilisateur` (
				`no`,
				`email`,
				`no_ville`,
				`mot_de_passe`,
				`verification_email`,
				`droits`,
				`etat`,
				`newsletter`,
				`code_desinscription_nl`
			    ) VALUES (
				:no,
				:email,
				:id_ville,
				:mot_de_passe,
				:verification_email,
				:droits,
				:etat,
				:newsletter,
				:code_alea
			    )";
	    $insert_elt = $connexion->prepare($sql_elt);
	    $insert_elt->execute(array(
			    ':no'=>$id_user,
			    ':email'=>$email,
			    ':id_ville'=>$id_ville,
			    ':mot_de_passe'=>$mdp,
			    ':verification_email'=>$verification_email,
			    ':droits'=>$droits,
			    ':etat'=>$etat,
			    ':newsletter'=>$inscrit_newsletter,
			    ':code_alea'=>$code_alea

	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
    
	    $_SESSION['message'] .= "$cc_maj \"$email\" ajouté avec succès.<br/>";
	}
	else
	{
	    // Requête BDD
	    $sql_elt = "UPDATE `utilisateur`
		SET email=:email,
		    no_ville=:id_ville,
		    mot_de_passe=:mot_de_passe,
		    verification_email=:verification_email,
		    droits=:droits,
		    newsletter=:newsletter,
		    etat=:etat
		WHERE no=:no";
	    $maj_elt = $connexion->prepare($sql_elt);
	    $maj_elt->execute(array(
			    ':no'=>$id_user,
			    ':email'=>$email,
			    ':id_ville'=>$id_ville,
			    ':mot_de_passe'=>$mdp,
			    ':verification_email'=>$verification_email,
			    ':droits'=>$droits,
			    ':newsletter'=>$inscrit_newsletter,
			    ':etat'=>$etat
	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
		
			$code_droits=0;
		//	echo "alert('".$code_droits."')";
			if($droits=="A")
			{
				$code_droits=1;
			}
			else
			{
				if($droits=="E")
				{
					$code_droits=2;
				}
			}
		//	echo "alert('".$code_droits."')";
			//suppression droit
			$sql_delete="DELETE FROM `droit_utilisateur` WHERE no_utilisateur=:id_user";
			$delete = $connexion->prepare($sql_delete);
			$delete->execute(array(':id_user'=>$id_user)) or die ("Erreur ".__LINE__." : ".$sql_delete);
			$nb_supp = $delete->rowCount();
			if($code_droits>0)
			{
			//ajout_droit
			$sql_elt = "INSERT INTO `droit_utilisateur` (
				`no_utilisateur`,
				`no_droit`
			    ) VALUES (
				:id_user,
				:no_droit
			    )";
	    $insert_elt = $connexion->prepare($sql_elt);
	    $insert_elt->execute(array(
			    ':id_user'=>$id_user,
			    ':no_droit'=>$code_droits

	    )) or die ("Erreur ".__LINE__." : ".$sql_elt);
			}
		}
    
	    $_SESSION['message'] .= "$cc_maj \"$email\" modifié avec succès.<br/>";
	}
	
	header("location:admin.php");
	exit();
    }


header("location:modifajout.php?id=$id_user");
exit();
?>