<?php
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";
//1. On vérifie les inputs (non vides pour email, email_verification, mdp, no_ville, captcha)
if(!empty($_POST["input_email"])&&!empty($_POST["input_email_verification"])&&!empty($_POST["input_mdp"])&&!empty($_POST["input_no_ville"])&&!empty($_POST["input_captcha"])){
//if(!empty($_POST["input_email"])&&!empty($_POST["input_email_verification"])&&!empty($_POST["input_mdp"])&&!empty($_POST["input_no_ville"])){
	$_POST["input_email"] = urldecode($_POST["input_email"]);
	$_POST["input_email_verification"] = urldecode($_POST["input_email_verification"]);
	$_POST["input_no_ville"] = urldecode($_POST["input_no_ville"]);
	$_POST["input_mdp"] = urldecode($_POST["input_mdp"]);
	$_POST["input_captcha"] = md5(urldecode($_POST["input_captcha"]));
	if(filter_var($_POST["input_email"], FILTER_VALIDATE_EMAIL)){//2. On vérifie que email est bien un email
		//3. On vérifie que le compte n'existe pas déjà
		$requete_utilisateur = "SELECT no FROM utilisateur WHERE email=:e";
		$tab_utilisateur = execute_requete($requete_utilisateur,array(":e"=>$_POST["input_email"]));
		if(empty($tab_utilisateur)){
			if($_POST["input_email"]==$_POST["input_email_verification"]){//4. On vérifie que les emails sont bien identiques
				if($_POST["input_captcha"]==$_SESSION["sysCaptchaCode"]){//5. On vérifie que le captcha soit correct
					$_SESSION["sysCaptchaCode"] = ""; //Si on vide pas, ça sert à rien
					//6. On vérifie que le pseudo n'existe pas déjà
					if(!empty($_POST["input_pseudo"])){
						$requete_pseudo = "SELECT no FROM utilisateur WHERE pseudo=:p";
						$tab_pseudo = execute_requete($requete_pseudo,array(":p"=>$_POST["input_pseudo"]));
					}
					else
						$tab_pseudo = array();
					if(empty($tab_pseudo)){
						//7. On regarde alors si l'adresse existe dans la table newsletter
						$requete_newsletter = "SELECT no,etat FROM newsletter WHERE email=:e";
						$tab_newsletter = execute_requete($requete_newsletter,array(":e"=>$_POST["input_email"]));
						if(!empty($tab_newsletter)){//7.1 Si c'est le cas, on supprime l'entrée
							$requete_delete = "DELETE FROM newsletter WHERE no=:no";
							execute_requete($requete_delete,array(":no"=>$tab_newsletter[0]["no"]));
							$etat_newsletter = $tab_newsletter[0]["etat"];
						}
						else
							$etat_newsletter = 1;
						$code_desinscription_nl = id_aleatoire();
						//8. On insère l'utilisateur
						$requete_insert = "INSERT INTO utilisateur(email,no_ville,mot_de_passe,verification_email,etat,newsletter,code_desinscription_nl,date_inscription,pseudo)
															VALUES(:e,:no_ville,:mdp,0,1,:nl,:c,CURRENT_TIMESTAMP,:p)";
						$param_insert = array(	":e"=>$_POST["input_email"],
												":no_ville"=>$_POST["input_no_ville"],
												":mdp"=>md5($_POST["input_email"].$_POST["input_mdp"].$cle_cryptage),
												":nl"=>$etat_newsletter,
												":c"=>$code_desinscription_nl,
												":p"=>$_POST["input_pseudo"]);
						$NO_UTILISATEUR = execute_requete($requete_insert,$param_insert);
						//9. on lui envoi le mail de vérification
							//On récupère les header et footer html
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
								curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
								$HEADER_MAIL_HTML = curl_exec($ch);
								curl_close($ch);
								//On récupère les header et footer html
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
								curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
								$FOOTER_MAIL_HTML = curl_exec($ch);
								curl_close($ch);
							$MAIL_EXPEDITEUR = $email_admin;
							$MAIL_DESTINATAIRE = $_POST["input_email"];
							$OBJET = "ensembleici.fr - Creation d'un espace personnel";
							//$CONTENU_MAIL_HTML = "Bonjour,<br />Merci d'avoir cr&eacute;&eacute; votre espace personnel.<br /><br />Nous vous rappelons vos informations de connexion :<br />&nbsp;&nbsp;&nbsp;- votre login : <b>".$_POST["input_email"]."</b><br />&nbsp;&nbsp;&nbsp;- votre mot de passe : <b>".$_POST["input_mdp"]."</b><br /><br />L’espace personnel vous permet de faciliter votre navigation sur le site « Ensemble ici », garantit un tri des informations en fonction de votre lieu d’habitation, sécurise vos annonces et facilite leur gestion.<br /><br />Salutations,<br />";
                                                        $CONTENU_MAIL_HTML = "Bonjour,<br />Merci d'avoir cr&eacute;&eacute; votre espace personnel.<br /><br />Nous vous rappelons votre identifiant de connexion : <b>".$_POST["input_email"]."</b><br /><br />L’espace personnel vous permet de faciliter votre navigation sur le site « Ensemble ici », garantit un tri des informations en fonction de votre lieu d’habitation, sécurise vos annonces et facilite leur gestion.<br /><br />A bientot sur <a href='http://www.ensembleici.fr/'>Ensemble Ici</a><br />";
							$CONTENU_MAIL_HTML = $HEADER_MAIL_HTML.$CONTENU_MAIL_HTML.$FOOTER_MAIL_HTML;
							//$CONTENU_MAIL_TXT = "Bonjour,\r\nMerci d'avoir créé votre espace personnel.\r\n\r\nNous vous rappelons vos informations de connexion :\r\n   - votre login : ".$_POST["input_email"]."\r\n   - votre mot de passe : ".$_POST["input_mdp"]."\r\n\r\nL’espace personnel vous permet de faciliter votre navigation sur le site « Ensemble ici », garantit un tri des informations en fonction de votre lieu d’habitation, sécurise vos annonces et facilite leur gestion.\r\n\r\nSalutations,\r\n";
                                                        $CONTENU_MAIL_TXT = "Bonjour,\r\nMerci d'avoir créé votre espace personnel.\r\n\r\nNous vous rappelons votre identifiant de connexion : ".$_POST["input_email"]."\r\n\r\nL’espace personnel vous permet de faciliter votre navigation sur le site « Ensemble ici », garantit un tri des informations en fonction de votre lieu d’habitation, sécurise vos annonces et facilite leur gestion.\r\n\r\nA bientôt sur Ensemble Ici\r\n";
							$UNSUSCRIBE_LINK = "";
							include "../01_include/envoyer_un_mail.php";
							
						//10. On regarde si le forum citoyen existe pour la ville sélectionnée par l'utilisateur.
						$requete_forumCitoyen = "SELECT no FROM forum WHERE no_ville=:no AND no_forum_type=1";
						$tab_forumCitoyen = execute_requete($requete_forumCitoyen,array(":no"=>$_POST["input_no_ville"]));
						if(empty($tab_forumCitoyen)||empty($tab_forumCitoyen[0]["no"])){ //10.1 S'il n'existe pas, on le créé.
							$insert_forumCitoyen = "INSERT INTO forum(no_ville,no_forum_type,date_creation) VALUES(:no,1,:d)";
							$NO_FORUM_CITOYEN = execute_requete($insert_forumCitoyen,array(":no"=>$_POST["input_no_ville"],":d"=>date("Y-m-d H:i:s")));
						}
						else
							$NO_FORUM_CITOYEN = $tab_forumCitoyen[0]["no"];
						
						//11. On regarde si l'utilisateur a décidé de s'inscrire au forum citoyen
						//if(!empty($_POST["forum_citoyen"])&&$_POST["forum_citoyen"]==1){
							$insert_notification = "INSERT INTO forum_notification(no_forum,no_utilisateur,etat,date_creation) VALUES(:no,:no_utilisateur,1,:date)";
							execute_requete($insert_notification,array(":no"=>$NO_FORUM_CITOYEN,":no_utilisateur"=>$NO_UTILISATEUR,":date"=>date("Y-m-d H:i:s")));
						//}
							
						//12. On connecte l'utilisateur
						connexion($_POST["input_email"],$_POST["input_mdp"]);
//						$return = array(true,"Vous êtes maintenant inscrit et connecté en tant qu'utilisateur.<br />Un mail viens d'être envoyé à l'adresse : ".$_POST["input_email"].".<br />Pensez à cliquer sur le lien qu'il contient afin de nous confirmer qu'il s'agit bien de votre adresse mail.");
                                                $return = array(true,"Vous êtes maintenant inscrit et connecté en tant qu'utilisateur.<br />Un mail de confirmation vient d'être envoyé à l'adresse : ".$_POST["input_email"].".<br />Nous vous souhaitons bonne navigation sur notre site.");
					}
					else{
						$return = array(false,"Ce nom d'utilisateur est déjà utilisé.");
					}
				}
				else{
					$return = array(false,"Le code de sécurité n'est pas valide.");
				}
			}
			else{
				$return = array(false,"Les adresse mail ne sont pas identiques.");
			}
		}
		else{
			$return = array(false,"Un compte existe déjà pour cette adresse.");
		}
	}
	else{
		$return = array(false,"L'adresse mail n'est pas correcte.");
	}
}
else{
	$return = array(false,"Veuillez saisir tous les champs.<br />Seul le nom d'utilisateur est facultatif.");
}
echo json_encode($return);
?>
