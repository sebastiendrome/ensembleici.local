<?php
//On récupère les header et footer html
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_header.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$HEADER_HTML = curl_exec($ch);
curl_close($ch);
//On récupère les header et footer html
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ensembleici.fr/01_include/template_mail_footer.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
$FOOTER_HTML = curl_exec($ch);
curl_close($ch);

function formater_objet($o){
	$o = html_entity_decode($o);
	$o = strip_tags($o);

	// $o = preg_replace("#\s([a-z])(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
	// $o = preg_replace("#\s([a-z])\s(\’|\')\s([a-z][a-z]+)#i", ' $1 $2', $o);
	// $o = preg_replace("#\s([a-z])\s(\’|\')([a-z][a-z]+)#i", ' $1 $2', $o);
	// $o = preg_replace("#\s([a-z])\s([a-z][a-z]+)#i", ' $1 $2', $o);
	$o = str_replace("'"," ",$o);
	$o = str_replace('"',"",$o);
	
// Pour les accents (a, e, i, o, u)
	$o = preg_replace('#[ãàâä]#i', 'a', $o); //(&a[a-z]{3,6};)
	$o = preg_replace('#[éèëê]#i', 'e', $o); //
	$o = preg_replace('#[õòöô]#i', 'o', $o); //(&o[a-z]{3,6};)
	$o = preg_replace('#[ìîî]#i', 'i', $o); //(&i[a-z]{3,6};)
	$o = preg_replace('#[ùûü]#i', 'u', $o); //(&u[a-z]{3,6};)
	$o = preg_replace('#ç#i', 'c', $o); //(&ccedil;)
	
	$o = trim($o);
	return $o;
}


$url_message = "http://www.ensembleici.fr/forum/".$adresse_redirec;
if($messagemessage==null){ //Nouveau message dans la ville.
	$OBJET = formater_objet("Ensemble-ici - forum citoyen : ".$nom_ville." - ".$titre_message);
	
	// $pseudo = utf8_decode($pseudo, $utf8);
	// $message = utf8_decode($message, $utf8);
	$CONTENU_MAIL_HTML = $HEADER_HTML;
	$CONTENU_MAIL_HTML .= "<center>Bonjour,<br />".$pseudo." vient d'&eacute;crire un nouveau message sur le forum citoyen de ".$nom_ville." : <br/><br/>";
	$CONTENU_MAIL_HTML .= "<b>\"".$titre_message."\"</b><br/><br/>";
	$CONTENU_MAIL_HTML .= "Pour le lire ou y r&eacute;pondre, cliquez ici : <a href=\"".$url_message."\">forum citoyen de ".$nom_ville."</a></center>";
	$CONTENU_MAIL_HTML .= "<br/><br/><div style=\"font-size: 11px; font-style: italic; \">Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajout&eacute;s sur ce forum, merci de cliquer ici: <a href=\"http://www.ensembleici.fr/forum_desinscription_fil_discussion.php?no_ville=".$no_sujet."&no_utilisateur=".$no_utilisateur."&messagemessage=".$messagemessage."&nom_ville=".$nom_ville."\">ici</a></div></center>";
	$CONTENU_MAIL_HTML .= $FOOTER_HTML;
	
	$CONTENU_MAIL_TXT = "Bonjour,\r\n".$pseudo." vient d'écrire un nouveau message sur le forum citoyen de ".$nom_ville."\r\n";
	$CONTENU_MAIL_TXT .= $titre_message."\r\n";
	$CONTENU_MAIL_TXT .= "Pour le lire ou y répondre : ".$url_message;
	$CONTENU_MAIL_TXT .= "Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajoutés sur ce forum, http://www.ensembleici.fr/forum_desinscription_fil_discussion.php?no_ville=".$no_sujet."&no_utilisateur=".$no_utilisateur."&messagemessage=".$messagemessage."";
	
	$info_admin_html = "<br/>&nbsp;<br/>Informations suppl&eacute;mentaires : <br/>email: <a href=\"mailto:\"".$_SESSION['UserConnecte_email']."\">".$_SESSION['UserConnecte_email']."</a><br/>numéro du message: ".$no_message_insert;
	$info_admin_txt = "\r\n\r\nInformations supplémentaires : \r\nemail: ".$_SESSION['UserConnecte_email']."\r\nnuméro du message: ".$no_message_insert;
	
	$requete_abonne = "SELECT utilisateur.email AS mail,utilisateur.no AS no FROM utilisateur JOIN message_utilisateur ON message_utilisateur.no_utilisateur=utilisateur.no WHERE message_utilisateur.no_sujet=:nos AND message_utilisateur.no_message=0 AND message_utilisateur.no_utilisateur<>:nou AND message_utilisateur.inscrit=1";
	$params_abonne = array(":nos"=>$no_sujet,":nou"=>$no_utilisateur);
}
else{ //Réponse à un message.
	$OBJET = formater_objet("Ensemble-ici - forum citoyen : ".$nom_ville." - ".$titre_message);

	$CONTENU_MAIL_HTML = $HEADER_HTML;
	$CONTENU_MAIL_HTML .= "<center>Bonjour,<br />".$pseudo." vient de r&eacute;pondre au message <b>".$titre_message."</b> dans le forum citoyen de ".$nom_ville."<br/>";
	$CONTENU_MAIL_HTML .= "Pour le lire ou y r&eacute;pondre, <a href=\"".$url_message."\">forum citoyen de ".$nom_ville."</a></center>";
	$CONTENU_MAIL_HTML .= "</br><div style=\"font-size: 10px; font-style: italic; \">Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajout&eacute;s sur ce forum, merci de cliquer <a href=\"http://www.ensembleici.fr/forum_desinscription_fil_discussion.php?no_ville=".$no_sujet."&no_utilisateur=".$no_utilisateur."&messagemessage=".$messagemessage."&nom_ville=".$nom_ville."\">ici</a></div></center>";
	$CONTENU_MAIL_HTML .= $FOOTER_HTML;
	
	$CONTENU_MAIL_TXT = "Bonjour,\r\n".$pseudo." vient de répondre au message \"".$titre_message."\" dans le forum citoyen de ".$nom_ville."\r\n";
	$CONTENU_MAIL_TXT .= "Pour le lire ou y répondre : ".$url_message;	
	$CONTENU_MAIL_TXT .= "Si vous souhaitez ne plus recevoir de mail pour vous avertir des nouveaux messages ajoutés sur ce forum, http://www.ensembleici.fr/forum_desinscription_fil_discussion.php?no_ville=".$no_sujet."&no_utilisateur=".$no_utilisateur."&messagemessage=".$messagemessage."&nom_ville=".$nom_ville."";
	
	$info_admin_html = "<br/>&nbsp;<br/>Informations suppl&eacute;mentaires : <br/>email: <a href=\"mailto:\"".$_SESSION['UserConnecte_email']."\">".$_SESSION['UserConnecte_email']."</a><br/>numéro du message: ".$no_message_insert;
	$info_admin_txt = "\r\n\r\nInformations supplémentaires : \r\nemail: ".$_SESSION['UserConnecte_email']."\r\nnuméro du message: ".$no_message_insert;
	
	
	$requete_abonne = "SELECT utilisateur.email AS mail,utilisateur.no AS no FROM utilisateur JOIN message_utilisateur ON message_utilisateur.no_utilisateur=utilisateur.no WHERE message_utilisateur.no_sujet=:nos AND message_utilisateur.no_message=:nom AND message_utilisateur.no_utilisateur<>:nou AND message_utilisateur.inscrit=1";
	$params_abonne = array(":nos"=>$no_sujet,":nou"=>$no_utilisateur,":nom"=>$messagemessage);
}
//On récupère la liste des emails (on epargne celui qui vient d'écrire)
$res_abonne = $connexion->prepare($requete_abonne);
$res_abonne->execute($params_abonne);
$tab_abonne = $res_abonne->fetchAll();

$UNSUSCRIBE_LINK = $url_message;
print_r($tab_abonne);

for($indice_abonne=0;$indice_abonne<count($tab_abonne);$indice_abonne++){
	$MAIL_DESTINATAIRE = $tab_abonne[$indice_abonne]["mail"];
	//Pour chaque abonné, on fait appèle à la fonction mail.
	include("envoyer_un_mail.php");
}

$CONTENU_MAIL_TXT .= $info_admin_txt;
$CONTENU_MAIL_HTML .= $info_admin_html;

$tab_admin = array("forum@ensembleici.fr","olivier@africultures.com","rubendelomio@hotmail.com");
$MAIL_DESTINATAIRE = "";
for($indice_admin=0;$indice_admin<count($tab_admin);$indice_admin++){
	if($MAIL_DESTINATAIRE!="")
		$MAIL_DESTINATAIRE .= ",".$tab_admin[$indice_admin];
	else
		$MAIL_DESTINATAIRE = $tab_admin[$indice_admin];
}
//on envoie le mail à tt les admins
include("envoyer_un_mail.php");
?>