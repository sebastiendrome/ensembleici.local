<?php
if(!function_exists("envoi_email"))
		include "_include_smtp.php";
	envoi_email($MAIL_EXPEDITEUR, $MAIL_DESTINATAIRE, $OBJET, $CONTENU_MAIL_HTML, $CONTENU_MAIL_TXT,$UNSUSCRIBE_LINK);
?>