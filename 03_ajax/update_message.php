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
//1. On vérifie les paramètres
if(!empty($_POST["c"])&&!empty($_POST["no"])){
	$_POST["c"] = urldecode($_POST["c"]);
	//2. On vérifie la connexion
	if(est_connecte()){
		$requete_update = "UPDATE message SET contenu=:c WHERE no=:no";
		execute_requete($requete_update,array(":c"=>$_POST["c"],":no"=>$_POST["no"]));
		
		/*
		if($_POST["p"]=="editorial")
			$table = "editorial";
		else if($_POST["p"]=="agenda"||$_POST["p"]=="evenement")
			$table = "evenement";
		else if($_POST["p"]=="structure")
			$table = "structure";
		else if($_POST["p"]=="petiteannonce"||$_POST["p"]=="petite-annonce")
			$table = "petiteannonce";
		else
			$table = "forum";
		
		$OBJET = "<b>".$_SESSION["utilisateur"]["pseudo"]."</b> a modifié un message dans : <b>".$tab_fiche[0]["titre"]."</b>";
		//3. On envoie les notifications aux administrateurs
			$CONTENU_MAIL_HTML = '<p>'.$OBJET.'</p>';
			$CONTENU_MAIL_HTML .= '<p style="border:1px solid grey;">'.$_POST["contenu"].'</p>';
			$CONTENU_MAIL_HTML .= '<p>';
				$CONTENU_MAIL_HTML .= '<a href="'.$url_message.'">[s\'y rendre]</a> - <a href="'.$url_supprression.'">[supprimer]</a>';
			$CONTENU_MAIL_HTML .= '</p>';
			$CONTENU_MAIL_HTML = $HEADER_HTML.$CONTENU_MAIL_HTML.$FOOTER_HTML;
			
			$OBJET = strip_tags($OBJET);
			
			$CONTENU_MAIL_TXT = $OBJET;
			$CONTENU_MAIL_TXT .= '\r\n\r\n'.strip_tags($_POST["contenu"]);
			$CONTENU_MAIL_TXT .= '\r\n\r\n';
			$CONTENU_MAIL_TXT .= 'S\'y rendre : '.$url_message.'\r\nSupprimer : '.$url_supprression;
			
			$OBJET = "[".$_POST["p"]."] [message] ".formater_objet($OBJET);
			
			$MAIL_DESTINATAIRE = "samuel@africultures.com";*/
			
			include "../01_include/envoyer_un_mail.php";
			
			
		$return = array(true,"Modifications apportées avec succés.");
	}
	else
		$return = array(false,"Vous devez être connecté pour poster un message");
}
else
	$return = array(false,"Vous ne pouvez pas poster un message vide");
echo json_encode($return);
?>
