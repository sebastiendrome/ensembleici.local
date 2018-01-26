<?php
$url_delete = "";
if($update){
	if(!$commentaire){
		$BODY_HTML = "<center>Bonjour administrateur,<br /><b>".$pseudo."</b> vient de modifier un message dans : <a style=\"text-decoration:none;font-weight:400;color:rgb(21,170,158);text-align:center;font-size:20px;margin-top:18px;display:block;\" href=\"".$url_forum."\">".$titre_sujet."</a>";
		$BODY_HTML .= "<p style=\"background-color:rgb(240, 237, 234);border:1px solid rgb(227, 214, 199);border-radius:5px;padding:10px;margin:10px;font-size:16px;\">".$contenu_message."</p><br/>";
		$BODY_HTML .= "Pour y r&eacute;pondre, cliquez <a href=\"".$url_message."\">ici</a><br />";
		$BODY_HTML .= "Pour le supprimer, cliquez <a href=\"".$url_delete."\">ici</a></center>";
	
		$HEADER_TXT = "ensembleici.fr\r\n";
		$BODY_TXT = "Bonjour administrateur,\r\n".$pseudo." vient de modifier un message dans \" ".$titre_sujet." \" \r\n";
		$BODY_TXT = "<< ".$contenu_message." >>\r\n";
		$BODY_TXT .= "Pour le lire, copiez/collez cette adresse dans votre navigateur : ".$url_message."\r\n";
		$BODY_TXT .= "Pour le supprimer, copiez/collez cette adresse dans votre navigateur : ".$url_delete."\r\n";
		$FOOTER_TXT = "";
	
		$OBJET = "nouveau message : ".$titre_sujet;
	}
	else{
		$BODY_HTML = "<center>Bonjour administrateur,<br /><b>".$pseudo."</b> vient de modifier un commentaire dans : <a style=\"text-decoration:none;font-weight:bold;color:rgb(21,170,158);text-align:center;font-size:20px;margin-top:18px;display:block;\" href=\"".$url_forum."\">".$titre_sujet."</a>";
		$BODY_HTML .= "<br/>";
		$BODY_HTML .= "Pour le lire, cliquez <a href=\"".$url_message."\">ici</a><br />";
		$BODY_HTML .= "Pour le supprimer, cliquez <a href=\"".$url_delete."\">ici</a></center>";
	
		$HEADER_TXT = "ensembleici.fr\r\n";
		$BODY_TXT = "Bonjour administrateur,\r\n".$pseudo." vient de modifier un commentaire dans \" ".$titre_sujet." \" \r\n";
		$BODY_TXT .= "Pour le lire ou y répondre, copiez/collez cette adresse dans votre navigateur : ".$url_message."\r\n";
		$BODY_TXT .= "Pour le supprimer, copiez/collez cette adresse dans votre navigateur : ".$url_delete."\r\n";
		$FOOTER_TXT = "";
	
		$OBJET = "nouveau commentaire : ".$titre_sujet;
	}
}
else{
	if(!$commentaire){
		$BODY_HTML = "<center>Bonjour administrateur,<br /><b>".$pseudo."</b> vient d'&eacute;crire un nouveau message dans : <a style=\"text-decoration:none;font-weight:400;color:rgb(21,170,158);text-align:center;font-size:20px;margin-top:18px;display:block;\" href=\"".$url_forum."\">".$titre_sujet."</a>";
		$BODY_HTML .= "<p style=\"background-color:rgb(240, 237, 234);border:1px solid rgb(227, 214, 199);border-radius:5px;padding:10px;margin:10px;font-size:16px;\">".$contenu_message."</p><br/>";
		$BODY_HTML .= "Pour y r&eacute;pondre, cliquez <a href=\"".$url_message."\">ici</a><br />";
		$BODY_HTML .= "Pour le supprimer, cliquez <a href=\"".$url_delete."\">ici</a></center>";
	
		$HEADER_TXT = "ensembleici.fr\r\n";
		$BODY_TXT = "Bonjour administrateur,\r\n".$pseudo." vient d'écrire un nouveau message dans \" ".$titre_sujet." \" \r\n";
		$BODY_TXT = "<< ".$contenu_message." >>\r\n";
		$BODY_TXT .= "Pour le lire, copiez/collez cette adresse dans votre navigateur : ".$url_message."\r\n";
		$BODY_TXT .= "Pour le supprimer, copiez/collez cette adresse dans votre navigateur : ".$url_delete."\r\n";
		$FOOTER_TXT = "";
	
		$OBJET = "nouveau message : ".$titre_sujet;
	}
	else{
		$BODY_HTML = "<center>Bonjour administrateur,<br /><b>".$pseudo."</b> vient de commenter un message dans : <a style=\"text-decoration:none;font-weight:bold;color:rgb(21,170,158);text-align:center;font-size:20px;margin-top:18px;display:block;\" href=\"".$url_forum."\">".$titre_sujet."</a>";
		$BODY_HTML .= "<br/>";
		$BODY_HTML .= "Pour le lire, cliquez <a href=\"".$url_message."\">ici</a><br />";
		$BODY_HTML .= "Pour le supprimer, cliquez <a href=\"".$url_delete."\">ici</a></center>";
	
		$HEADER_TXT = "ensembleici.fr\r\n";
		$BODY_TXT = "Bonjour administrateur,\r\n".$pseudo." vient de commenter un message dans \" ".$titre_sujet." \" \r\n";
		$BODY_TXT .= "Pour le lire ou y répondre, copiez/collez cette adresse dans votre navigateur : ".$url_message."\r\n";
		$BODY_TXT .= "Pour le supprimer, copiez/collez cette adresse dans votre navigateur : ".$url_delete."\r\n";
		$FOOTER_TXT = "";
	
		$OBJET = "nouveau commentaire : ".$titre_sujet;
	}
}
$OBJET = formater_objet($OBJET);
$CONTENU_MAIL_HTML = $HEADER_HTML.$BODY_HTML.$FOOTER_HTML;
$CONTENU_MAIL_TXT = $HEADER_TXT.$BODY_TXT.$FOOTER_TXT;
$MAIL_DESTINATAIRE = $email_forum;
include "envoyer_un_mail.php";
?>
