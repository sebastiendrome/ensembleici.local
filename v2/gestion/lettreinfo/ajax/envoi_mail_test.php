<?php
if(isset($_POST["no"])&&$_POST["no"]!=null&&$_POST["no"]!=0){
    require_once('../../../01_include/_connect.php');
    require_once('../../../01_include/_var_ensemble.php');
    require_once 'config_pear.php';
//	include "vider_liste.php";
	//On cr�ait une file d'attente
	$file_denvoi_de_mail =& new Mail_Queue($db_options, $mail_options);

	//Dans un premier temps on vide la liste
	while ($mail = $file_denvoi_de_mail->get()){
		$result = $file_denvoi_de_mail->deleteMail($mail->getId());
	}
	//Dans un second temps, on récupère le texte correspondant à no_lettre
	$requete = "SELECT lettreinfo.objet AS o, lettreinfo_envoi.contenu_html AS c FROM lettreinfo_envoi JOIN lettreinfo ON lettreinfo.no_envoi=lettreinfo_envoi.no WHERE lettreinfo.no=:no";
	$res = $connexion->prepare($requete);
	$res->execute(array(":no"=>$_POST["no"]));
	$tab = $res->fetchAll();
	// echo $tab[0]["c"];
	$contenu_HTML = $tab[0]["c"];
	$objet = $tab[0]["o"];
	$no_ville_test = 9558;
	$regex_replace = "/(\[\*\*idv\*\*\])/i";
	$regex_replace_codoff = "/(\[\*\*codoff\*\*\])/i";
	$regex_replace_typoff = "/(\[\*\*typoff\*\*\])/i";
	$contenu_HTML = preg_replace($regex_replace, $no_ville_test, $contenu_HTML);
	$contenu_HTML = preg_replace($regex_replace_codoff, 0, $contenu_HTML);
	$contenu_HTML = preg_replace($regex_replace_typoff, 0, $contenu_HTML);
	//Enfin, on remplie la liste avec : la liste complete, ou l'adresse en param�tre pour le test
	if(isset($_POST["a"])&&$_POST["a"]!=null&&$_POST["a"]!=""){ //On remplie la file d'attente pour le test
		$from = 'Ensemble Ici <newsletter@ensembleici.fr>';
		$destinataire = urldecode($_POST["a"]);
		$message = "<html><head></head><body>".$contenu_HTML."</body></html>";

		$entetes = array( 'From'    => $from,
					   'To'      => $destinataire,
					   'Subject' => "[TEST] ".$objet,
					   'X-Sender' => '<www.ensembleici.fr>',
//					   'X-auth-smtp-user' => 'newsletter@ensembleici.fr',
                                            'X-auth-smtp-user' => 'contact@envolinfo.com',
					   'X-Priority' => 3,
					   'X-Unsubscribe-Web' => '<http://www.ensembleici.fr/desinscription.html?codoff=0&typoff=0',
					   'X-Unsubscribe-Email' => '<mailto:unsubscribe@ensembleici.fr>',
					   'X-Mailer' => 'PHP/'.phpversion(),
					   'Content-Type' => 'text/html; charset=utf-8',
					   'Return-path' => "-f".$from);
		//On créait un message valide
		$mime =& new Mail_mime();
		$mime->setHTMLBody($message);
		$corps = $mime->get();
		$entetes = $mime->headers($entetes,true);
		//On place le message dans la file d'attente
		$put = $file_denvoi_de_mail->put( $from, $destinataire, $entetes, $corps );
		
		//On envoi le message
		$file_denvoi_de_mail->sendMailsInQueue();

		$reponse = array(false, utf8_encode("message envoyé ! "));
	}
	else{
		$reponse = array(false, utf8_encode("une erreur est survenue, veuiillez réessayer ! "));
	}
}
else{
	$reponse = array(false, utf8_encode("une erreur est survenue, veuiillez réessayer ! "));
}
echo json_encode($reponse);
?>