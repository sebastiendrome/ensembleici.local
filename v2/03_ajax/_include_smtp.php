<?php
//set_include_path(get_include_path() . ":/home/ensemble/www/gestion/lettreinfo/PEAR");
set_include_path(get_include_path() . ":/home/ensemble/gestion/lettreinfo/PEAR");
	require_once "Mail/mime.php";
	require_once 'Mail.php';

function envoi_email($email_expediteur, $emails_destinataires, $sujet_mail, $message_html="", $message_texte="",$unsuscribe_link="", $emails_destinatairesCc="", $emails_destinatairesBcc="", $return_path="-fforum@ensembleici.fr"){
	$headers= array();
	$headers['From'] = $email_expediteur;
	if($emails_destinatairesCc!="")
		$headers['Cc'] = $emails_destinatairesCc;
	if($emails_destinatairesBcc!="")
		$headers['Bcc'] = $emails_destinatairesBcc;
	$headers['Subject'] = $sujet_mail;
	$headers['X-Mailer'] = "PHP/" . phpversion();
	$headers['X-Sender'] = "<www.ensembleici.fr>";
	$headers['X-auth-smtp-user'] = "<forum@ensembleici.fr>";
	$headers['X-Priority'] = 3;
	// $headers['X-Unsubscribe-Web'] = "<".$unsuscribe_link."http://www.ensembleici.fr/forum/?nav=self&sr=desinscription-lettre>";
	$headers['X-Unsubscribe-Web'] = "<http://www.ensembleici.fr/forum/?id_ville=".$no_sujet."&nom_ville=".$nom_ville."#inscription_message".$messagemessage.">";
	// $headers['X-Unsubscribe-Web'] = "<http://www.spla.pro/self>";
	$headers['X-Unsubscribe-Email'] = "<mailto:forum@ensembleici.fr>";
	$headers['Return-Path'] = $return_path;
	
	/*$headers['text_encoding'] = '7bit';
	$headers['text_charset'] = 'ISO-8859-1';
	$headers['html_charset'] = 'ISO-8859-1';
	$headers['head_charset'] = 'ISO-8859-1';*/
	
	$mime = &new Mail_Mime(array('eol' => "\n"));
	$mime->setTXTBody($message_texte);
	$mime->setHTMLBody($message_html);
	
	$mime_params = array(
	  'text_encoding' => '7bit',
	  'text_charset'  => 'UTF-8',
	  'html_charset'  => 'UTF-8',
	  'head_charset'  => 'UTF-8'
	);
	
	$message = $mime->get($mime_params);	
	$headers = $mime->headers($headers,true);	
	
	// $mail =& Mail::factory('mail');
	// $mail->send($emails_destinataires, $headers, $message);
	
	$headers = $mime->txtHeaders($headers);
	mail($emails_destinataires, $sujet_mail, $message, $headers, $return_path);
	
	
	
	// $mail =& Mail::factory('mail');
	// $mail->send($to, $headers, $mbody);
	/*
	// $boundary = "-----=" . md5( uniqid ( rand() ) ); 
	$limite = "_----------=_parties_".md5(uniqid (rand()));
	$message="";
	$headers="";
	// if($message_html<>""){
		// $message=$message_html;
		// $headers = "MIME-version: 1.0\n";
		// $headers .= "Content-type: text/html; charset=iso-8859-1\n";
	// }
	// else{
		// $headers = 'MIME-Version: 1.0' . "\r\n";
		// $headers .= "Content-type: text; charset=iso-8859-1\r\n";
		// $message= $message_texte;
	// }
	$header .= "MIME-Version: 1.0\n";
	$header .= "Content-Type: multipart/alternative; boundary=\"".$limite."\"";
	$headers .= "X-Mailer: PHP/" . phpversion() . "\n" ;
	$headers .= "X-Sender: <www.africultures.info>\n";
	$headers .= "X-auth-smtp-user: newsletter@africultures.info \n";
	$headers .= "X-Priority: 3 \n";
	$headers .= "X-Unsubscribe-Web:<http://www.africultures.com/php/?nav=self&sr=desinscription-lettre>  \n";
	$headers .= "X-Unsubscribe-Email:<mailto:unsubscribe@africultures.com>  \n";
	
	// En-t�tes additionnels
	$headers .= 'From: ' . $email_expediteur . "\r\n";
	if ($emails_destinatairesCc<>""){
		$headers .= 'Cc: ' . $emails_destinatairesCc . "\r\n";
	}
	if ($emails_destinatairesBcc<>""){
		$headers .= 'Bcc: ' . $emails_destinatairesBcc . "\r\n";
	}
	
	$message .= "--".$limite."\n";
	$message .= "Content-Type: text/plain\n";
	$message .= "charset=\"iso-8859-1\"\n";
	$message .= "Content-Transfer-Encoding: 8bit\n\n";
	$message .= $message_texte;
	
	$message .= "\n\n--".$limite."\n";
	$message .= "Content-Type: text/html; ";
	$message .= "charset=\"iso-8859-1\"; ";
	$message .= "Content-Transfer-Encoding: 8bit;\n\n";
	$message .= $message_html;

	$message .= "\n--".$limite."--";
	*/
	// Envoi
	// return mail($emails_destinataires, $sujet_mail, $message, $headers, $return_path);
}

/*
$domaine_mail = "spla";
if($_SESSION["pays"]!=0&&$_SESSION["pays"]!=-1){ //Si un pays est en session, alors on r�cup�re son domaine.
	$rqt_dmn = "SELECT url AS u FROM acp_pays WHERE no=:no";
	$res_dmn = $connexion->prepare($rqt_dmn);
	$res_dmn->execute(array(":no"=>$_SESSION["pays"]));
	$tbl_dmn = $res_dmn->fetchAll();
	if(count($tbl_dmn)>0){
		if($tbl_dmn[0]["u"]!=""&&$tbl_dmn[0]["u"]!=null)
			$domaine_mail = $tbl_dmn[0]["u"];
	}
}

$MAIL_EXPEDITEUR = $domaine_mail." <newsletter@".$domaine_mail.".info>";*/
$MAIL_EXPEDITEUR = "ensemble-ici <forum@ensembleici.fr>";


// $from = $nom." <".$from.">";

// $limite = "_----------=_parties_".md5(uniqid (rand()));

// $header  = "Reply-to: ".$from."\n";
// $header .= "From: ".$from."\n";
// $header .= "X-Sender: <".$site.">\n";
// $header .= "X-Mailer: PHP\n";
// $header .= "X-auth-smtp-user: ".$from." \n";
// $header .= "X-abuse-contact: ".$from." \n";
// $header .= "Date: ".date("D, j M Y G:i:s O")."\n";
// $header .= "MIME-Version: 1.0\n";
// $header .= "Content-Type: multipart/alternative; boundary=\"".$limite."\"";

// $message = "";

// $message .= "--".$limite."\n";
// $message .= "Content-Type: text/plain\n";
// $message .= "charset=\"iso-8859-1\"\n";
// $message .= "Content-Transfer-Encoding: 8bit\n\n";
// $message .= $text;

// $message .= "\n\n--".$limite."\n";
// $message .= "Content-Type: text/html; ";
// $message .= "charset=\"iso-8859-1\"; ";
// $message .= "Content-Transfer-Encoding: 8bit;\n\n";
// $message .= $html;

// $message .= "\n--".$limite."--";
?>
