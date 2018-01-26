<?php
session_name("EspacePerso");
session_start();
require_once('../01_include/_var_ensemble.php');
//include('01_include/fonctions.php');
$login=strtolower(trim($_REQUEST['mail']));
$ville=$_REQUEST['ville'];
$cp=$_REQUEST['cp'];
$mot_de_passe=urldecode($_REQUEST['mdp']);
$pseudo=urldecode($_REQUEST['pseudo']);
$txt_erreur="";
$code_desinscription_nl = strtolower(trim($_REQUEST['code_desinscription_nl']));
$loginbdd=strtolower(trim($_REQUEST['mail']));
$mdpbdd = md5($_REQUEST['mail'].trim($_REQUEST['mdp']).$cle_cryptage);
$statutbdd = 1; //actif
include('../01_include/_connect.php');
$ok = true;
$alert = "";
$txt_err = "";
$code_err = 0;
if (isset($_REQUEST['userCaptchaCode'])) 
{
	if (!empty($_REQUEST['userCaptchaCode']))
	{
		$userCaptchaCode = $_REQUEST['userCaptchaCode'];
		/* Cryptage saisie en MD5 +  comparaison avec session */
		if( md5($userCaptchaCode) != $_SESSION['sysCaptchaCode'] )
		{
			$ok = false;
			$txt_err="Le code de sécurité saisi est erronné.";
			$alert = "Le code de sécurité saisi est erronné.1";
			$code_err = 1;
		}
		else
		{
			// l'adresse email existe dans la base ?
			$sql_utilisateurs="SELECT * FROM $table_user WHERE email like :email";
			$res_utilisateurs = $connexion->prepare($sql_utilisateurs);
			$res_utilisateurs->execute(array(':email'=>strtolower($login))) or die ("requete ligne 39 : ".$sql_utilisateurs);
			$tab_utilisateur=$res_utilisateurs->fetchAll();
			//email déjà existant
			if(count($tab_utilisateur)>0)
			{
				$ok = false;
				$alert = "Cette adresse email est déjà utilisée pour un compte.";
				$txt_erreur="Cette adresse email est déjà utilisée pour un compte.<br/> Veuillez choisir une autre adresse email.";
				$code_err = 2;
			}
			else
			{
				if($_REQUEST["forum"]==1||$pseudo!="")
				{
					if($_REQUEST["forum"]==1&&$pseudo=="")
						$pseudo_ok = false;
					else{
						// le pseudo existe dans la base ?
						$sql_pseudo="SELECT * FROM $table_user WHERE pseudo like :p";
						$res_pseudo = $connexion->prepare($sql_pseudo);
						$res_pseudo->execute(array(':p'=>strtolower($pseudo))) or die ("requete ligne 39 : ".$sql_pseudo);
						$tab_pseudo=$res_pseudo->fetchAll();
						if(count($tab_pseudo)>0)
							$pseudo_ok = false;
						else
							$pseudo_ok = true;
					}
				}
				else{
					$pseud_ok = true;
					$pseudo = "";
				}
				if($pseudo_ok)
				{
					//on teste la bonne existance de la ville
					$sql_ville="SELECT * FROM villes WHERE nom_ville_maj= :nom_ville_maj AND code_postal=:code_postal";
					$res_ville = $connexion->prepare($sql_ville);
					$res_ville->execute(array(':nom_ville_maj'=>$ville,':code_postal'=>$cp)) or die ("requete ligne 39 : ".$sql_ville);
					$tab_ville=$res_ville->fetchAll();
					//si problème de saisie
					if(count($tab_ville)==0)
					{
						$ok = false;
						$alert = "Un problème est survenu lors de l'enregistrement de votre commune.";
						$txt_erreur="Un problème est survenu lors de l'enregistrement de votre commune.<br/> Veuillez recommencer.";
						$code_err = 3;
					}
					else
					{
						//recuperation de l'id de la ville
						$id_ville=$tab_ville[0]['id'];

						if ($code_desinscription_nl)
						{
							// suppression de la table newsletter si inscription depuis le lien de la lettre d'info
							$sql_del = "DELETE FROM `newsletter`
										WHERE code_desinscription_nl=:code 
											AND email=:email
										LIMIT 1";
							$delNews = $connexion->prepare($sql_del);
							$delNews->execute(array(':code'=>$code_desinscription_nl,':email'=>$login)) or die ("Erreur ".__LINE__." : ".$sql_del);
							$no = $connexion->lastInsertId();
							$code_alea = $code_desinscription_nl; // on récupère le même code de desinscription à la lettre d'info
						}
						else
						{
							// On créé le code de desinscription à la lettre d'info
							$code_alea = id_aleatoire();
						}

						// insertion dans la base envoi d'email admin + email inscrit
						$sql_utilisateurs = "INSERT INTO $table_user (email, no_ville, mot_de_passe, etat, verification_email, id_connexion, newsletter, code_desinscription_nl, pseudo) VALUES (:email, :no_ville, :mot_de_passe, :etat, :verification_email, :id_connexion, :inscrit_newsletter, :code_desinscription_nl, :p)";
						$insert = $connexion->prepare($sql_utilisateurs);
						$insert->execute(array(':email'=>$login, ':no_ville'=>$id_ville, ':mot_de_passe'=>$mdpbdd, ':etat'=>1, ':verification_email'=>0, ':id_connexion'=>id_aleatoire(), ':inscrit_newsletter'=>1, ':code_desinscription_nl'=>$code_alea, ':p'=>$pseudo)) or die ("Erreur ".__LINE__." : ".$sql_utilisateurs);
						$no = $connexion->lastInsertId();

						//envoi de l'email d'inscription
						$sujet = "ensembleici.fr - Creation d'un espace personnel";
						$mail_exp = $email_admin;
						$message = "Bonjour,<br>
									Merci d'avoir cr&eacute;&eacute; votre espace personnel.
									<br/><br/>
									Nous vous rappelons vos informations de connexion :<br>
									&nbsp;&nbsp;&nbsp;- votre login : <b>".$login."</b><br>
									&nbsp;&nbsp;&nbsp;- votre mot de passe : <b>".$mot_de_passe."</b><br><br>
									L'espace personnel vous permet de faciliter votre navigation sur le site « Ensemble ici », garantit un tri des informations en fonction de votre lieu dhabitation, sécurise vos annonces et facilite leur gestion.<br/><br/>
									Salutations, <br/><br/><br/>";
						$message = $emails_header.$message.$emails_footer;
						$boundary = "-----=" . md5( uniqid ( rand() ) );
						$headers = "From: $mail_exp \n"; 
						$headers .="Reply-To:".$mail_exp.""."\n"; 
						$headers .= "X-Mailer: PHP/".phpversion()."\n";
						$headers .= "MIME-Version: 1.0\n";
						$headers .= "Content-Type: text/html; charset='Iso-8859-1'; boundary=\"$boundary\"";
						$headers .='Content-Transfer-Encoding: quoted-printable';
						$mail=strtolower($login);
						$destinataire = $dest;
						mail($mail,$sujet,$message,$headers);
					
					
						//On triche pour envoyer les paramètres à l'include
						$after_inscription = 1;
						include "./connexion.php";
					}
				}
				else
				{
					$ok = false;
					$txt_err="Le pseudo est déjà utilisé.";
					$alert = "Le pseudo est déjà utilisé.";
					$code_err = 4;
				}
			}
		}
	}
	else
	{
		//captcha vide normalement impossible....
		$ok = false;
		$txt_err="Le code de sécurité saisi est erronné.";
		$alert = "Le code de sécurité saisi est erronné.2";
		$code_err = 1;
	}
}
else
{
	//captcha vide normalement impossible....
	$ok = false;
	$txt_err="Le code de sécurité saisi est erronné.";
	$alert = "Le code de sécurité saisi est erronné.3";
	$code_err = 1;
}
$reponse = array("ok"=>$ok,"alert"=>$alert,"txt_erreur"=>$txt_err,"code_erreur"=>$code_err);
echo json_encode($reponse);
?>
