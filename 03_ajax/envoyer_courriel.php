<?php
/***
Ce fichier permet d'envoyer un courriel à un contact grace aux paramètres :
	textarea_contenu_mail, input_no_contact, input_email_expediteur, input_captcha
**/
header('Content-Type: text/plain; charset=UTF-8');
//1. Initialisation de la session
include "../01_include/_session_start.php";
//2. Récupération des variables principales et indispensables
include "../01_include/_var_ensemble.php";
//3. Récupération des fonctions utiles ou indispensables
include "../01_include/_fonctions.php";
//4. Initialisation des variables de la page et de l'utilisateur en fonction des gets, cookies et sessions
include "../01_include/_init_var.php";

$_POST["input_contact_libelle"] = urldecode($_POST["input_contact_libelle"]);
//1. On récupère le contact en s'assurant qu'il possède bien une adresse mail
if(!empty($_POST["input_no_contact"])){
	//D'une pierre deux (même trois) coups, l'utilisateur ayant renseigné son nom, on peut le mettre en pseudo s'il ne l'avait pas déjà fait, et le lier à son contact
	if(est_connecte()&&empty($_SESSION["utilisateur"]["pseudo"])&&!empty($_POST["input_contact_libelle"])){ //Utilisateur connecté, et sans pseudo (et là a renseigné son nom)
		//On regarde donc si l'on peut mettre le pseudo à l'utilisateur (unique)
		$requete_pseudo = "SELECT pseudo FROM utilisateur WHERE pseudo=:pseudo";
		$tab_pseudo = execute_requete($requete_pseudo,array(":pseudo"=>$_POST["input_contact_libelle"]));
		if(empty($tab_pseudo)){ //Ce pseudo est libre, on l'insère
			$requete_update_pseudo = "UPDATE utilisateur SET pseudo=:pseudo WHERE no=:no";
			$modif_utilisateur = execute_requete($requete_update_pseudo,array(":pseudo"=>$_POST["input_contact_libelle"],":no"=>$_SESSION["utilisateur"]["no"]));
			if(!empty($modif_utilisateur))
				$_SESSION["utilisateur"]["pseudo"] = $_POST["input_contact_libelle"];
		}
		//On regarde si le contact utilisateur est nommé
		$requete_contact_utilisateur = "SELECT IFNULL(contact.nom,'') AS nom, contact.no FROM contact JOIN utilisateur ON utilisateur.no_contact=contact.no WHERE utilisateur.no=:no";
		$tab_contact_utilisateur = execute_requete($requete_contact_utilisateur,array(":no"=>$_SESSION["utilisateur"]["no"]));
		if(empty($tab_contact_utilisateur[0]["nom"])){
			$requete_update_contact_utilisateur = "UPDATE contact SET nom=:nom WHERE no=:no";
			$modif_contact = execute_requete($requete_update_contact_utilisateur,array(":nom"=>$_POST["input_contact_libelle"],":no"=>$tab_contact_utilisateur[0]["no"]));
		}
	}
	
	//On s'occupe maintenant de l'envoi du courriel
	$requete_contact = "SELECT contact_contactType.valeur AS email FROM contact_contactType JOIN contact ON contact.no=contact_contactType.no_contact WHERE contact.no=:no AND contact_contactType.no_contactType=2";
	$tab_contact = execute_requete($requete_contact,array(":no"=>$_POST["input_no_contact"]));
	if(!empty($tab_contact))
		$MAIL_DESTINATAIRE = $tab_contact[0]["email"];
	else
		$MAIL_DESTINATAIRE = "";
	//$MAIL_DESTINATAIRE = "brozzu.samuel@gmail.com";
	//$MAIL_DESTINATAIRE = "samuel@africultures.com";
}
else
	$MAIL_DESTINATAIRE = "";
if(filter_var($MAIL_DESTINATAIRE, FILTER_VALIDATE_EMAIL)){
	if(!empty($_POST["textarea_contenu_mail"])&&!empty($_POST["input_email_expediteur"])&&!empty($_POST["input_captcha"])&&!empty($_POST["input_contact_libelle"])){//2. on vérifie que les paramètres ne sont pas vides.
		$CONTENU_MAIL_HTML = str_replace("\r","",str_replace("\n","",nl2br($_POST["textarea_contenu_mail"])));
		$CONTENU_MAIL_TXT = $_POST["textarea_contenu_mail"];
		$_POST["input_email_expediteur"] = urldecode($_POST["input_email_expediteur"]);
		$_POST["input_captcha"] = md5(urldecode($_POST["input_captcha"]));
		$_POST["input_email_expediteur"] = urldecode($_POST["input_email_expediteur"]);
		if(filter_var($_POST["input_email_expediteur"], FILTER_VALIDATE_EMAIL)){//3. On vérifie que l'adresse mail est valide
			if($_POST["input_captcha"]==$_SESSION["sysCaptchaCode"]){//4. On vérifie le captcha
				$_SESSION["sysCaptchaCode"] = ""; //Si on vide pas, ça sert à rien
				//5. On peut envoyer le message
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $root_site."01_include/template_mail_header.php");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					$HEADER_MAIL_HTML = curl_exec($ch);
					curl_close($ch);
					//On récupère les header et footer html
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $root_site."01_include/template_mail_footer.php");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					$FOOTER_MAIL_HTML = curl_exec($ch);
					curl_close($ch);
				$MAIL_EXPEDITEUR = str_replace("-"," ",url_rewrite($_POST["input_contact_libelle"]))." <".$_POST["input_email_expediteur"].">";
				$OBJET = "ensembleici.fr - nouveau message de ".$_POST["input_contact_libelle"];
				$CONTENU_MAIL_HTML = $HEADER_MAIL_HTML.$CONTENU_MAIL_HTML.'<p class="signature" style="text-align:left;font-style:italic;color:rgb(227, 214, 199);border-top: 1px solid rgb(227, 214, 199); margin: 1em 5em 0em 1em;padding:1em;">'.$_POST["input_contact_libelle"].'</p>'.$FOOTER_MAIL_HTML;
				$UNSUSCRIBE_LINK = "";
				include "../01_include/envoyer_un_mail.php";
				$return = array(true,"Votre message a bien été envoyé.");
			}
			else{
				$return = array(false,"Le code de sécurité n'est pas valide.");
			}
		}
		else{
			$return = array(false,'Vous devez saisir une adresse mail valide');
		}
	}
	else{
		$return = array(false,'Vous devez remplir tous les champs');
	}
}
else{
	$return = array(false,'Une erreur est survenue');
}
echo json_encode($return);
?>
