<?php
if(isset($_POST["no"])&&$_POST["no"]!=null&&$_POST["no"]!=0){
	include "config_pear.php";
	//Dans un premier temps on vide la liste
	
	//Dans un second temps, on r�cup�re le texte correspondant � no_lettre
	
	//Enfin, on remplie la liste avec : la liste compl�te, ou l'adresse en param�tre pour le test
	if(isset($_POST["a"])&&$_POST["a"]!=null&&$_POST["a"]!=""){ //On remplie la file d'attente pour le test
		//On cr�ait une file d'attente
		$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);

		//On cr�ait l'en-t�te
		$from = 'newsletter@africultures.info';
		$destinataire = urldecode($_POST["a"]);
		$message = 'Hi! This is test message!! :)';

		$entetes = array( 'From'    => $from,
		   'To'      => $destinataire,
		   'Subject' => "Message de test"  );

		//On cr�ait un message valide
		$mime =& new Mail_mime();
		$mime->setTXTBody($message);
		$corps = $mime->get();
		$entetes = $mime->headers($entetes);

		//On place le message dans la file d'attente
		$file_denvoi_de_mail->put( $from, $destinataire, $entetes, $corps );
		$reponse = true;
	}
	else{ //On remplie la file d'attente pour toute la liste des abonn�s.
		//On regarde le nombre d'abonn�s � ins�rer depuis utilisateurs
		
		//On regarde le nombre d'abonn�s � ins�rer depuis newsletter
		$reponse = false;
	}
}
else{
	$reponse = array(false, utf8_encode("une erreur est survenue, veuiillez r�essayer ! "));
}
echo json_encode($reponse);
?>