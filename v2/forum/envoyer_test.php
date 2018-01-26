<?php
header('Content-Type: text/html; charset=UTF-8');
/*************************************************************************************************
Fichier d'exemple de la fonction envoi_mail.
il faut renseigner les variables $OBJET, $CONTENU_MAIL_HTML, $CONTENU_MAIL_TXT, $MAIL_DESTINATAIRE
Une fois que ces variables sont renseignées, il n'y a plus qu'a faire l'include du fihcier envoi_mail.php
Il se charge des envois.
si $MAIL_DESTINATAIRE est affecté dans une boucle, il est possible de faire plusieurs fois l'envoi
Le fichier envoi mail.php doit être inclu pour chaque envoi.
$CONTENU_MAIL_HTML et $CONTENU_MAIL_TXT ne sont pas obligatoires tout les deux.
Mais il est préférable de faire les deux version pour que tous les clients puisse avoir une version
accessible du mail.
**************************************************************************************************/
	$URL_INCLUDE = "php/"; //Url à laquelle se situent les fichiers envoi_mail.php et include_smtp.php
	$ENVOI_MAIL = $URL_INCLUDE."envoi_mail.php"; //variable à inclure lors de chaque envois de mails.
	
	$OBJET = "[TEST] Ceci est l'objet de test";
	$CONTENU_MAIL_HTML = "Ceci est le mail d&apos;exemple<br/><b>Ensemble-ici</b>";
	$CONTENU_MAIL_TXT = "Ceci est le mail d'exemple\r\nEnsemble-ici";
	
	/*
		Exemple 1 : envoi simple
		*/
		$MAIL_DESTINATAIRE = "ruben@africultures.com";
		include $ENVOI_MAIL;
		echo "mail envoyé à ".$MAIL_DESTINATAIRE."<br/>";
	/*
		Exemple 2 : envoi multiples
		*/
		$les_destinataires = array("maxime@africultures.com","ruben@africultures.com","samuel@africultures.com");
		for($i=0;$i<count($les_destinataires);$i++){
			$MAIL_DESTINATAIRE = $les_destinataires[$i];
			include $ENVOI_MAIL;
			echo "mail envoyé à ".$MAIL_DESTINATAIRE."<br/>";
		}
?>