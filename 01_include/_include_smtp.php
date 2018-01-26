<?php
//set_include_path(get_include_path() . ":/home/ensemble/www/01_include/PEAR");
set_include_path(get_include_path() . ":/home/ensemble/01_include/PEAR");
require_once "Mail/mime.php";
require_once 'Mail.php';

function envoi_email($email_expediteur, $emails_destinataires, $sujet_mail, $message_html="", $message_texte="",$unsuscribe_link="", $emails_destinatairesCc="", $emails_destinatairesBcc="", $return_path=""){
	if(empty($return_path))
		$return_path = "-f".$email_expediteur;
	$headers= array();
	$headers['From'] = $email_expediteur;
	if($emails_destinatairesCc!="")
		$headers['Cc'] = $emails_destinatairesCc;
	if($emails_destinatairesBcc!="")
		$headers['Bcc'] = $emails_destinatairesBcc;
	//$headers['Subject'] = $sujet_mail;
	$headers['X-Mailer'] = "PHP/" . phpversion();
	$headers['X-Sender'] = "<www.ensembleici.fr>";
	$headers['X-auth-smtp-user'] = "<".$email_expediteur.">";
	$headers['X-Priority'] = 3;
	// $headers['X-Unsubscribe-Web'] = "<".$unsuscribe_link."http://www.ensembleici.fr/forum/?nav=self&sr=desinscription-lettre>";
	$headers['X-Unsubscribe-Web'] = $unsuscribe_link;
	// $headers['X-Unsubscribe-Web'] = "<http://www.spla.pro/self>";
	$headers['X-Unsubscribe-Email'] = "<mailto:".$email_expediteur.">";
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
	
	$headers = $mime->txtHeaders($headers);
	mail($emails_destinataires, $sujet_mail, $message, $headers, $return_path);
}
if(empty($MAIL_EXPEDITEUR))
	$MAIL_EXPEDITEUR = "ensemble-ici <contact@ensembleici.fr>";
//Unsubscribe forum:
	//"<http://www.ensembleici.fr/forum/?id_ville=".$no_sujet."&nom_ville=".$nom_ville."#inscription_message".$messagemessage.">";
?>
